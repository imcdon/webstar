<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

/**
 * Session cart with optional SQLite persistence for abandoned-cart recovery.
 *
 * @phpstan-type CartLine array{variation_id:string, qty:int}
 */
final class Cart
{
    private const SESSION_KEY = 'webstar_shop_cart';
    private const CART_ID_KEY = 'webstar_shop_cart_id';

    public function __construct(
        private readonly Config $config,
        private readonly Catalog $catalog,
        private readonly PDO $db,
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
    }

    public static function make(Config $config): self
    {
        return new self($config, Catalog::make($config), Db::pdo());
    }

    /** @return list<CartLine> */
    public function lines(): array
    {
        $lines = $_SESSION[self::SESSION_KEY] ?? [];
        return is_array($lines) ? array_values($lines) : [];
    }

    public function count(): int
    {
        $this->pruneInvalid();
        $n = 0;
        foreach ($this->lines() as $line) {
            $n += (int) ($line['qty'] ?? 0);
        }
        return $n;
    }

    /**
     * Drop cart lines whose variation no longer exists (e.g. after catalog reseed).
     * @return int number of lines removed
     */
    public function pruneInvalid(): int
    {
        $lines = $this->lines();
        $kept = [];
        $removed = 0;
        foreach ($lines as $line) {
            $vid = (string) ($line['variation_id'] ?? '');
            if ($vid !== '' && $this->catalog->getVariation($vid)) {
                $kept[] = $line;
            } else {
                $removed++;
            }
        }
        if ($removed > 0) {
            $this->saveLines($kept);
        }
        return $removed;
    }

    public function add(string $variationId, int $qty = 1): void
    {
        $qty = max(1, $qty);
        $variation = $this->catalog->getVariation($variationId);
        if (!$variation) {
            throw new \InvalidArgumentException('Variation not found.');
        }
        if ((int) $variation['track_inventory'] === 1 && (int) $variation['quantity'] < $qty) {
            // allow add up to available; callers may still add and we clamp on checkout
        }

        $lines = $this->lines();
        $found = false;
        foreach ($lines as &$line) {
            if ($line['variation_id'] === $variationId) {
                $line['qty'] = (int) $line['qty'] + $qty;
                if ((int) $variation['track_inventory'] === 1) {
                    $line['qty'] = min($line['qty'], max(0, (int) $variation['quantity']));
                }
                $found = true;
                break;
            }
        }
        unset($line);
        if (!$found) {
            $lines[] = ['variation_id' => $variationId, 'qty' => $qty];
        }
        $this->saveLines($lines);
    }

    public function update(string $variationId, int $qty): void
    {
        $lines = $this->lines();
        $out = [];
        foreach ($lines as $line) {
            if ($line['variation_id'] !== $variationId) {
                $out[] = $line;
                continue;
            }
            if ($qty > 0) {
                $out[] = ['variation_id' => $variationId, 'qty' => $qty];
            }
        }
        $this->saveLines($out);
    }

    public function remove(string $variationId): void
    {
        $this->update($variationId, 0);
    }

    public function clear(): void
    {
        $this->saveLines([]);
    }

    public function setShippingMethod(string $method): void
    {
        $_SESSION['webstar_shop_shipping'] = in_array($method, ['flat', 'pickup'], true) ? $method : 'flat';
        $this->persistDb();
    }

    public function shippingMethod(): string
    {
        return (string) ($_SESSION['webstar_shop_shipping'] ?? 'flat');
    }

    public function setEmail(?string $email): void
    {
        $_SESSION['webstar_shop_email'] = $email ? strtolower(trim($email)) : null;
        $this->persistDb();
    }

    public function email(): ?string
    {
        $e = $_SESSION['webstar_shop_email'] ?? null;
        return is_string($e) && $e !== '' ? $e : null;
    }

    /**
     * @return array{
     *   lines: list<array>,
     *   subtotal_cents: int,
     *   shipping_cents: int,
     *   shipping_method: string,
     *   shipping_label: string,
     *   total_cents: int,
     *   errors: list<string>
     * }
     */
    public function detailed(): array
    {
        $detailed = [];
        $subtotal = 0;
        $errors = [];
        $kept = [];
        $pruned = 0;

        foreach ($this->lines() as $line) {
            $variation = $this->catalog->getVariation($line['variation_id']);
            if (!$variation) {
                $pruned++;
                continue;
            }
            $qty = (int) $line['qty'];
            $available = (int) $variation['quantity'];
            if ((int) $variation['track_inventory'] === 1 && $available < $qty) {
                if ($available <= 0) {
                    $errors[] = $variation['product_name'] . ' is sold out.';
                    $pruned++;
                    continue;
                }
                $qty = $available;
                $errors[] = $variation['product_name'] . ' quantity reduced to available stock.';
            }
            $kept[] = ['variation_id' => $variation['id'], 'qty' => $qty];
            $lineTotal = $qty * (int) $variation['price_cents'];
            $subtotal += $lineTotal;
            $detailed[] = [
                'variation_id' => $variation['id'],
                'square_id' => $variation['square_id'],
                'product_id' => $variation['product_id'],
                'product_name' => $variation['product_name'],
                'product_slug' => $variation['product_slug'],
                'variation_name' => $variation['name'],
                'qty' => $qty,
                'unit_price_cents' => (int) $variation['price_cents'],
                'line_total_cents' => $lineTotal,
                'image_url' => $variation['image_url'] ?? null,
                'local_image' => $variation['local_image'] ?? null,
                'quantity_available' => $available,
            ];
        }

        // Persist clamps / drop missing lines so a catalog reseed doesn't brick checkout.
        if ($kept !== $this->lines()) {
            $this->saveLines($kept);
        }
        if ($pruned > 0 && $detailed === []) {
            $errors[] = 'Your cart items are no longer available (catalog was updated). Please add them again from the shop.';
        } elseif ($pruned > 0) {
            array_unshift(
                $errors,
                'Some items were removed from your cart because they are no longer available. Please add them again if needed.'
            );
        }

        $method = $this->shippingMethod();
        if ($method === 'pickup' && $this->config->pickupEnabled) {
            $shipping = 0;
            $label = $this->config->pickupLabel;
        } else {
            $method = 'flat';
            if ($this->config->shippingFreeOverCents > 0 && $subtotal >= $this->config->shippingFreeOverCents) {
                $shipping = 0;
                $label = 'Free shipping';
            } else {
                $shipping = $this->config->shippingFlatCents;
                $label = 'USPS Ground Advantage';
            }
        }

        return [
            'lines' => $detailed,
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'shipping_method' => $method,
            'shipping_label' => $label,
            'total_cents' => $subtotal + $shipping,
            'errors' => $errors,
        ];
    }

    public function cartId(): string
    {
        if (!empty($_SESSION[self::CART_ID_KEY])) {
            return (string) $_SESSION[self::CART_ID_KEY];
        }
        $id = Db::newId('cart_');
        $_SESSION[self::CART_ID_KEY] = $id;
        $this->persistDb();
        return $id;
    }

    public function restoreToken(): string
    {
        $id = $this->cartId();
        $stmt = $this->db->prepare('SELECT restore_token FROM carts WHERE id = ?');
        $stmt->execute([$id]);
        $token = $stmt->fetchColumn();
        if (is_string($token) && $token !== '') {
            return $token;
        }
        $token = bin2hex(random_bytes(16));
        $this->db->prepare('UPDATE carts SET restore_token = ? WHERE id = ?')->execute([$token, $id]);
        return $token;
    }

    public function restoreFromToken(string $token): bool
    {
        $stmt = $this->db->prepare('SELECT * FROM carts WHERE restore_token = ? AND status = \'open\' LIMIT 1');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        $items = json_decode((string) $row['items_json'], true);
        if (!is_array($items)) {
            return false;
        }
        $_SESSION[self::CART_ID_KEY] = $row['id'];
        $_SESSION[self::SESSION_KEY] = $items;
        $_SESSION['webstar_shop_shipping'] = $row['shipping_method'] ?: 'flat';
        if ($row['email']) {
            $_SESSION['webstar_shop_email'] = $row['email'];
        }
        return true;
    }

    public function markConverted(string $orderId): void
    {
        $this->db->prepare(
            'UPDATE carts SET status = \'converted\', converted_order_id = ?, updated_at = datetime(\'now\') WHERE id = ?'
        )->execute([$orderId, $this->cartId()]);
        $this->clear();
        unset($_SESSION[self::CART_ID_KEY]);
    }

    public function attachPaymentLink(string $linkId, string $url, ?string $squareOrderId = null): void
    {
        $this->persistDb();
        $this->db->prepare(
            'UPDATE carts SET payment_link_id = ?, payment_link_url = ?, square_order_id = ?, updated_at = datetime(\'now\') WHERE id = ?'
        )->execute([$linkId, $url, $squareOrderId, $this->cartId()]);
    }

    /** @param list<CartLine> $lines */
    private function saveLines(array $lines): void
    {
        $_SESSION[self::SESSION_KEY] = array_values($lines);
        $this->persistDb();
    }

    private function persistDb(): void
    {
        $id = $_SESSION[self::CART_ID_KEY] ?? null;
        if (!$id) {
            $id = Db::newId('cart_');
            $_SESSION[self::CART_ID_KEY] = $id;
        }
        $sessionId = session_id() ?: null;
        $items = json_encode($this->lines(), JSON_UNESCAPED_SLASHES);
        $stmt = $this->db->prepare(
            'INSERT INTO carts (id, session_id, email, items_json, shipping_method, status, updated_at)
             VALUES (?,?,?,?,?,\'open\',datetime(\'now\'))
             ON CONFLICT(id) DO UPDATE SET
               session_id = excluded.session_id,
               email = COALESCE(excluded.email, carts.email),
               items_json = excluded.items_json,
               shipping_method = excluded.shipping_method,
               updated_at = excluded.updated_at'
        );
        $stmt->execute([
            $id,
            $sessionId,
            $this->email(),
            $items,
            $this->shippingMethod(),
        ]);
    }
}

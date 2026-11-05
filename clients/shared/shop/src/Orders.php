<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class Orders
{
    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo());
    }

    /**
     * @param array{lines:list<array>,subtotal_cents?:int,shipping_cents:int,shipping_method:string,shipping_label:string,total_cents?:int} $cart
     */
    public function createFromDemoCart(
        array $cart,
        string $orderId,
        string $paymentId,
        string $orderNumber,
        ?string $email = null,
    ): array {
        $email = $email ?: 'demo@example.com';
        $subtotal = (int) ($cart['subtotal_cents'] ?? array_sum(array_column($cart['lines'], 'line_total_cents')));
        $shipping = (int) ($cart['shipping_cents'] ?? 0);
        $total = (int) ($cart['total_cents'] ?? ($subtotal + $shipping));

        $this->db->prepare(
            'INSERT INTO orders (id, square_order_id, square_payment_id, order_number, email, status,
              subtotal_cents, shipping_cents, tax_cents, total_cents, shipping_method, shipping_name)
             VALUES (?,?,?,?,?,\'completed\',?,?,0,?,?,?)'
        )->execute([
            $orderId,
            $orderId,
            $paymentId,
            $orderNumber,
            strtolower($email),
            $subtotal,
            $shipping,
            $total,
            $cart['shipping_method'] ?? 'flat',
            $cart['shipping_label'] ?? null,
        ]);

        $itemStmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, variation_id, square_variation_id, name, variation_name, quantity, unit_price_cents, line_total_cents)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $stockStmt = $this->db->prepare(
            'UPDATE variations SET quantity = MAX(0, quantity - ?) WHERE id = ?'
        );

        foreach ($cart['lines'] as $line) {
            $itemStmt->execute([
                $orderId,
                $line['product_id'] ?? null,
                $line['variation_id'] ?? null,
                $line['square_id'] ?? null,
                $line['product_name'] ?? 'Item',
                $line['variation_name'] ?? null,
                (int) $line['qty'],
                (int) $line['unit_price_cents'],
                (int) $line['line_total_cents'],
            ]);
            if (!empty($line['variation_id'])) {
                $stockStmt->execute([(int) $line['qty'], $line['variation_id']]);
            }
        }

        return $this->getById($orderId) ?? [];
    }

    /**
     * Persist a local order immediately after a successful Web Payments charge.
     *
     * @param \Square\Types\Order $squareOrder
     * @param \Square\Types\Payment|null $payment
     * @param array{email?:string,phone?:string,name?:string,shipping_method?:string,address?:array}|null $form
     */
    public function createFromSquareOrder(
        $squareOrder,
        $payment,
        ?string $cartId = null,
        ?string $accountId = null,
        ?array $form = null,
    ): array {
        $squareOrderId = (string) $squareOrder->getId();
        $existing = $this->getBySquareOrderId($squareOrderId);
        if ($existing) {
            return $existing;
        }

        $orderId = Db::newId('ord_');
        $orderNumber = 'DJ' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $email = strtolower((string) (
            $form['email']
            ?? $payment?->getBuyerEmailAddress()
            ?? 'customer@example.com'
        ));

        $tax = (int) ($squareOrder->getTotalTaxMoney()?->getAmount() ?? 0);
        $discount = (int) ($squareOrder->getTotalDiscountMoney()?->getAmount() ?? 0);
        $shipping = (int) ($squareOrder->getTotalServiceChargeMoney()?->getAmount() ?? 0);
        $total = (int) ($squareOrder->getTotalMoney()?->getAmount() ?? 0);
        $subtotal = max(0, $total - $tax - $shipping + $discount);

        $shippingMethod = (string) ($form['shipping_method'] ?? 'flat');
        $shippingName = (string) ($form['name'] ?? '');
        $addressJson = !empty($form['address']) ? json_encode($form['address']) : null;

        $this->db->prepare(
            'INSERT INTO orders (id, square_order_id, square_payment_id, order_number, cart_id, account_id,
              email, phone, status, subtotal_cents, shipping_cents, tax_cents, discount_cents, total_cents,
              shipping_method, shipping_name, shipping_address_json, buyer_json, raw_json)
             VALUES (?,?,?,?,?,?,?,?,\'completed\',?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $orderId,
            $squareOrderId,
            $payment?->getId(),
            $orderNumber,
            $cartId,
            $accountId,
            $email,
            $form['phone'] ?? null,
            $subtotal,
            $shipping,
            $tax,
            $discount,
            $total,
            $shippingMethod,
            $shippingName !== '' ? $shippingName : null,
            $addressJson,
            json_encode([
                'email' => $email,
                'name' => $form['name'] ?? null,
                'phone' => $form['phone'] ?? null,
            ]),
            $squareOrder->toJson(),
        ]);

        $itemStmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, variation_id, square_variation_id, name, variation_name, quantity, unit_price_cents, line_total_cents)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $findVar = $this->db->prepare('SELECT id, product_id FROM variations WHERE square_id = ? LIMIT 1');
        $stockStmt = $this->db->prepare(
            'UPDATE variations SET quantity = MAX(0, quantity - ?) WHERE square_id = ?'
        );

        foreach ($squareOrder->getLineItems() ?? [] as $li) {
            $squareVarId = $li->getCatalogObjectId();
            $productId = null;
            $variationId = null;
            if ($squareVarId) {
                $findVar->execute([$squareVarId]);
                $row = $findVar->fetch();
                if ($row) {
                    $variationId = $row['id'];
                    $productId = $row['product_id'];
                }
                $stockStmt->execute([(int) $li->getQuantity(), $squareVarId]);
            }
            $qty = (int) $li->getQuantity();
            $unit = (int) ($li->getBasePriceMoney()?->getAmount() ?? 0);
            $lineTotal = (int) ($li->getTotalMoney()?->getAmount() ?? ($unit * $qty));
            $itemStmt->execute([
                $orderId,
                $productId,
                $variationId,
                $squareVarId,
                (string) ($li->getName() ?? 'Item'),
                $li->getVariationName(),
                $qty,
                $unit,
                $lineTotal,
            ]);
        }

        if ($cartId) {
            $this->db->prepare(
                "UPDATE carts SET status = 'converted', converted_order_id = ?, square_order_id = ?, updated_at = datetime('now') WHERE id = ?"
            )->execute([$orderId, $squareOrderId, $cartId]);
        }

        return $this->getById($orderId) ?? [];
    }

    /**
     * Upsert an order from a Square payment/order payload.
     *
     * @param array<string,mixed> $payload
     */
    public function upsertFromSquarePayment(array $payload): ?array
    {
        $payment = $payload['data']['object']['payment'] ?? $payload['payment'] ?? null;
        if (!is_array($payment)) {
            return null;
        }
        if (($payment['status'] ?? '') !== 'COMPLETED') {
            return null;
        }

        $squareOrderId = $payment['order_id'] ?? null;
        $paymentId = $payment['id'] ?? null;
        if (!$squareOrderId) {
            return null;
        }

        $existing = $this->getBySquareOrderId((string) $squareOrderId);
        if ($existing) {
            return $existing;
        }

        $orderData = null;
        if (!$this->config->demoMode) {
            try {
                $square = SquareClientFactory::make($this->config);
                $resp = $square->orders->get(new \Square\Orders\Requests\GetOrdersRequest([
                    'orderId' => (string) $squareOrderId,
                ]));
                $orderData = $resp->getOrder();
            } catch (\Throwable $e) {
                // fall through with payment-only data
            }
        }

        $orderId = Db::newId('ord_');
        $orderNumber = 'DJ' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $email = strtolower((string) ($payment['buyer_email_address'] ?? $payment['buyerEmailAddress'] ?? 'customer@example.com'));
        $amount = (int) ($payment['amount_money']['amount'] ?? 0);

        $this->db->prepare(
            'INSERT INTO orders (id, square_order_id, square_payment_id, order_number, email, status,
              total_cents, subtotal_cents, raw_json, buyer_json)
             VALUES (?,?,?,?,?,\'completed\',?,?,?,?)'
        )->execute([
            $orderId,
            $squareOrderId,
            $paymentId,
            $orderNumber,
            $email,
            $amount,
            $amount,
            json_encode($payload),
            json_encode($payment),
        ]);

        // Link cart if square_order_id matches
        $this->db->prepare(
            'UPDATE carts SET status = \'converted\', converted_order_id = ?, updated_at = datetime(\'now\')
             WHERE square_order_id = ? OR payment_link_id IN (
                SELECT payment_link_id FROM carts WHERE square_order_id = ?
             )'
        )->execute([$orderId, $squareOrderId, $squareOrderId]);

        return $this->getById($orderId);
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->itemsFor($id);
        return $order;
    }

    public function getBySquareOrderId(string $squareOrderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE square_order_id = ?');
        $stmt->execute([$squareOrderId]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->itemsFor($order['id']);
        return $order;
    }

    public function getByNumber(string $orderNumber): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_number = ? COLLATE NOCASE');
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->itemsFor($order['id']);
        return $order;
    }

    public function lookup(string $email, string $orderNumber): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE email = ? COLLATE NOCASE AND order_number = ? COLLATE NOCASE LIMIT 1'
        );
        $stmt->execute([strtolower(trim($email)), trim($orderNumber)]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->itemsFor($order['id']);
        return $order;
    }

    /** @return list<array> */
    public function forEmail(string $email): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE email = ? COLLATE NOCASE ORDER BY created_at DESC'
        );
        $stmt->execute([strtolower(trim($email))]);
        $orders = $stmt->fetchAll();
        foreach ($orders as &$order) {
            $order['items'] = $this->itemsFor($order['id']);
        }
        return $orders;
    }

    /** @return list<array> */
    public function recent(int $limit = 50): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function emailHasPurchasedProduct(string $email, string $productId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE o.email = ? COLLATE NOCASE AND oi.product_id = ? AND o.status = \'completed\'
             LIMIT 1'
        );
        $stmt->execute([strtolower(trim($email)), $productId]);
        return (bool) $stmt->fetchColumn();
    }

    /** @return list<array> */
    private function itemsFor(string $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}

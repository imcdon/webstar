<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class Wishlist
{
    private const COOKIE = 'webstar_wishlist';

    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo());
    }

    /** @return list<string> product ids */
    public function ids(): array
    {
        $raw = $_COOKIE[self::COOKIE] ?? '';
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
    }

    public function has(string $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function toggle(string $productId): bool
    {
        $ids = $this->ids();
        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_filter($ids, fn ($id) => $id !== $productId));
            $added = false;
        } else {
            $ids[] = $productId;
            $added = true;
        }
        $this->writeCookie($ids);
        return $added;
    }

    public function mergeToAccount(string $accountId): void
    {
        $ins = $this->db->prepare(
            'INSERT OR IGNORE INTO wishlists (account_id, product_id) VALUES (?,?)'
        );
        foreach ($this->ids() as $productId) {
            $ins->execute([$accountId, $productId]);
        }
        // load account wishlist into cookie
        $stmt = $this->db->prepare('SELECT product_id FROM wishlists WHERE account_id = ?');
        $stmt->execute([$accountId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $this->writeCookie(array_values(array_unique(array_map('strval', $ids))));
    }

    /** @return list<array> */
    public function products(Catalog $catalog): array
    {
        $out = [];
        foreach ($this->ids() as $id) {
            $p = $catalog->getById($id);
            if ($p) {
                $vars = $catalog->variationsFor($id);
                $p['min_price'] = $vars ? min(array_column($vars, 'price_cents')) : 0;
                $out[] = $p;
            }
        }
        return $out;
    }

    /** @param list<string> $ids */
    private function writeCookie(array $ids): void
    {
        $json = json_encode(array_values($ids));
        setcookie(self::COOKIE, (string) $json, [
            'expires' => time() + 60 * 60 * 24 * 365,
            'path' => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE] = (string) $json;
    }
}

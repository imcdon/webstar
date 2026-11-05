<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class Reviews
{
    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
        private readonly Orders $orders,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo(), Orders::make($config));
    }

    public function submit(
        string $productId,
        string $authorName,
        string $body,
        int $rating,
        ?string $email = null,
        ?string $accountId = null,
    ): int {
        $rating = max(1, min(5, $rating));
        $authorName = trim($authorName);
        $body = trim($body);
        if ($authorName === '' || $body === '') {
            throw new \InvalidArgumentException('Name and review text are required.');
        }

        $verified = 0;
        if ($email && $this->orders->emailHasPurchasedProduct($email, $productId)) {
            $verified = 1;
        }

        $this->db->prepare(
            'INSERT INTO reviews (product_id, account_id, author_name, author_email, rating, body, is_verified, is_approved)
             VALUES (?,?,?,?,?,?,?,0)'
        )->execute([$productId, $accountId, $authorName, $email ? strtolower($email) : null, $rating, $body, $verified]);

        return (int) $this->db->lastInsertId();
    }

    public function approve(int $id): void
    {
        $this->db->prepare('UPDATE reviews SET is_approved = 1 WHERE id = ?')->execute([$id]);
    }

    public function reject(int $id): void
    {
        $this->db->prepare('DELETE FROM reviews WHERE id = ?')->execute([$id]);
    }

    /** @return list<array> */
    public function pending(): array
    {
        return $this->db->query(
            'SELECT r.*, p.name AS product_name FROM reviews r
             JOIN products p ON p.id = r.product_id
             WHERE r.is_approved = 0 ORDER BY r.created_at DESC'
        )->fetchAll();
    }

    /** @return list<array> */
    public function latestApproved(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, p.name AS product_name, p.slug AS product_slug FROM reviews r
             JOIN products p ON p.id = r.product_id
             WHERE r.is_approved = 1 ORDER BY r.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

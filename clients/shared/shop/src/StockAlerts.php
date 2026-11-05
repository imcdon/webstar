<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class StockAlerts
{
    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
        private readonly Mailer $mailer,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo(), Mailer::make($config));
    }

    public function subscribe(string $variationId, string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email.');
        }
        $this->db->prepare(
            'INSERT INTO stock_alerts (variation_id, email) VALUES (?,?)
             ON CONFLICT(variation_id, email) DO UPDATE SET notified_at = NULL'
        )->execute([$variationId, $email]);
    }

    public function notifyBackInStock(): int
    {
        $rows = $this->db->query(
            'SELECT sa.id, sa.email, sa.variation_id, v.name AS variation_name, p.name AS product_name, p.slug
             FROM stock_alerts sa
             JOIN variations v ON v.id = sa.variation_id
             JOIN products p ON p.id = v.product_id
             WHERE sa.notified_at IS NULL AND v.quantity > 0'
        )->fetchAll();

        $mark = $this->db->prepare('UPDATE stock_alerts SET notified_at = datetime(\'now\') WHERE id = ?');
        $n = 0;
        foreach ($rows as $row) {
            $url = $this->config->absoluteUrl('product/?slug=' . urlencode($row['slug']));
            $label = $row['product_name'];
            if ($row['variation_name'] && $row['variation_name'] !== 'Default') {
                $label .= ' (' . $row['variation_name'] . ')';
            }
            $this->mailer->backInStock($row['email'], $label, $url);
            $mark->execute([$row['id']]);
            $n++;
        }
        return $n;
    }
}

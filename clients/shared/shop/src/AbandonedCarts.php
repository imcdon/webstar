<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class AbandonedCarts
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

    public function process(): array
    {
        $sent2 = $this->sendWave(
            hours: 2,
            column: 'reminder_2h_sent_at',
            olderThan: '-2 hours',
            newerThan: '-24 hours'
        );
        $sent24 = $this->sendWave(
            hours: 24,
            column: 'reminder_24h_sent_at',
            olderThan: '-24 hours',
            newerThan: '-7 days'
        );
        return ['sent_2h' => $sent2, 'sent_24h' => $sent24];
    }

    public function unsubscribe(string $token): void
    {
        $this->db->prepare(
            'UPDATE carts SET unsubscribed_at = datetime(\'now\') WHERE restore_token = ?'
        )->execute([$token]);
    }

    private function sendWave(int $hours, string $column, string $olderThan, string $newerThan): int
    {
        $sql = "SELECT * FROM carts
                WHERE status = 'open'
                  AND email IS NOT NULL AND email != ''
                  AND unsubscribed_at IS NULL
                  AND {$column} IS NULL
                  AND items_json != '[]'
                  AND updated_at <= datetime('now', ?)
                  AND updated_at >= datetime('now', ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$olderThan, $newerThan]);
        $rows = $stmt->fetchAll();

        $mark = $this->db->prepare("UPDATE carts SET {$column} = datetime('now') WHERE id = ?");
        $n = 0;
        foreach ($rows as $row) {
            $token = $row['restore_token'] ?: bin2hex(random_bytes(16));
            if (!$row['restore_token']) {
                $this->db->prepare('UPDATE carts SET restore_token = ? WHERE id = ?')->execute([$token, $row['id']]);
            }
            $url = $this->config->absoluteUrl('cart/?restore=' . urlencode($token));
            $this->mailer->abandonedCart($row['email'], $url, $hours);
            $mark->execute([$row['id']]);
            $n++;
        }
        return $n;
    }
}

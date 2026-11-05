<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;
use Square\Utils\WebhooksHelper;

final class Webhooks
{
    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
        private readonly Catalog $catalog,
        private readonly Orders $orders,
        private readonly Mailer $mailer,
        private readonly StockAlerts $stockAlerts,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self(
            $config,
            Db::pdo(),
            Catalog::make($config),
            Orders::make($config),
            Mailer::make($config),
            StockAlerts::make($config),
        );
    }

    public function verify(string $body, string $signatureHeader): bool
    {
        if ($this->config->demoMode) {
            return true;
        }
        if ($this->config->squareWebhookSignatureKey === '' || $this->config->squareWebhookSignatureKey === 'REPLACE_ME') {
            return false;
        }
        return WebhooksHelper::verifySignature(
            requestBody: $body,
            signatureHeader: $signatureHeader,
            signatureKey: $this->config->squareWebhookSignatureKey,
            notificationUrl: $this->config->squareWebhookNotificationUrl,
        );
    }

    /**
     * @return array{ok:bool,message:string}
     */
    public function handle(string $body): array
    {
        $payload = json_decode($body, true);
        if (!is_array($payload)) {
            return ['ok' => false, 'message' => 'Invalid JSON'];
        }

        $eventId = (string) ($payload['event_id'] ?? $payload['eventId'] ?? Db::newId('evt_'));
        $type = (string) ($payload['type'] ?? '');

        $exists = $this->db->prepare('SELECT processed_at FROM webhook_events WHERE id = ?');
        $exists->execute([$eventId]);
        $row = $exists->fetch();
        if ($row && $row['processed_at']) {
            return ['ok' => true, 'message' => 'Already processed'];
        }

        $this->db->prepare(
            'INSERT INTO webhook_events (id, event_type, merchant_id, payload_json)
             VALUES (?,?,?,?)
             ON CONFLICT(id) DO UPDATE SET payload_json = excluded.payload_json'
        )->execute([
            $eventId,
            $type,
            $payload['merchant_id'] ?? null,
            $body,
        ]);

        try {
            match (true) {
                $type === 'payment.updated' || $type === 'payment.created' => $this->onPayment($payload),
                str_starts_with($type, 'inventory.count') => $this->onInventory($payload),
                str_starts_with($type, 'catalog.version') => $this->onCatalog(),
                default => null,
            };
            $this->db->prepare(
                'UPDATE webhook_events SET processed_at = datetime(\'now\'), error = NULL WHERE id = ?'
            )->execute([$eventId]);
            return ['ok' => true, 'message' => 'Processed ' . $type];
        } catch (\Throwable $e) {
            $this->db->prepare(
                'UPDATE webhook_events SET error = ? WHERE id = ?'
            )->execute([$e->getMessage(), $eventId]);
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    private function onPayment(array $payload): void
    {
        // Persist a thin payment record so we can stop abandoned-cart emails and
        // mark verified buyers for reviews. Receipts, notifications, and all
        // fulfillment (pack, ship, pickup) stay in the Square Dashboard.
        $this->orders->upsertFromSquarePayment($payload);
    }

    private function onInventory(array $payload): void
    {
        $counts = $payload['data']['object']['inventory_counts']
            ?? $payload['data']['object']['inventoryCounts']
            ?? [];
        if (!is_array($counts)) {
            $counts = [];
        }

        // Always refresh from API when available
        try {
            $this->catalog->syncInventory();
        } catch (\Throwable $e) {
            // update from payload as fallback
            $stmt = $this->db->prepare('UPDATE variations SET quantity = ? WHERE square_id = ?');
            foreach ($counts as $count) {
                if (($count['state'] ?? '') !== 'IN_STOCK') {
                    continue;
                }
                $stmt->execute([
                    (int) ((float) ($count['quantity'] ?? 0)),
                    $count['catalog_object_id'] ?? $count['catalogObjectId'] ?? '',
                ]);
            }
        }

        $this->stockAlerts->notifyBackInStock();
    }

    private function onCatalog(): void
    {
        $this->catalog->syncFromSquare();
    }
}

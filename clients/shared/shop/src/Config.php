<?php

declare(strict_types=1);

namespace Webstar\Shop;

/**
 * Shop configuration bag. Built from the client shop-config.php array.
 */
final class Config
{
    public function __construct(
        public readonly string $siteName,
        public readonly string $siteUrl,
        public readonly string $siteEmail,
        public readonly string $ownerEmail,
        public readonly string $supportPhone,
        public readonly string $sqlitePath,
        public readonly string $squareToken,
        public readonly string $squareApplicationId,
        public readonly string $squareEnvironment,
        public readonly string $squareLocationId,
        public readonly string $squareWebhookSignatureKey,
        public readonly string $squareWebhookNotificationUrl,
        public readonly string $squareTaxName,
        public readonly string $giftCardPurchaseUrl,
        public readonly int $shippingFlatCents,
        public readonly int $shippingFreeOverCents,
        public readonly bool $pickupEnabled,
        public readonly string $pickupLabel,
        public readonly string $mailFrom,
        public readonly string $mailFromName,
        public readonly ?array $smtp,
        public readonly string $adminPasswordHash,
        public readonly int $lowStockThreshold,
        public readonly string $currency,
        public readonly string $timezone,
        public readonly bool $demoMode,
    ) {
    }

    /**
     * @param array<string, mixed> $cfg
     */
    public static function fromArray(array $cfg): self
    {
        $smtp = $cfg['smtp'] ?? null;
        if ($smtp !== null && !is_array($smtp)) {
            $smtp = null;
        }

        $token = (string) ($cfg['square_token'] ?? '');
        $demo = (bool) ($cfg['demo_mode'] ?? false);
        if ($token === '' || $token === 'REPLACE_ME') {
            $demo = true;
        }

        return new self(
            siteName: (string) ($cfg['site_name'] ?? 'Shop'),
            siteUrl: rtrim((string) ($cfg['site_url'] ?? ''), '/'),
            siteEmail: (string) ($cfg['site_email'] ?? ''),
            ownerEmail: (string) ($cfg['owner_email'] ?? ''),
            supportPhone: (string) ($cfg['support_phone'] ?? ''),
            sqlitePath: (string) ($cfg['sqlite_path'] ?? ''),
            squareToken: $token,
            squareApplicationId: (string) ($cfg['square_application_id'] ?? ''),
            squareEnvironment: (string) ($cfg['square_environment'] ?? 'sandbox'),
            squareLocationId: (string) ($cfg['square_location_id'] ?? ''),
            squareWebhookSignatureKey: (string) ($cfg['square_webhook_signature_key'] ?? ''),
            squareWebhookNotificationUrl: (string) ($cfg['square_webhook_notification_url'] ?? ''),
            squareTaxName: (string) ($cfg['square_tax_name'] ?? 'Michigan Sales Tax'),
            giftCardPurchaseUrl: (string) ($cfg['gift_card_purchase_url'] ?? ''),
            shippingFlatCents: (int) ($cfg['shipping_flat_cents'] ?? 599),
            shippingFreeOverCents: (int) ($cfg['shipping_free_over_cents'] ?? 7500),
            pickupEnabled: (bool) ($cfg['pickup_enabled'] ?? true),
            pickupLabel: (string) ($cfg['pickup_label'] ?? 'Local pickup'),
            mailFrom: (string) ($cfg['mail_from'] ?? 'noreply@example.com'),
            mailFromName: (string) ($cfg['mail_from_name'] ?? ($cfg['site_name'] ?? 'Shop')),
            smtp: $smtp,
            adminPasswordHash: (string) ($cfg['admin_password_hash'] ?? ''),
            lowStockThreshold: (int) ($cfg['low_stock_threshold'] ?? 5),
            currency: (string) ($cfg['currency'] ?? 'USD'),
            timezone: (string) ($cfg['timezone'] ?? 'America/Detroit'),
            demoMode: $demo,
        );
    }

    public function isSandbox(): bool
    {
        return $this->squareEnvironment !== 'production';
    }

    public function webPaymentsSdkUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.web.squarecdn.com/v1/square.js'
            : 'https://web.squarecdn.com/v1/square.js';
    }

    public function absoluteUrl(string $path = ''): string
    {
        $path = ltrim($path, '/');
        if ($path === '') {
            return $this->siteUrl . '/';
        }
        if (str_contains($path, '?') || str_contains($path, '.php') || str_contains($path, '.')) {
            return $this->siteUrl . '/' . $path;
        }
        return $this->siteUrl . '/' . rtrim($path, '/') . '/';
    }
}

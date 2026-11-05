<?php

declare(strict_types=1);

/**
 * Copy to shop-config.php and fill in Square sandbox (or production) credentials.
 * shop-config.php is gitignored — never commit real tokens.
 *
 * Square Developer Dashboard → Applications → Credentials:
 *   - Application ID → square_application_id (Web Payments SDK)
 *   - Access token → square_token
 *   - Sandbox vs Production → square_environment
 * Locations tab → square_location_id
 * Webhooks → signature key + notification URL
 *
 * With token left as REPLACE_ME, the shop runs in demo mode:
 *   - Catalog loads from seed/catalog.php via bin/seed-local.php
 *   - Checkout creates a local demo order (no Square charge)
 */

$root = dirname(__DIR__);

return [
    'site_name' => "DJ's Tackle",
    'site_url' => 'http://localhost/webstar-business-solutions/clients/djstackle',
    'site_email' => 'djstackle20@gmail.com',
    'owner_email' => 'djstackle20@gmail.com',
    'support_phone' => '(810) 627-9974',

    'sqlite_path' => $root . '/data/shop.sqlite',

    // Square
    // Credentials → Sandbox tab → Application ID (must start with sandbox-sq0idb- while environment=sandbox)
    'square_application_id' => 'REPLACE_ME',
    'square_token' => 'REPLACE_ME',
    'square_environment' => 'sandbox', // sandbox | production
    'square_location_id' => 'REPLACE_ME',
    'square_webhook_signature_key' => 'REPLACE_ME',
    'square_webhook_notification_url' => 'https://djstackle.com/square-webhook.php',
    'square_tax_name' => 'Michigan Sales Tax', // must match Catalog tax name (seed creates this)

    // Square eGift cards hosted purchase page (Dashboard → Gift Cards). Optional.
    'gift_card_purchase_url' => '',

    // Shipping (cents)
    'shipping_flat_cents' => 599,
    'shipping_free_over_cents' => 7500,
    'pickup_enabled' => true,
    'pickup_label' => 'Local pickup — Sterling Heights, MI',

    // Mail — leave smtp null to log emails to data/mail.log
    'mail_from' => 'noreply@djstackle.com',
    'mail_from_name' => "DJ's Tackle",
    'smtp' => null,
    // 'smtp' => [
    //     'host' => 'smtp.example.com',
    //     'port' => 587,
    //     'user' => '',
    //     'pass' => '',
    //     'secure' => 'tls',
    // ],

    // Admin password: change-me — generate with: php -r "echo password_hash('yourpass', PASSWORD_DEFAULT);"
    'admin_password_hash' => '$2y$10$CBElXXaApao1HDCAATr6f.kz2qhVezOI3uVg1pewEFN08e/xR7lBq',

    'low_stock_threshold' => 5,
    'currency' => 'USD',
    'timezone' => 'America/Detroit',

    // Forced demo even with a token (optional)
    'demo_mode' => false,
];

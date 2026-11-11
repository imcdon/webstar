<?php

declare(strict_types=1);

/**
 * Report what's ready vs blocked for live cutover on djstackle.com.
 * Usage: php bin/production-ready-check.php
 */

$config = require dirname(__DIR__) . '/includes/shop-config.php';

$checks = [];

$appId = (string) ($config['square_application_id'] ?? '');
$token = (string) ($config['square_token'] ?? '');
$env = (string) ($config['square_environment'] ?? '');
$loc = (string) ($config['square_location_id'] ?? '');
$hook = (string) ($config['square_webhook_signature_key'] ?? '');
$siteUrl = (string) ($config['site_url'] ?? '');
$smtp = $config['smtp'] ?? null;

$checks['sandbox_app_id_present'] = str_starts_with($appId, 'sandbox-sq0idb-') || str_starts_with($appId, 'sq0idp-');
$checks['token_present'] = $token !== '' && $token !== 'REPLACE_ME';
$checks['location_present'] = $loc !== '' && $loc !== 'REPLACE_ME';
$checks['smtp_configured'] = is_array($smtp) && !empty($smtp['host']) && !empty($smtp['user']);
$checks['webhook_signature_set'] = $hook !== '' && $hook !== 'REPLACE_ME';
$checks['logos_present'] = is_file(dirname(__DIR__) . '/assets/img/logo-nav.png')
    && is_file(dirname(__DIR__) . '/assets/img/logo-full.png')
    && is_file(dirname(__DIR__) . '/assets/img/favicon.png');
$checks['apple_pay_association'] = is_file(
    dirname(__DIR__) . '/.well-known/apple-developer-merchantid-domain-association'
);

$isProduction = $env === 'production' && str_starts_with($appId, 'sq0idp-');
$checks['production_credentials'] = $isProduction;
$checks['site_url_live'] = str_starts_with($siteUrl, 'https://djstackle.com');

echo "DJ's Tackle production readiness\n";
echo str_repeat('-', 40) . "\n";
foreach ($checks as $name => $ok) {
    echo ($ok ? '[ok]  ' : '[!!] ') . $name . "\n";
}

$blockers = [];
if (!$checks['production_credentials']) {
    $blockers[] = 'Switch shop-config.php to production Application ID, access token, location ID, and square_environment=production';
}
if (!$checks['webhook_signature_set']) {
    $blockers[] = 'Create Square webhooks → paste signature key into square_webhook_signature_key';
}
if (!$checks['site_url_live']) {
    $blockers[] = 'Set site_url to https://djstackle.com on the live server config';
}
if (!$checks['apple_pay_association']) {
    $blockers[] = 'Download Apple Pay domain association from Square and save under .well-known/';
}
$blockers[] = 'Point djstackle.com document root at this PHP shop (or sync deploy) and place one small live test order, then refund';

echo "\nRemaining for live cutover:\n";
foreach ($blockers as $i => $b) {
    echo ($i + 1) . ". $b\n";
}

exit($checks['production_credentials'] && $checks['webhook_signature_set'] ? 0 : 2);

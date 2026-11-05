<?php

declare(strict_types=1);

/**
 * Replay a saved Square webhook JSON payload against the local handler.
 * Usage: php replay-webhook.php /path/to/payload.json /path/to/djstackle
 */

$payloadFile = $argv[1] ?? '';
$clientRoot = $argv[2] ?? dirname(__DIR__, 3) . '/djstackle';
if (!is_file($payloadFile)) {
    fwrite(STDERR, "Usage: php replay-webhook.php payload.json [clientRoot]\n");
    exit(1);
}

$shop_config_path = is_file($clientRoot . '/includes/shop-config.php')
    ? $clientRoot . '/includes/shop-config.php'
    : $clientRoot . '/includes/shop-config.example.php';

$shopConfig = require dirname(__DIR__) . '/bootstrap.php';
$body = (string) file_get_contents($payloadFile);
$result = \Webstar\Shop\Webhooks::make($shopConfig)->handle($body);
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

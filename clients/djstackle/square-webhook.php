<?php

declare(strict_types=1);

/**
 * Square webhook receiver. Register this exact URL in the Square Developer Dashboard.
 * Events: payment.updated, inventory.count.updated, catalog.version.updated
 */

require __DIR__ . '/includes/config.php';

use Webstar\Shop\Webhooks;

$body = file_get_contents('php://input') ?: '';
$signature = (string) ($_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '');
$hooks = Webhooks::make($shopConfig);

if (!$hooks->verify($body, $signature)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid signature';
    exit;
}

$result = $hooks->handle($body);
http_response_code($result['ok'] ? 200 : 500);
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($result);

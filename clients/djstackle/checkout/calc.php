<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Checkout;
use Webstar\Shop\Helpers;

header('Content-Type: application/json; charset=UTF-8');

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        throw new RuntimeException('POST required');
    }

    $input = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    if (!empty($input['shipping_method'])) {
        $shopCart->setShippingMethod((string) $input['shipping_method']);
    }
    if (!empty($input['email'])) {
        $shopCart->setEmail((string) $input['email']);
    }

    $form = [
        'email' => (string) ($input['email'] ?? $shopCart->email() ?? ''),
        'phone' => (string) ($input['phone'] ?? ''),
        'name' => (string) ($input['name'] ?? ''),
        'shipping_method' => (string) ($input['shipping_method'] ?? $shopCart->shippingMethod()),
        'address' => is_array($input['address'] ?? null) ? $input['address'] : [],
        'coupon' => (string) ($input['coupon'] ?? ''),
        'gift_card_token' => (string) ($input['gift_card_token'] ?? ''),
    ];

    $totals = Checkout::make($shopConfig)->calculate($shopCart, $form);
    echo json_encode(['ok' => true, 'totals' => $totals]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

<?php

declare(strict_types=1);

/**
 * Smoke test for on-site checkout.
 *
 * Demo mode: places a local order.
 * Live sandbox: charges with Square test tokens (cnon:card-nonce-ok).
 *
 * Usage: php bin/e2e-smoke.php
 */

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/webstar-business-solutions/clients/djstackle/';
$_SERVER['DOCUMENT_ROOT'] = 'C:/xampp/htdocs';
$_SERVER['SCRIPT_NAME'] = '/webstar-business-solutions/clients/djstackle/index.php';

require dirname(__DIR__) . '/includes/config.php';

echo 'demo=' . ($shopConfig->demoMode ? '1' : '0') . PHP_EOL;

$search = $shopCatalog->search(['per_page' => 5]);
echo 'products=' . $search['total'] . PHP_EOL;
if ($search['total'] < 1) {
    fwrite(STDERR, "Run php bin/seed-local.php or sync-catalog.php first\n");
    exit(1);
}

$email = 'e2e-test@djstackle.com';
$vars = $shopCatalog->variationsFor($search['items'][0]['id']);
$varId = $vars[0]['id'];
echo 'variation=' . $vars[0]['name'] . ' sku=' . ($vars[0]['sku'] ?? '') . PHP_EOL;

$shopCart->clear();
$shopCart->add($varId, 1);
$shopCart->setEmail($email);
$shopCart->setShippingMethod('flat');

$form = [
    'email' => $email,
    'phone' => '+18105550100',
    'name' => 'E2E Tester',
    'shipping_method' => 'flat',
    'address' => [
        'line1' => '123 Main St',
        'line2' => '',
        'city' => 'Sterling Heights',
        'state' => 'MI',
        'postal' => '48313',
        'country' => 'US',
    ],
    'coupon' => '',
    'gift_card_token' => '',
];

$checkout = \Webstar\Shop\Checkout::make($shopConfig);

$totals = $checkout->calculate($shopCart, $form);
echo 'calc due=' . $totals['due_cents']
    . ' tax=' . $totals['tax_cents']
    . ' ship=' . $totals['shipping_cents'] . PHP_EOL;

if ($shopConfig->demoMode) {
    $result = $checkout->pay($shopCart, $form);
} else {
    // Sandbox card success token (no Web Payments SDK needed)
    $form['source_id'] = 'cnon:card-nonce-ok';
    try {
        $result = $checkout->pay($shopCart, $form);
    } catch (Throwable $e) {
        fwrite(STDERR, 'PAY_FAIL: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

$order = \Webstar\Shop\Orders::make($shopConfig)->getByNumber($result['order_number']);
if (!$order) {
    fwrite(STDERR, "Local order missing after pay\n");
    exit(1);
}

echo 'order=' . $order['order_number']
    . ' total=' . $order['total_cents']
    . ' square_order=' . ($result['square_order_id'] ?? '') . PHP_EOL;
echo "E2E_OK\n";

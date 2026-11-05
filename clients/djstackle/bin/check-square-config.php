<?php

declare(strict_types=1);

$c = require dirname(__DIR__) . '/includes/shop-config.php';

foreach (['square_token', 'square_location_id', 'square_environment'] as $k) {
    $v = (string) ($c[$k] ?? '');
    $ok = $v !== '' && $v !== 'REPLACE_ME';
    if ($ok) {
        echo $k . '=' . substr($v, 0, 4) . '...(len=' . strlen($v) . ')' . PHP_EOL;
    } else {
        echo $k . '=' . ($v === '' ? '(empty)' : $v) . PHP_EOL;
    }
}

$shop_config_path = dirname(__DIR__) . '/includes/shop-config.php';
$shopConfig = require dirname(__DIR__, 2) . '/shared/shop/bootstrap.php';
echo 'demo_mode=' . ($shopConfig->demoMode ? 'yes' : 'no') . PHP_EOL;

<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : $root . '/includes/shop-config.example.php';

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';
$catalog = \Webstar\Shop\Catalog::make($shopConfig);

if ($shopConfig->demoMode) {
    echo "Demo mode — running seed-local instead.\n";
    require __DIR__ . '/seed-local.php';
    exit(0);
}

$result = $catalog->syncFromSquare();
echo 'Synced products=' . $result['products'] . ' variations=' . $result['variations']
    . ' inventory_rows=' . $result['inventory_updated'] . "\n";

<?php

declare(strict_types=1);

/**
 * cPanel cron (hourly):
 *   php /home/USER/.../clients/djstackle/bin/abandoned-carts.php
 */

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : $root . '/includes/shop-config.example.php';

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';
$result = \Webstar\Shop\AbandonedCarts::make($shopConfig)->process();
echo 'Abandoned cart reminders: 2h=' . $result['sent_2h'] . ' 24h=' . $result['sent_24h'] . "\n";

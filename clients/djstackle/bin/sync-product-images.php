<?php

declare(strict_types=1);

/**
 * Apply seed/catalog.php local_image + color_images onto the local SQLite catalog
 * so the PHP shop shows jig color photos (and other seed local images).
 *
 * Usage: php bin/sync-product-images.php
 */

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : $root . '/includes/shop-config.example.php';

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';
$n = \Webstar\Shop\Catalog::make($shopConfig)->applySeedImages();
echo "Applied seed images to {$n} product/variation rows.\n";

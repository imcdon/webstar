<?php

declare(strict_types=1);

/**
 * Seed SQLite from seed/catalog.php (no Square credentials required).
 * Usage: php bin/seed-local.php
 */

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : $root . '/includes/shop-config.example.php';

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';

$listings = \Webstar\Shop\SeedCatalog::load($root);

$catalog = \Webstar\Shop\Catalog::make($shopConfig);
$n = $catalog->seedFromArray($listings);
echo "Seeded {$n} products into {$shopConfig->sqlitePath}\n";

// Seed a couple approved demo reviews
$db = \Webstar\Shop\Db::pdo();
$product = $db->query("SELECT id FROM products WHERE name LIKE '%Walleye%' LIMIT 1")->fetch();
if ($product) {
    $exists = (int) $db->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
    if ($exists === 0) {
        $db->prepare(
            'INSERT INTO reviews (product_id, author_name, rating, body, is_verified, is_approved) VALUES (?,?,?,?,1,1)'
        )->execute([$product['id'], 'Mike', 5, 'Great jigs — chip-free paint and solid hooks. Will buy again.']);
        $db->prepare(
            'INSERT INTO reviews (product_id, author_name, rating, body, is_verified, is_approved) VALUES (?,?,?,?,1,1)'
        )->execute([$product['id'], 'Sarah', 5, 'River Ready box has everything I need for a weekend on the river.']);
        echo "Added demo reviews\n";
    }
}

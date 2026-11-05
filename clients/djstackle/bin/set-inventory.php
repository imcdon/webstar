<?php

declare(strict_types=1);

/**
 * Push stock quantities to Square for every synced variation.
 *
 * Quantities come from seed/catalog.php (matched by SKU);
 * anything unmatched falls back to --default (25).
 *
 *   php bin/set-inventory.php            # use CSV qtys
 *   php bin/set-inventory.php --qty=40   # force every variation to 40
 *   php bin/set-inventory.php --default=10
 *
 * Run bin/sync-catalog.php first so variations have Square IDs.
 */

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : $root . '/includes/shop-config.example.php';

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';

use Square\Types\BatchChangeInventoryRequest;
use Square\Types\InventoryChange;
use Square\Types\InventoryPhysicalCount;
use Webstar\Shop\Catalog;
use Webstar\Shop\Db;
use Webstar\Shop\SquareClientFactory;

if ($shopConfig->demoMode) {
    fwrite(STDERR, "Demo mode — Square credentials required.\n");
    exit(1);
}

$opts = getopt('', ['qty::', 'default::']);
$forceQty = isset($opts['qty']) && $opts['qty'] !== false ? (int) $opts['qty'] : null;
$defaultQty = isset($opts['default']) && $opts['default'] !== false ? (int) $opts['default'] : 25;

// SKU → qty from the seed catalog
$skuQty = [];
try {
    foreach (\Webstar\Shop\SeedCatalog::load($root) as $listing) {
        foreach ($listing['variations'] as $v) {
            if (!empty($v['sku'])) {
                $skuQty[(string) $v['sku']] = (int) $v['quantity'];
            }
        }
    }
} catch (Throwable) {
    // no seed — fall back to --default for everything
}

$db = Db::pdo();
$rows = $db->query(
    "SELECT v.square_id, v.sku, v.name, p.name AS product
     FROM variations v JOIN products p ON p.id = v.product_id
     WHERE v.square_id IS NOT NULL AND v.square_id NOT LIKE 'local_%'"
)->fetchAll();

if ($rows === []) {
    fwrite(STDERR, "No Square variations in local DB. Run bin/sync-catalog.php first.\n");
    exit(1);
}

$changes = [];
$now = date('c');
foreach ($rows as $r) {
    $qty = $forceQty ?? ($skuQty[(string) $r['sku']] ?? $defaultQty);
    $changes[] = new InventoryChange([
        'type' => 'PHYSICAL_COUNT',
        'physicalCount' => new InventoryPhysicalCount([
            'catalogObjectId' => $r['square_id'],
            'locationId' => $shopConfig->squareLocationId,
            'quantity' => (string) $qty,
            'state' => 'IN_STOCK',
            'occurredAt' => $now,
        ]),
    ]);
    printf("  %-45s %-10s qty=%d\n", mb_substr($r['product'], 0, 45), $r['name'], $qty);
}

$square = SquareClientFactory::make($shopConfig);
foreach (array_chunk($changes, 100) as $chunk) {
    $square->inventory->batchCreateChanges(new BatchChangeInventoryRequest([
        'idempotencyKey' => bin2hex(random_bytes(16)),
        'changes' => $chunk,
        'ignoreUnchangedCounts' => true,
    ]));
}
echo 'Pushed counts for ' . count($changes) . " variations to Square.\n";

$updated = Catalog::make($shopConfig)->syncInventory();
echo "Local inventory rows updated: {$updated}\n";

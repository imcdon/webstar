<?php

declare(strict_types=1);

/**
 * Push the client's seed catalog into Square (sandbox or production).
 *
 * Reads seed/catalog.php (structured listings with options) — falls back to
 * seed/products.json. Creates categories, item options (Size, Color, …),
 * items with one variation per option combination, and sets stock counts.
 *
 * Usage (cwd = clients/djstackle):
 *   php ../shared/shop/bin/seed-sandbox.php            # add/upsert
 *   php ../shared/shop/bin/seed-sandbox.php --replace  # delete existing items/options/categories first
 *
 * Requires includes/shop-config.php with real token + location_id.
 */

$clientRoot = getcwd();
if (!is_file($clientRoot . '/seed/catalog.php') && !is_file($clientRoot . '/seed/products.json')) {
    $clientRoot = dirname(__DIR__, 3) . '/djstackle';
}

$shop_config_path = is_file($clientRoot . '/includes/shop-config.php')
    ? $clientRoot . '/includes/shop-config.php'
    : null;
if (!$shop_config_path) {
    fwrite(STDERR, "Create includes/shop-config.php with Square credentials first.\n");
    exit(1);
}

$shopConfig = require dirname(__DIR__) . '/bootstrap.php';
if ($shopConfig->demoMode) {
    fwrite(STDERR, "shop-config still has REPLACE_ME token. Fill Square credentials.\n");
    exit(1);
}

use Square\Catalog\Requests\BatchDeleteCatalogObjectsRequest;
use Square\Catalog\Requests\BatchUpsertCatalogObjectsRequest;
use Square\Catalog\Requests\ListCatalogRequest;
use Square\Types\BatchChangeInventoryRequest;
use Square\Types\CatalogCategory;
use Square\Types\CatalogItem;
use Square\Types\CatalogItemOption;
use Square\Types\CatalogItemOptionForItem;
use Square\Types\CatalogItemOptionValue;
use Square\Types\CatalogItemOptionValueForItemVariation;
use Square\Types\CatalogItemVariation;
use Square\Types\CatalogObject;
use Square\Types\CatalogObjectBatch;
use Square\Types\CatalogObjectCategory;
use Square\Types\CatalogObjectItem;
use Square\Types\CatalogObjectItemOption;
use Square\Types\CatalogObjectItemOptionValue;
use Square\Types\CatalogObjectItemVariation;
use Square\Types\CatalogObjectTax;
use Square\Types\CatalogTax;
use Square\Types\Currency;
use Square\Types\InventoryChange;
use Square\Types\InventoryPhysicalCount;
use Square\Types\Money;
use Webstar\Shop\SeedCatalog;
use Webstar\Shop\SquareClientFactory;

$replace = in_array('--replace', $argv, true);

$listings = SeedCatalog::load($clientRoot);
$square = SquareClientFactory::make($shopConfig);
$locationId = $shopConfig->squareLocationId;

$tempId = static fn (string $prefix, string $name): string =>
    '#' . $prefix . '_' . strtoupper(trim((string) preg_replace('/[^A-Za-z0-9]+/', '_', $name), '_'));

// ---------------------------------------------------------------------------
// Optional: wipe existing items / item options / categories
// ---------------------------------------------------------------------------
if ($replace) {
    $toDelete = [];
    $pager = $square->catalog->list(new ListCatalogRequest(['types' => 'ITEM,ITEM_OPTION,CATEGORY,TAX']));
    foreach ($pager as $obj) {
        if ($obj->isItem()) {
            $toDelete[] = $obj->asItem()->getId();
        } elseif ($obj->isItemOption()) {
            $toDelete[] = $obj->asItemOption()->getId();
        } elseif ($obj->isCategory()) {
            $toDelete[] = $obj->asCategory()->getId();
        } elseif ($obj->isTax()) {
            $toDelete[] = $obj->asTax()->getId();
        }
    }
    if ($toDelete) {
        echo 'Deleting ' . count($toDelete) . " existing catalog objects...\n";
        foreach (array_chunk($toDelete, 200) as $chunk) {
            $square->catalog->batchDelete(new BatchDeleteCatalogObjectsRequest(['objectIds' => $chunk]));
        }
    }
}

// ---------------------------------------------------------------------------
// Build objects
// ---------------------------------------------------------------------------
$objects = [];

// Categories
$catIds = [];
foreach ($listings as $l) {
    $catIds[$l['category']] = true;
}
foreach (array_keys($catIds) as $name) {
    $id = $tempId('CAT', $name);
    $catIds[$name] = $id;
    $objects[] = CatalogObject::category(new CatalogObjectCategory([
        'id' => $id,
        'presentAtAllLocations' => true,
        'categoryData' => new CatalogCategory(['name' => $name]),
    ]));
}

// Sales tax (auto-applied on orders when items reference it)
$taxName = $shopConfig->squareTaxName !== '' ? $shopConfig->squareTaxName : 'Michigan Sales Tax';
$taxId = $tempId('TAX', $taxName);
$objects[] = CatalogObject::tax(new CatalogObjectTax([
    'id' => $taxId,
    'presentAtAllLocations' => true,
    'taxData' => new CatalogTax([
        'name' => $taxName,
        'calculationPhase' => 'TAX_SUBTOTAL_PHASE',
        'inclusionType' => 'ADDITIVE',
        'percentage' => '6.0',
        'enabled' => true,
    ]),
]));

// Item options: one global option per name, union of values across listings
/** @var array<string, array<string, string>> $optionValueIds optionName => valueName => tempId */
$optionValueIds = [];
$optionIds = [];
foreach ($listings as $l) {
    foreach ($l['options'] as $opt) {
        $optionIds[$opt['name']] ??= $tempId('OPT', $opt['name']);
        foreach ($opt['values'] as $value) {
            $optionValueIds[$opt['name']][$value] ??= $tempId('OPTV', $opt['name'] . '_' . $value);
        }
    }
}
foreach ($optionIds as $optName => $optId) {
    $valueObjects = [];
    $ordinal = 0;
    foreach ($optionValueIds[$optName] as $valueName => $valueId) {
        $valueObjects[] = CatalogObject::itemOptionVal(new CatalogObjectItemOptionValue([
            'id' => $valueId,
            'presentAtAllLocations' => true,
            'itemOptionValueData' => new CatalogItemOptionValue([
                'itemOptionId' => $optId,
                'name' => $valueName,
                'ordinal' => $ordinal++,
            ]),
        ]));
    }
    $objects[] = CatalogObject::itemOption(new CatalogObjectItemOption([
        'id' => $optId,
        'presentAtAllLocations' => true,
        'itemOptionData' => new CatalogItemOption([
            'name' => $optName,
            'displayName' => $optName,
            'showColors' => false,
            'values' => $valueObjects,
        ]),
    ]));
}

// Items + variations
$tempVarQty = [];
foreach ($listings as $l) {
    $itemId = $tempId('ITEM', $l['key']);
    $variations = [];
    foreach ($l['variations'] as $vi => $v) {
        $varId = $itemId . '_V' . $vi;
        $tempVarQty[$varId] = (int) $v['quantity'];
        $optionValues = [];
        foreach ($v['option_values'] as $optName => $valueName) {
            $optionValues[] = new CatalogItemOptionValueForItemVariation([
                'itemOptionId' => $optionIds[$optName],
                'itemOptionValueId' => $optionValueIds[$optName][$valueName],
            ]);
        }
        $variations[] = CatalogObject::itemVariation(new CatalogObjectItemVariation([
            'id' => $varId,
            'presentAtAllLocations' => true,
            'itemVariationData' => new CatalogItemVariation([
                'itemId' => $itemId,
                'name' => $v['name'],
                'sku' => $v['sku'],
                'ordinal' => $vi,
                'pricingType' => 'FIXED_PRICING',
                'priceMoney' => new Money([
                    'amount' => (int) $v['price_cents'],
                    'currency' => Currency::Usd->value,
                ]),
                'trackInventory' => true,
                'sellable' => true,
                'stockable' => true,
                'itemOptionValues' => $optionValues ?: null,
            ]),
        ]));
    }

    $itemOptions = array_map(
        static fn (array $opt) => new CatalogItemOptionForItem(['itemOptionId' => $optionIds[$opt['name']]]),
        $l['options'],
    );

        $objects[] = CatalogObject::item(new CatalogObjectItem([
        'id' => $itemId,
        'presentAtAllLocations' => true,
        'itemData' => new CatalogItem([
            'name' => $l['name'],
            'description' => $l['description'],
            'categoryId' => $catIds[$l['category']],
            'taxIds' => [$taxId],
            'variations' => $variations,
            'itemOptions' => $itemOptions ?: null,
            'productType' => 'REGULAR',
        ]),
    ]));
}

// ---------------------------------------------------------------------------
// Upsert (single batch so temp-ID references resolve together)
// ---------------------------------------------------------------------------
echo 'Upserting ' . count($objects) . " catalog objects (" . count($tempVarQty) . " variations)...\n";
$response = $square->catalog->batchUpsert(new BatchUpsertCatalogObjectsRequest([
    'idempotencyKey' => bin2hex(random_bytes(16)),
    'batches' => [new CatalogObjectBatch(['objects' => $objects])],
]));

$idMappings = [];
foreach ($response->getIdMappings() ?? [] as $map) {
    if ($map->getClientObjectId() && $map->getObjectId()) {
        $idMappings[$map->getClientObjectId()] = $map->getObjectId();
    }
}
echo '  ok (' . count($idMappings) . " ids mapped)\n";

// ---------------------------------------------------------------------------
// Inventory counts
// ---------------------------------------------------------------------------
$changes = [];
$now = date('c');
foreach ($tempVarQty as $tempVarId => $qty) {
    $serverId = $idMappings[$tempVarId] ?? null;
    if (!$serverId) {
        continue;
    }
    $changes[] = new InventoryChange([
        'type' => 'PHYSICAL_COUNT',
        'physicalCount' => new InventoryPhysicalCount([
            'catalogObjectId' => $serverId,
            'locationId' => $locationId,
            'quantity' => (string) $qty,
            'state' => 'IN_STOCK',
            'occurredAt' => $now,
        ]),
    ]);
}
if ($changes) {
    echo 'Setting stock for ' . count($changes) . " variations...\n";
    foreach (array_chunk($changes, 100) as $chunk) {
        $square->inventory->batchCreateChanges(new BatchChangeInventoryRequest([
            'idempotencyKey' => bin2hex(random_bytes(16)),
            'changes' => $chunk,
            'ignoreUnchangedCounts' => true,
        ]));
    }
}

echo "Done. Next: php bin/sync-catalog.php\n";

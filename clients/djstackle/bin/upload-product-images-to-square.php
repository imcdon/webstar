<?php

declare(strict_types=1);

/**
 * Upload one primary product photo per Square ITEM from seed/images/.
 * Matches seed listings to Square items by exact product name, then by
 * family heuristics for Ecwid-style item names (e.g. "Walleye Jigs 3/8 oz 2 pack").
 * Does not attach variation-level images.
 *
 * Usage (cwd = clients/djstackle):
 *   php bin/upload-product-images-to-square.php
 *
 * Requires includes/shop-config.php with a real Square token.
 * After upload: php bin/sync-catalog.php
 */

$root = dirname(__DIR__);
$shop_config_path = is_file($root . '/includes/shop-config.php')
    ? $root . '/includes/shop-config.php'
    : null;
if (!$shop_config_path) {
    fwrite(STDERR, "Create includes/shop-config.php with Square credentials first.\n");
    exit(1);
}

$shopConfig = require dirname($root) . '/shared/shop/bootstrap.php';
if ($shopConfig->demoMode) {
    fwrite(STDERR, "shop-config still has REPLACE_ME token. Fill Square credentials.\n");
    exit(1);
}

use Square\Catalog\Images\Requests\CreateImagesRequest;
use Square\Catalog\Requests\ListCatalogRequest;
use Square\Types\CatalogImage;
use Square\Types\CatalogObject;
use Square\Types\CatalogObjectImage;
use Square\Types\CreateCatalogImageRequest;
use Square\Utils\File;
use Webstar\Shop\SeedCatalog;
use Webstar\Shop\SquareClientFactory;

/**
 * @param array<string, array<string,mixed>> $byName lowercase name => listing
 * @param array<string, array<string,mixed>> $byKey key => listing
 * @return ?array<string,mixed>
 */
$resolveSeed = static function (string $squareName, array $byName, array $byKey): ?array {
    $lower = strtolower(trim($squareName));
    if ($lower === '') {
        return null;
    }
    if (isset($byName[$lower])) {
        return $byName[$lower];
    }

    // Ecwid-era Square catalog uses separate items per size; map to seed families.
    if (str_contains($lower, 'walleye jig')) {
        return $byKey['walleye-jigs'] ?? null;
    }
    if (str_contains($lower, 'bottom bouncer')) {
        return $byKey['bottom-bouncers'] ?? null;
    }
    if (str_contains($lower, 'crawler harness')) {
        return $byKey['crawler-harness'] ?? null;
    }
    if (str_contains($lower, 'stinger hook')) {
        return $byKey['stinger-hooks'] ?? null;
    }
    if (str_contains($lower, 'arctic spinner')) {
        return $byKey['arctic-spinners'] ?? null;
    }
    if (str_contains($lower, 'river ready')) {
        return $byKey['river-ready-box'] ?? null;
    }

    return null;
};

$listings = SeedCatalog::load($root);
$byName = [];
$byKey = [];
foreach ($listings as $listing) {
    $name = strtolower(trim((string) ($listing['name'] ?? '')));
    $key = (string) ($listing['key'] ?? '');
    if ($name !== '') {
        $byName[$name] = $listing;
    }
    if ($key !== '') {
        $byKey[$key] = $listing;
    }
}

$square = SquareClientFactory::make($shopConfig);
$iterator = $square->catalog->list(new ListCatalogRequest([
    'types' => 'ITEM',
]));

$ok = 0;
$skip = 0;
$fail = 0;
$matched = 0;

foreach ($iterator as $object) {
    if (!$object->isItem()) {
        continue;
    }
    $item = $object->asItem();
    $itemId = (string) ($item->getId() ?? '');
    $itemData = $item->getItemData();
    $itemName = trim((string) ($itemData?->getName() ?? ''));
    if ($itemId === '' || $itemName === '') {
        continue;
    }

    $seed = $resolveSeed($itemName, $byName, $byKey);
    if ($seed === null) {
        echo "SKIP  Square item “{$itemName}”: no matching seed listing\n";
        $skip++;
        continue;
    }
    $matched++;

    $localRel = trim((string) ($seed['local_image'] ?? ''));
    $path = $localRel !== '' ? $root . '/' . str_replace('\\', '/', $localRel) : '';
    if ($path === '' || !is_file($path) || !is_readable($path)) {
        echo "FAIL  {$itemName}: local image missing ({$localRel}). Run bin/import-product-images.php first.\n";
        $fail++;
        continue;
    }

    $hash = hash_file('sha256', $path) ?: (string) filemtime($path);
    $idempotencyKey = substr('img-' . $itemId . '-' . $hash, 0, 128);
    $seedKey = preg_replace('/[^a-zA-Z0-9_]/', '_', (string) ($seed['key'] ?? 'item')) ?: 'item';

    try {
        $mime = 'image/jpeg';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $mime = 'image/png';
        } elseif ($ext === 'gif') {
            $mime = 'image/gif';
        }

        $response = $square->catalog->images->create(new CreateImagesRequest([
            'imageFile' => File::createFromFilepath($path, basename($path), $mime),
            'request' => new CreateCatalogImageRequest([
                'idempotencyKey' => $idempotencyKey,
                'objectId' => $itemId,
                'isPrimary' => true,
                'image' => CatalogObject::image(new CatalogObjectImage([
                    'id' => '#img_' . $seedKey,
                    'imageData' => new CatalogImage([
                        'name' => $itemName,
                        'caption' => $itemName,
                    ]),
                ])),
            ]),
        ]));

        $imageId = '';
        $imageObj = $response->getImage();
        if ($imageObj !== null && $imageObj->isImage()) {
            $imageId = (string) ($imageObj->asImage()->getId() ?? '');
        }
        echo "OK    {$itemName}: attached image" . ($imageId !== '' ? " {$imageId}" : '') . "\n";
        $ok++;
    } catch (Throwable $e) {
        echo "FAIL  {$itemName}: " . $e->getMessage() . "\n";
        $fail++;
    }
}

if ($matched === 0) {
    echo "No Square items matched seed product families. Seed Square first (seed-sandbox.php).\n";
}

echo "\nDone. uploaded={$ok} skipped={$skip} failed={$fail} matched={$matched}\n";
echo "Next: php bin/sync-catalog.php\n";
exit($fail > 0 ? 1 : 0);

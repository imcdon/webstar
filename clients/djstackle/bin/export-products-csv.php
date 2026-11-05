<?php

declare(strict_types=1);

/**
 * Export the structured seed catalog (seed/catalog.php) to CSV.
 *
 *   seed/products.csv               — flat reference sheet (one row per variation)
 *   seed/products-square-import.csv — Square Item Library import format
 *                                      (Option Name/Value 1 & 2 build item options)
 *
 * Note: the preferred way to load Square is bin/seed-sandbox.php (API); this CSV
 * is a fallback for manual Dashboard import.
 */

$root = dirname(__DIR__);
require dirname($root) . '/shared/shop/vendor/autoload.php';

use Webstar\Shop\SeedCatalog;

$listings = SeedCatalog::load($root);
$csvPath = $root . '/seed/products.csv';
$squarePath = $root . '/seed/products-square-import.csv';

function writeCsv(string $path, array $headers, array $rows): void
{
    $fh = fopen($path, 'wb');
    if ($fh === false) {
        throw new RuntimeException("Cannot write $path");
    }
    fwrite($fh, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
    fputcsv($fh, $headers);
    foreach ($rows as $row) {
        fputcsv($fh, $row);
    }
    fclose($fh);
}

$exportRows = [];
$squareRows = [];

foreach ($listings as $l) {
    $image = $l['images'][0] ?? '';
    $optNames = array_column($l['options'], 'name');

    foreach ($l['variations'] as $v) {
        $price = number_format(((int) $v['price_cents']) / 100, 2, '.', '');
        $ov = $v['option_values'];

        $exportRows[] = [
            $l['key'],
            $l['name'],
            $v['name'],
            $l['description'],
            $l['category'],
            $v['sku'],
            $price,
            $v['quantity'],
            $optNames[0] ?? '', $ov[$optNames[0] ?? ''] ?? '',
            $optNames[1] ?? '', $ov[$optNames[1] ?? ''] ?? '',
            $image,
            $l['local_image'] ?? '',
        ];

        $squareRows[] = [
            '',                       // Token (blank = new)
            '',                       // Item Type (blank = Standard)
            $l['name'],
            $v['name'],
            $l['description'],
            $l['category'],
            $v['sku'],
            $price,
            $optNames[0] ?? '', $ov[$optNames[0] ?? ''] ?? '',
            $optNames[1] ?? '', $ov[$optNames[1] ?? ''] ?? '',
            'Y',                      // Sellable
            'Y',                      // Stockable
            (string) $v['quantity'],  // New Quantity
            $image,
        ];
    }
}

writeCsv($csvPath, [
    'key', 'item_name', 'variation_name', 'description', 'category', 'sku', 'price_usd', 'quantity',
    'option_1', 'option_1_value', 'option_2', 'option_2_value', 'image_url', 'local_image',
], $exportRows);

// Tax column omitted: Square rejects it unless a matching tax already exists.
writeCsv($squarePath, [
    'Token', 'Item Type', 'Item Name', 'Variation Name', 'Description', 'Category', 'SKU', 'Price',
    'Option Name 1', 'Option Value 1', 'Option Name 2', 'Option Value 2',
    'Sellable', 'Stockable', 'New Quantity', 'Image URL',
], $squareRows);

echo 'Wrote ' . count($exportRows) . " variation rows across " . count($listings) . " listings\n";
echo "  $csvPath\n";
echo "  $squarePath\n";

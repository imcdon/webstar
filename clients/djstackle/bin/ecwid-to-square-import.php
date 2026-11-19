<?php

declare(strict_types=1);

/**
 * Convert an Ecwid catalog export CSV to Square Item Library import CSV.
 *
 * Usage:
 *   php bin/ecwid-to-square-import.php "C:\path\catalog_export.csv" ["C:\path\output.csv"] [default_qty]
 *
 * default_qty — Stockable=Y and New Quantity on every row (default 25). Ecwid qty used when tracked and > 0.
 *
 * Output columns match seed/products-square-import.csv (Dashboard import).
 * Multi-select “Color 1…8” box builders are exported as a single variation
 * with a note in the description — use seed-sandbox.php or manual setup for those.
 */

$inPath = $argv[1] ?? '';
$outPath = $argv[2] ?? '';

if ($inPath === '' || !is_file($inPath)) {
    fwrite(STDERR, "Usage: php ecwid-to-square-import.php <ecwid-export.csv> [output.csv]\n");
    exit(1);
}

if ($outPath === '') {
    $outPath = preg_replace('/\.csv$/i', '', $inPath) . '-square-import.csv';
}

$defaultQty = isset($argv[3]) ? max(0, (int) $argv[3]) : 25;

/**
 * @return array{0: string, 1: string} Stockable, New Quantity
 */
function stockColumns(array $idx, array $row, int $defaultQty): array
{
    $tracked = strtolower((string) ($row[$idx['product_is_inventory_tracked']] ?? '')) === 'true';
    $ecwidQty = (int) ($row[$idx['product_quantity']] ?? 0);
    if ($tracked && $ecwidQty > 0) {
        return ['Y', (string) $ecwidQty];
    }
    if ($defaultQty > 0) {
        return ['Y', (string) $defaultQty];
    }

    return ['N', ''];
}

require dirname(__DIR__, 2) . '/shared/shop/vendor/autoload.php';

use Square\Locations\Requests\GetLocationsRequest;
use Webstar\Shop\Helpers;
use Webstar\Shop\SquareClientFactory;

/**
 * @return list<string>
 */
function rowStockTail(string $stockable, string $newQty, string $image, int $defaultQty, ?string $locationName): array
{
    $qty = $newQty !== '' ? $newQty : (string) $defaultQty;
    if ($defaultQty > 0) {
        $stockable = 'Y';
    }
    $tail = ['Y', $stockable, $qty, $image];
    if ($locationName !== null && $locationName !== '') {
        $tail[] = 'Y';
        $tail[] = $qty;
    }

    return $tail;
}

$locationName = null;
$configRoot = dirname(__DIR__);
$configPath = $configRoot . '/includes/shop-config.php';
if (is_file($configPath) && !in_array('--no-location', $argv, true)) {
    try {
        $shop_config_path = $configPath;
        $shopConfig = require dirname($configRoot) . '/shared/shop/bootstrap.php';
        if (!$shopConfig->demoMode && $shopConfig->squareLocationId !== '') {
            $square = SquareClientFactory::make($shopConfig);
            $locRes = $square->locations->get(new GetLocationsRequest([
                'locationId' => $shopConfig->squareLocationId,
            ]));
            $locationName = $locRes->getLocation()?->getName();
        }
    } catch (Throwable) {
        // optional
    }
}

const MAX_COMBINATIONS = 500;

/** @return array<string, mixed> */
function readEcwidExport(string $path): array
{
    $fh = fopen($path, 'rb');
    if ($fh === false) {
        throw new RuntimeException("Cannot read $path");
    }
    $header = fgetcsv($fh);
    if ($header === false) {
        fclose($fh);
        throw new RuntimeException('Empty CSV');
    }
    $idx = array_flip($header);

    $products = [];
    $options = [];

    while (($row = fgetcsv($fh)) !== false) {
        $type = $row[0] ?? '';
        $id = (string) ($row[$idx['product_internal_id']] ?? '');

        if ($type === 'product') {
            $products[$id] = $row;
        } elseif ($type === 'product_option') {
            $optType = (string) ($row[$idx['product_option_type']] ?? '');
            if ($optType === 'TEXTINPUT' || $optType === 'DATE' || $optType === 'FILES') {
                continue;
            }
            $name = trim((string) ($row[$idx['product_option_name']] ?? ''));
            $value = trim((string) ($row[$idx['product_option_value']] ?? ''));
            if ($name === '' || $value === '') {
                continue;
            }
            $markup = (float) ($row[$idx['product_option_markup']] ?? 0);
            $key = mb_strtolower($value);
            if (!isset($options[$id][$name])) {
                $options[$id][$name] = [];
            }
            // Ecwid sometimes lists the same color twice with different casing; Square rejects duplicates.
            if (array_key_exists($key, $options[$id][$name])) {
                continue;
            }
            $options[$id][$name][$key] = ['value' => $value, 'markup' => $markup];
        }
    }
    fclose($fh);

    return ['idx' => $idx, 'products' => $products, 'options' => $options];
}

function plainDescription(string $html): string
{
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';
    // Square import troubleshooting: commas in import file can cause issues.
    return str_replace(',', ';', $text);
}

function guessCategory(string $name): string
{
    $n = strtolower($name);
    if (str_contains($n, 'walleye jig')) {
        return 'Walleye Jigs';
    }
    if (str_contains($n, 'bottom bouncer')) {
        return 'Bottom Bouncers';
    }
    if (str_contains($n, 'crawler harness')) {
        return 'Crawler Harnesses';
    }
    if (str_contains($n, 'river ready')) {
        return 'River Ready Boxes';
    }
    if (str_contains($n, 'arctic spinner')) {
        return 'Arctic Spinners';
    }
    if (str_contains($n, 'stinger')) {
        return 'Stinger Hooks';
    }

    return '';
}

/** @param list<array{name: string, values: list<array{value: string, markup: float}>}> $sets */
function combinationCount(array $sets): int
{
    $n = 1;
    foreach ($sets as $set) {
        $n *= max(1, count($set['values']));
    }

    return $n;
}

/**
 * @param list<array{name: string, values: list<array{value: string, markup: float}>}> $sets
 * @return list<list<array{name: string, value: string, markup: float}>>
 */
function cartesianOptionTuples(array $sets): array
{
    $out = [[]];
    foreach ($sets as $set) {
        $next = [];
        foreach ($out as $partial) {
            foreach ($set['values'] as $v) {
                $next[] = array_merge($partial, [
                    ['name' => $set['name'], 'value' => $v['value'], 'markup' => $v['markup']],
                ]);
            }
        }
        $out = $next;
    }

    return $out;
}

function makeSku(string|int $productId, string $productSku, string $variationKey): string
{
    $base = $productSku !== '' ? $productSku : 'DJT-' . (string) $productId;
    $suffix = Helpers::slugify($variationKey);

    return strtoupper(substr($base . '-' . $suffix, 0, 64));
}

$data = readEcwidExport($inPath);
$idx = $data['idx'];
$squareRows = [];
$warnings = [];

foreach ($data['products'] as $id => $row) {
    $name = trim((string) ($row[$idx['product_name']] ?? ''));
    if ($name === '') {
        continue;
    }

    $basePrice = (float) ($row[$idx['product_price']] ?? 0);
    $desc = plainDescription((string) ($row[$idx['product_description']] ?? ''));
    $category = guessCategory($name);
    $image = trim((string) ($row[$idx['product_media_main_image_url']] ?? ''));
    $productSku = trim((string) ($row[$idx['product_sku']] ?? ''));
    [$stockable, $newQty] = stockColumns($idx, $row, $defaultQty);

    $optMap = $data['options'][$id] ?? [];
    /** @var list<array{name: string, values: list<array{value: string, markup: float}>}> $sets */
    $sets = [];
    foreach ($optMap as $optName => $values) {
        $sets[] = ['name' => $optName, 'values' => array_values($values)];
    }

    $multiColorSlots = false;
    foreach ($sets as $set) {
        if (preg_match('/^Color\s+\d+$/i', $set['name'])) {
            $multiColorSlots = true;
            break;
        }
    }

    $comboCount = $sets === [] ? 1 : combinationCount($sets);
    $simplify = count($sets) > 2 || $multiColorSlots || $comboCount > MAX_COMBINATIONS;

    if ($simplify) {
        $warnings[] = "$name ($id): exported as single variation — "
            . count($sets) . ' option group(s); configure color slots in Square or use bin/seed-sandbox.php.';
        $note = ' Customer selects multiple options at checkout; verify item setup in Square after import.';
        $squareRows[] = array_merge(
            [
                '', '', $name, 'Standard', $desc . $note, $category,
                makeSku($id, $productSku, 'standard'),
                number_format($basePrice, 2, '.', ''),
                '', '', '', '',
            ],
            rowStockTail($stockable, $newQty, $image, $defaultQty, $locationName),
        );
        continue;
    }

    if ($sets === []) {
        $squareRows[] = array_merge(
            [
                '', '', $name, 'Regular', $desc, $category,
                makeSku($id, $productSku, 'regular'),
                number_format($basePrice, 2, '.', ''),
                '', '', '', '',
            ],
            rowStockTail($stockable, $newQty, $image, $defaultQty, $locationName),
        );
        continue;
    }

    if (count($sets) === 1) {
        $optName = $sets[0]['name'];
        foreach ($sets[0]['values'] as $v) {
            $price = $basePrice + $v['markup'];
            $varName = $v['value'];
            $squareRows[] = array_merge(
                [
                    '', '', $name, $varName, $desc, $category,
                    makeSku($id, $productSku, $varName),
                    number_format($price, 2, '.', ''),
                    $optName, $v['value'], '', '',
                ],
                rowStockTail($stockable, $newQty, $image, $defaultQty, $locationName),
            );
        }
        continue;
    }

    // Exactly two option groups — Square CSV supports Option 1 & 2.
    $tuples = cartesianOptionTuples($sets);
    foreach ($tuples as $tuple) {
        $markup = array_sum(array_column($tuple, 'markup'));
        $price = $basePrice + $markup;
        $varName = implode(' / ', array_column($tuple, 'value'));
        $squareRows[] = array_merge(
            [
                '', '', $name, $varName, $desc, $category,
                makeSku($id, $productSku, $varName),
                number_format($price, 2, '.', ''),
                $tuple[0]['name'], $tuple[0]['value'],
                $tuple[1]['name'], $tuple[1]['value'],
            ],
            rowStockTail($stockable, $newQty, $image, $defaultQty, $locationName),
        );
    }
}

$headers = [
    'Token', 'Item Type', 'Item Name', 'Variation Name', 'Description', 'Category', 'SKU', 'Price',
    'Option Name 1', 'Option Value 1', 'Option Name 2', 'Option Value 2',
    'Sellable', 'Stockable', 'New Quantity', 'Image URL',
];
if ($locationName !== null && $locationName !== '') {
    $headers[] = 'Enabled ' . $locationName;
    $headers[] = 'New Quantity ' . $locationName;
}

$fh = fopen($outPath, 'wb');
if ($fh === false) {
    throw new RuntimeException("Cannot write $outPath");
}
fwrite($fh, "\xEF\xBB\xBF");
fputcsv($fh, $headers);
foreach ($squareRows as $row) {
    fputcsv($fh, $row);
}
fclose($fh);

echo "Wrote " . count($squareRows) . " variation rows for " . count($data['products']) . " Ecwid products\n";
echo "  default New Quantity=$defaultQty (Stockable=Y on stocked rows)\n";
if ($locationName) {
    echo "  location columns: Enabled / New Quantity \"$locationName\"\n";
}
echo "  $outPath\n";

if ($warnings !== []) {
    echo "\nWarnings (" . count($warnings) . "):\n";
    foreach ($warnings as $w) {
        echo "  - $w\n";
    }
}
<?php

declare(strict_types=1);

/**
 * Download primary product photos from Ecwid CDN URLs in seed/catalog.php
 * into seed/images/ (one image per product family).
 *
 * Usage (cwd = clients/djstackle):
 *   php bin/import-product-images.php
 *   php bin/import-product-images.php --force
 */

$root = dirname(__DIR__);
$force = in_array('--force', $argv, true);

require dirname($root) . '/shared/shop/vendor/autoload.php';

$listings = \Webstar\Shop\SeedCatalog::load($root);
$ok = 0;
$skip = 0;
$fail = 0;

foreach ($listings as $listing) {
    $key = (string) ($listing['key'] ?? '');
    $name = (string) ($listing['name'] ?? $key);
    $localRel = trim((string) ($listing['local_image'] ?? ''));
    $images = $listing['images'] ?? [];
    $sourceUrl = is_array($images) && isset($images[0]) ? trim((string) $images[0]) : '';

    if ($localRel === '' || $sourceUrl === '') {
        echo "FAIL  {$name}: missing local_image or images[0]\n";
        $fail++;
        continue;
    }

    $dest = $root . '/' . str_replace('\\', '/', $localRel);
    $dir = dirname($dest);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        echo "FAIL  {$name}: cannot create {$dir}\n";
        $fail++;
        continue;
    }

    if (is_file($dest) && !$force) {
        $bytes = filesize($dest) ?: 0;
        echo "SKIP  {$name}: {$localRel} already exists ({$bytes} bytes). Use --force to re-download.\n";
        $skip++;
        continue;
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 45,
            'header' => "User-Agent: DJTackle-ImageImport/1.0\r\nAccept: image/*\r\n",
            'follow_location' => 1,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $data = @file_get_contents($sourceUrl, false, $ctx);
    if ($data === false || $data === '') {
        echo "FAIL  {$name}: download failed\n       {$sourceUrl}\n";
        $fail++;
        continue;
    }

    if (@file_put_contents($dest, $data) === false) {
        echo "FAIL  {$name}: cannot write {$localRel}\n";
        $fail++;
        continue;
    }

    echo 'OK    ' . $name . ': ' . $localRel . ' (' . strlen($data) . " bytes)\n";
    $ok++;
}

echo "\nDone. downloaded={$ok} skipped={$skip} failed={$fail}\n";
exit($fail > 0 ? 1 : 0);

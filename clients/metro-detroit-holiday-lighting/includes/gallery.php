<?php

declare(strict_types=1);

/**
 * List gallery image files from assets/img/gallery/.
 *
 * @return list<array{file:string,url:string,alt:string}>
 */
function mdhl_gallery_images(): array
{
    $dir = dirname(__DIR__) . '/assets/img/gallery';
    if (!is_dir($dir)) {
        return [];
    }

    $files = scandir($dir) ?: [];
    $out = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (!preg_match('/\.(jpe?g|png|webp)$/i', $file)) {
            continue;
        }
        $base = pathinfo($file, PATHINFO_FILENAME);
        $label = ucwords(str_replace(['-', '_'], ' ', $base));
        $out[] = [
            'file' => $file,
            'url' => url('assets/img/gallery/' . rawurlencode($file)),
            'alt' => 'Holiday lighting by Metro Detroit Holiday Lighting — ' . $label,
        ];
    }

    usort($out, static fn ($a, $b) => strcasecmp($a['file'], $b['file']));

    return $out;
}

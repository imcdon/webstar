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
        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
        $width = 0;
        $height = 0;
        $size = @getimagesize($fullPath);
        if (is_array($size)) {
            $width = (int) $size[0];
            $height = (int) $size[1];
        }
        $orientation = 'landscape';
        if ($width > 0 && $height > 0) {
            if ($height > $width) {
                $orientation = 'portrait';
            } elseif ($width === $height) {
                $orientation = 'square';
            }
        }
        $out[] = [
            'file' => $file,
            'url' => url('assets/img/gallery/' . rawurlencode($file)),
            'alt' => 'Holiday lighting by Metro Detroit Holiday Lighting — ' . $label,
            'width' => $width,
            'height' => $height,
            'orientation' => $orientation,
        ];
    }

    usort($out, static fn ($a, $b) => strcasecmp($a['file'], $b['file']));

    return $out;
}

/**
 * Filter gallery images whose filenames match any of the given patterns.
 *
 * @param list<string> $patterns PCRE patterns matched against the filename
 * @param int $limit Max images to return (0 = all matches)
 * @return list<array{file:string,url:string,alt:string}>
 */
function mdhl_gallery_for_landing(array $patterns, int $limit = 6): array
{
    if ($patterns === []) {
        return [];
    }

    $matched = [];
    foreach (mdhl_gallery_images() as $img) {
        foreach ($patterns as $pattern) {
            if (@preg_match($pattern, $img['file']) === 1) {
                $matched[] = $img;
                break;
            }
        }
    }

    if ($limit > 0) {
        $matched = array_slice($matched, 0, $limit);
    }

    return $matched;
}

/**
 * Return gallery entries for an ordered list of filenames (skips missing files).
 *
 * @param list<string> $filenames Basenames under assets/img/gallery/
 * @return list<array{file:string,url:string,alt:string,width:int,height:int,orientation:string}>
 */
function mdhl_gallery_pick_files(array $filenames): array
{
    if ($filenames === []) {
        return [];
    }

    $byFile = [];
    foreach (mdhl_gallery_images() as $img) {
        $byFile[$img['file']] = $img;
    }

    $out = [];
    foreach ($filenames as $file) {
        if (isset($byFile[$file])) {
            $out[] = $byFile[$file];
        }
    }

    return $out;
}

<?php

declare(strict_types=1);

/**
 * Phase 0 content inventory — stdout CSV.
 * Usage: php bin/content-phase0-audit.php > docs/content-revamp/phase0-inventory.csv
 */

$root = dirname(__DIR__);

function word_count_html(string $html): int
{
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

    return $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
}

function landing_mc_words(array $page): int
{
    $n = 0;
    foreach ($page['sections'] ?? [] as $sec) {
        $n += word_count_html((string) ($sec['body'] ?? ''));
    }

    return $n;
}

function max_section_words(array $page): int
{
    $max = 0;
    foreach ($page['sections'] ?? [] as $sec) {
        $max = max($max, word_count_html((string) ($sec['body'] ?? '')));
    }

    return $max;
}

function seo_red_flags(array $page): string
{
    $flags = [];
    $blob = '';
    foreach ($page['sections'] ?? [] as $sec) {
        $blob .= ' ' . ($sec['body'] ?? '');
    }
    $blob = strtolower(strip_tags($blob));
    if (max_section_words($page) >= 280) {
        $flags[] = 'long_section';
    }
    if (landing_mc_words($page) >= 1200) {
        $flags[] = 'high_total_mc';
    }
    foreach (['map pack', 'core web vitals', 'i-75', 'somerset', 'search engine', 'indexable'] as $phrase) {
        if (str_contains($blob, $phrase)) {
            $flags[] = 'keyword_stuff:' . str_replace(' ', '_', $phrase);
        }
    }

    return implode(';', array_unique($flags));
}

$rows = [];

$articles = require $root . '/library/articles-data.php';
foreach ($articles as $a) {
    $slug = (string) ($a['slug'] ?? '');
    $path = $root . '/library/articles/' . preg_replace('/[^a-z0-9-]/', '', $slug) . '.php';
    $body = is_readable($path) ? file_get_contents($path) : '';
    $body = (string) preg_replace('/<\?php[\s\S]*?\?>/', '', $body);
    $words = word_count_html($body);
    $flags = [];
    if ($words >= 800) {
        $flags[] = 'long_guide';
    }
    if (substr_count(strtolower($body), '$350') >= 3) {
        $flags[] = 'repeated_pricing';
    }
    if (substr_count(strtolower($body), 'metro detroit') >= 4) {
        $flags[] = 'geo_repeat';
    }
    $disposition = ($words >= 800 || $flags !== []) ? 'rewrite' : 'keep';
    if (str_starts_with((string) ($a['published'] ?? ''), '2026-11-')) {
        $disposition = $flags === [] ? 'keep' : 'light_edit';
    }
    $rows[] = [
        'kind' => 'guide',
        'slug' => $slug,
        'url' => 'resources/' . $slug . '/',
        'source' => 'library/articles/' . $slug . '.php',
        'type' => 'resource',
        'primary_keyword' => (string) ($a['primary_keyword'] ?? ''),
        'published' => (string) ($a['published'] ?? ''),
        'updated' => (string) ($a['updated'] ?? ''),
        'total_words' => $words,
        'max_section' => 0,
        'section_count' => substr_count($body, '<h2'),
        'red_flags' => implode(';', $flags),
        'search_first_risk' => $flags !== [] ? 'yes' : 'no',
        'disposition' => $disposition,
        'priority' => 'P1',
        'experience_hook' => '',
        'notes' => $disposition === 'keep' || $disposition === 'light_edit' ? 'Phase 1 revamp shipped' : 'Needs rewrite',
    ];
}

foreach (['seo-pages-data.php' => require $root . '/library/seo-pages-data.php', 'seo-pages-batch.php' => require $root . '/library/seo-pages-batch.php'] as $file => $pages) {
    foreach ($pages as $p) {
        $slug = (string) ($p['slug'] ?? '');
        $total = landing_mc_words($p);
        $maxSec = max_section_words($p);
        $flags = seo_red_flags($p);
        $type = (string) ($p['type'] ?? '');
        $disp = 'rewrite';
        if ($file === 'seo-pages-data.php' && $total <= 650 && $maxSec <= 160) {
            $disp = 'keep';
        }
        $searchFirst = $file === 'seo-pages-batch.php' ? 'yes' : 'maybe';
        $priority = 'P2';
        if ($file === 'seo-pages-batch.php') {
            $priority = $total >= 1000 ? 'P0' : 'P1';
        }

        $rows[] = [
            'kind' => 'landing',
            'slug' => $slug,
            'url' => $slug . '/',
            'source' => 'library/' . $file,
            'type' => $type,
            'primary_keyword' => (string) ($p['h1'] ?? $p['title'] ?? ''),
            'published' => '',
            'updated' => (string) ($p['updated'] ?? ''),
            'total_words' => $total,
            'max_section' => $maxSec,
            'section_count' => count($p['sections'] ?? []),
            'red_flags' => $flags,
            'search_first_risk' => $searchFirst,
            'disposition' => $disp,
            'priority' => $priority,
            'experience_hook' => '',
            'notes' => $file === 'seo-pages-batch.php' ? 'Batch template MC — scaled-content priority' : 'Shorter MC — light pass',
        ];
    }
}

$out = fopen('php://output', 'w');
if ($rows !== []) {
    fputcsv($out, array_keys($rows[0]));
    foreach ($rows as $row) {
        fputcsv($out, $row);
    }
}
fclose($out);

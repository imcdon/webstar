<?php

declare(strict_types=1);

function webstar_config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/site-config.php';
    }

    return $config;
}

function webstar_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Site root URL path (strips /portal or /library when the current script lives there).
 */
function webstar_base_path(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = dirname($script);
    if ($dir === '/' || $dir === '.') {
        return '';
    }
    $dir = rtrim($dir, '/');
    if (preg_match('#/(portal|library)$#', $dir)) {
        $dir = dirname($dir);
        if ($dir === '/' || $dir === '\\' || $dir === '.') {
            return '';
        }
    }

    return rtrim(str_replace('\\', '/', $dir), '/');
}

function webstar_url(string $path = ''): string
{
    $base = webstar_base_path();
    $fragment = '';
    $hashPos = strpos($path, '#');
    if ($hashPos !== false) {
        $fragment = substr($path, $hashPos);
        $path = substr($path, 0, $hashPos);
    }

    $path = ltrim($path, '/');
    $path = webstar_clean_path($path);

    if ($path === '') {
        $url = $base === '' ? '/' : $base . '/';
    } else {
        $url = ($base === '' ? '' : $base) . '/' . $path;
    }

    return $url . $fragment;
}

/**
 * Map legacy PHP / query paths to clean public paths.
 */
function webstar_clean_path(string $path): string
{
    if ($path === '' || $path === 'index.php') {
        return '';
    }

    $query = '';
    $qPos = strpos($path, '?');
    if ($qPos !== false) {
        $query = substr($path, $qPos + 1);
        $path = substr($path, 0, $qPos);
    }

    $static = [
        'packages.php' => 'packages/',
        'examples.php' => 'examples/',
        'about.php' => 'about/',
        'contact.php' => 'contact/',
        'privacy.php' => 'privacy/',
        'terms.php' => 'terms/',
    ];
    if (isset($static[$path])) {
        return $static[$path];
    }

    if ($path === 'package.php') {
        parse_str($query, $params);
        $slug = trim((string) ($params['slug'] ?? ''));
        if ($slug !== '') {
            return 'packages/' . rawurlencode($slug) . '/';
        }

        return 'packages/';
    }

    if ($path === 'landing.php') {
        parse_str($query, $params);
        $slug = trim((string) ($params['slug'] ?? ''));
        if ($slug !== '') {
            return rawurlencode($slug) . '/';
        }

        return '';
    }

    return $path . ($query !== '' ? '?' . $query : '');
}

function webstar_package_url(string $slug, string $fragment = ''): string
{
    $url = webstar_url('packages/' . rawurlencode($slug) . '/');
    if ($fragment !== '') {
        $url .= '#' . ltrim($fragment, '#');
    }

    return $url;
}

function webstar_absolute_url(string $path = ''): string
{
    $config = webstar_config();
    $host = (string) ($_SERVER['HTTP_HOST'] ?? $config['domain']);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

    return $scheme . '://' . $host . webstar_url($path);
}

function webstar_is_local_host(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));

    return str_contains($host, 'localhost')
        || str_starts_with($host, '127.0.0.1')
        || str_ends_with($host, '.local');
}

function webstar_packages(): array
{
    static $packages = null;
    if ($packages === null) {
        $packages = require __DIR__ . '/packages-data.php';
    }

    return $packages;
}

function webstar_package_by_slug(string $slug): ?array
{
    $packages = webstar_packages();

    return $packages[$slug] ?? null;
}

/**
 * Ordered feature keys => human labels for the comparison matrix.
 *
 * @return array<string, string>
 */
function webstar_feature_labels(): array
{
    return [
        'pages' => 'Website pages',
        'contact_form' => 'Contact form',
        'call_book_buttons' => 'Call / book now buttons',
        'booking_integration' => 'Calendar / booking integration',
        'custom_design' => 'Custom design & content',
        'flyers' => 'Custom flyers',
        'business_card' => 'Business card',
        'letterhead' => 'Custom letterhead',
        'email_signature' => 'Email signature',
        'custom_functionality' => 'Custom functionality',
        'third_party_integrations' => 'Third-party integrations',
    ];
}

function webstar_seo_pages(): array
{
    static $pages = null;
    if ($pages === null) {
        $pages = require __DIR__ . '/seo-pages-data.php';
    }

    return $pages;
}

function webstar_seo_by_slug(string $slug): ?array
{
    foreach (webstar_seo_pages() as $page) {
        if (($page['slug'] ?? '') === $slug) {
            return $page;
        }
    }

    return null;
}

function webstar_seo_landing_url(string $slug): string
{
    return webstar_url(rawurlencode($slug) . '/');
}

/**
 * SEO landing links for footer (service areas vs industries).
 *
 * @return array{areas: list<array{label: string, href: string}>, industries: list<array{label: string, href: string}>}
 */
function webstar_seo_footer_links(): array
{
    $areas = [];
    $industries = [];

    foreach (webstar_seo_pages() as $page) {
        $slug = (string) ($page['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        $label = (string) ($page['footer_label'] ?? $page['h1'] ?? $page['title'] ?? $slug);
        $label = preg_replace('/^Web Design Services for /', '', $label) ?? $label;

        $item = [
            'label' => $label,
            'href' => webstar_seo_landing_url($slug),
        ];

        if (preg_match('#(?:nail-salons|hair-salons|contractors|landscaping)$#', $slug) === 1) {
            $industries[] = $item;
        } else {
            $areas[] = $item;
        }
    }

    return ['areas' => $areas, 'industries' => $industries];
}

/**
 * @return list<array{title:string,url:string,type:string,thumbnail:string,description:string}>
 */
function webstar_examples(): array
{
    static $examples = null;
    if ($examples === null) {
        $examples = require __DIR__ . '/examples-data.php';
    }

    return $examples;
}

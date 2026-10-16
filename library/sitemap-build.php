<?php

declare(strict_types=1);

/**
 * @return list<array{loc: string, lastmod: string, priority: string}>
 */
function webstar_sitemap_entries(): array
{
    $entries = [];
    $add = static function (string $path, string $lastmod, string $priority) use (&$entries): void {
        $entries[] = [
            'loc' => webstar_production_site_url($path),
            'lastmod' => $lastmod,
            'priority' => $priority,
        ];
    };

    $today = gmdate('Y-m-d');
    $mtime = static function (string $file): string {
        $ts = @filemtime($file);

        return $ts ? gmdate('Y-m-d', $ts) : gmdate('Y-m-d');
    };

    $root = dirname(__DIR__);
    $add('', $mtime($root . '/index.php'), '1.0');
    $add('packages/', $mtime($root . '/packages.php'), '0.9');
    $add('plans/', $mtime($root . '/plans.php'), '0.9');
    $add('process/', $mtime($root . '/process.php'), '0.8');
    $add('examples/', $mtime($root . '/examples.php'), '0.7');
    $add('about/', $mtime($root . '/about.php'), '0.7');
    $add('contact/', $mtime($root . '/contact.php'), '0.8');
    $add('service-areas/', $mtime($root . '/service-areas.php'), '0.8');
    $add('industries/', $mtime($root . '/industries.php'), '0.8');
    $add('resources/', $mtime($root . '/resources.php'), '0.8');

    foreach (webstar_packages() as $pkg) {
        $slug = (string) ($pkg['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $add('packages/' . rawurlencode($slug) . '/', $today, '0.85');
    }

    foreach (webstar_seo_pages() as $seo) {
        $slug = (string) ($seo['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $updated = (string) ($seo['updated'] ?? $today);
        $prio = ($seo['type'] ?? '') === 'intent' ? '0.75' : '0.7';
        $add(rawurlencode($slug) . '/', $updated, $prio);
    }

    foreach (webstar_articles() as $article) {
        $slug = (string) ($article['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $updated = (string) ($article['updated'] ?? $article['published'] ?? $today);
        $add('resources/' . rawurlencode($slug) . '/', $updated, '0.65');
    }

    $add('privacy/', $mtime($root . '/privacy.php'), '0.3');
    $add('terms/', $mtime($root . '/terms.php'), '0.3');
    $add('accessibility/', $mtime($root . '/accessibility.php'), '0.3');
    $add('weddings/', $mtime($root . '/weddings/index.php'), '0.5');
    $add('weddings/services/', $mtime($root . '/weddings/services.php'), '0.45');
    $add('weddings/examples/', $mtime($root . '/weddings/examples.php'), '0.45');

    return $entries;
}

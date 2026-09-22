<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
header('Content-Type: application/xml; charset=UTF-8');
$urls = [
    'index.php', 'packages.php', 'examples.php', 'about.php', 'contact.php', 'privacy.php', 'terms.php',
];
foreach (webstar_packages() as $pkg) {
    $urls[] = 'package.php?slug=' . rawurlencode($pkg['slug']);
}
foreach (webstar_seo_pages() as $seo) {
    $urls[] = 'landing.php?slug=' . rawurlencode((string) $seo['slug']);
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach (array_unique($urls) as $path) {
    echo '  <url><loc>' . webstar_h(webstar_absolute_url($path)) . '</loc></url>' . "\n";
}
echo '</urlset>';

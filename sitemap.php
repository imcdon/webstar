<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
header('Content-Type: application/xml; charset=UTF-8');
$urls = [
    webstar_absolute_url(''),
    webstar_absolute_url('packages.php'),
    webstar_absolute_url('examples.php'),
    webstar_absolute_url('about.php'),
    webstar_absolute_url('contact.php'),
    webstar_absolute_url('privacy.php'),
    webstar_absolute_url('terms.php'),
];
foreach (webstar_packages() as $pkg) {
    $urls[] = webstar_absolute_url('packages/' . rawurlencode((string) $pkg['slug']) . '/');
}
foreach (webstar_seo_pages() as $seo) {
    $urls[] = webstar_absolute_url(rawurlencode((string) $seo['slug']) . '/');
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach (array_unique($urls) as $loc) {
    echo '  <url><loc>' . webstar_h($loc) . '</loc></url>' . "\n";
}
echo '</urlset>';

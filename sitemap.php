<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/sitemap-build.php';

header('Content-Type: application/xml; charset=UTF-8');

$entries = webstar_sitemap_entries();
$seen = [];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($entries as $entry) {
    $loc = (string) $entry['loc'];
    if (isset($seen[$loc])) {
        continue;
    }
    $seen[$loc] = true;
    echo '  <url>';
    echo '<loc>' . webstar_h($loc) . '</loc>';
    echo '<lastmod>' . webstar_h((string) $entry['lastmod']) . '</lastmod>';
    echo '<priority>' . webstar_h((string) $entry['priority']) . '</priority>';
    echo '</url>' . "\n";
}
echo '</urlset>';

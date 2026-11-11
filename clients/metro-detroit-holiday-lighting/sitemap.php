<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=UTF-8');

require_once __DIR__ . '/includes/landing-pages.php';

$urls = [
    ['loc' => absolute_url(''), 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['loc' => absolute_url('gallery/'), 'changefreq' => 'weekly', 'priority' => '0.8'],
    ['loc' => absolute_url('contact/'), 'changefreq' => 'monthly', 'priority' => '0.9'],
    ['loc' => absolute_url('service-areas/'), 'changefreq' => 'monthly', 'priority' => '0.9'],
];

foreach (array_keys(mdhl_landing_pages()) as $slug) {
    $urls[] = [
        'loc' => absolute_url($slug . '/'),
        'changefreq' => 'monthly',
        'priority' => '0.9',
    ];
}

foreach (mdhl_cities() as $city) {
    $urls[] = [
        'loc' => absolute_url('service-areas/' . $city['slug'] . '/'),
        'changefreq' => 'monthly',
        'priority' => '0.8',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $entry): ?>
  <url>
    <loc><?= htmlspecialchars($entry['loc']) ?></loc>
    <changefreq><?= htmlspecialchars($entry['changefreq']) ?></changefreq>
    <priority><?= htmlspecialchars($entry['priority']) ?></priority>
  </url>
<?php endforeach; ?>
</urlset>

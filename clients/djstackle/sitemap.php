<?php

declare(strict_types=1);

require __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$urls = [
    ['loc' => absolute_url(), 'priority' => '1.0'],
    ['loc' => absolute_url('shop/'), 'priority' => '0.9'],
    ['loc' => absolute_url('about/'), 'priority' => '0.6'],
    ['loc' => absolute_url('contact/'), 'priority' => '0.6'],
    ['loc' => absolute_url('faq/'), 'priority' => '0.5'],
    ['loc' => absolute_url('gift-cards/'), 'priority' => '0.5'],
    ['loc' => absolute_url('shipping-returns/'), 'priority' => '0.4'],
    ['loc' => absolute_url('privacy/'), 'priority' => '0.3'],
    ['loc' => absolute_url('terms/'), 'priority' => '0.3'],
];

foreach ($shopCatalog->search(['per_page' => 200])['items'] as $p) {
    $urls[] = [
        'loc' => absolute_url('product/?slug=' . rawurlencode($p['slug'])),
        'priority' => '0.8',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc><priority>' . $u['priority'] . '</priority></url>' . "\n";
}
echo '</urlset>';

<?php
/*
 * sitemap.php - XML sitemap for https://superioriceadventures.com
 */
require __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles.php';

header('Content-Type: application/xml; charset=UTF-8');

$pages = [
    '',
    'about/',
    'gallery/',
    'articles/',
    'faq/',
    'contact/',
    'inland-splake-fishing/',
    'lake-superior-burbot/',
    'lake-superior-lake-trout/',
    'ice-shack-rentals/',
];

$urls = [];
foreach ($pages as $page) {
    $urls[] = [
        'loc'     => absolute_url($page),
        'changefreq' => $page === '' || $page === 'articles/' ? 'weekly' : 'monthly',
        'priority'   => $page === '' ? '1.0' : ($page === 'contact/' || str_contains($page, 'fishing') || str_contains($page, 'burbot') || str_contains($page, 'trout') || str_contains($page, 'shack') ? '0.9' : '0.7'),
    ];
}

try {
    $articles = get_published_articles();
    foreach ($articles as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            continue;
        }
        $urls[] = [
            'loc'        => absolute_url('articles/' . $slug . '/'),
            'changefreq' => 'monthly',
            'priority'   => '0.6',
            'lastmod'    => !empty($article['updated_at'])
                ? date('Y-m-d', strtotime($article['updated_at']))
                : (!empty($article['published_at']) ? date('Y-m-d', strtotime($article['published_at'])) : null),
        ];
    }
} catch (Throwable $e) {
    // DB unavailable — still emit static pages
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $entry): ?>
  <url>
    <loc><?= htmlspecialchars($entry['loc']) ?></loc>
<?php if (!empty($entry['lastmod'])): ?>
    <lastmod><?= htmlspecialchars($entry['lastmod']) ?></lastmod>
<?php endif; ?>
    <changefreq><?= htmlspecialchars($entry['changefreq']) ?></changefreq>
    <priority><?= htmlspecialchars($entry['priority']) ?></priority>
  </url>
<?php endforeach; ?>
</urlset>

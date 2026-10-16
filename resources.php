<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/article-helpers.php';

$currentPage = 'resources';
$pageTitle = 'Guides & Resources — Michigan Small Business Websites | Webstar';
$pageDescription = 'Practical guides on website cost, Google Business Profile, launch checklists, and Wix alternatives for Metro Detroit and Michigan small businesses.';
$canonicalPath = 'resources/';

$articles = webstar_articles_sorted();

$itemList = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Webstar resources and guides',
    'itemListElement' => [],
];
$pos = 1;
foreach ($articles as $article) {
    $slug = (string) ($article['slug'] ?? '');
    if ($slug === '') {
        continue;
    }
    $itemList['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $pos++,
        'name' => (string) ($article['title'] ?? $slug),
        'url' => webstar_production_site_url('resources/' . rawurlencode($slug) . '/'),
    ];
}
$jsonLd = json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Guides &amp; resources</h1>
        <p class="home-section__textbox">Straightforward articles on pricing, Google visibility, and launch prep — written for Michigan owners who want a site they control, not agency fluff. New posts appear here and in the <a href="<?php echo webstar_h(webstar_url('resources/feed/')); ?>">RSS feed</a>.</p>
        <ul class="seo-hub-grid">
            <?php foreach ($articles as $article) :
                $slug = (string) ($article['slug'] ?? '');
                ?>
                <li class="seo-hub-card">
                    <a href="<?php echo webstar_h(webstar_article_url($slug)); ?>">
                        <span class="seo-hub-card__title"><?php echo webstar_h((string) ($article['title'] ?? $slug)); ?></span>
                        <span class="seo-hub-card__meta"><?php echo webstar_h((string) ($article['excerpt'] ?? $article['meta_description'] ?? '')); ?></span>
                        <?php if (!empty($article['published'])) : ?>
                            <span class="seo-hub-card__date"><?php echo webstar_h(date('F j, Y', strtotime((string) $article['published']))); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="home-section__textbox">
            <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Compare packages</a> ·
            <a href="<?php echo webstar_h(webstar_url('service-areas.php')); ?>">Service areas</a> ·
            <a href="<?php echo webstar_h(webstar_url('industries.php')); ?>">Industries</a>
        </p>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

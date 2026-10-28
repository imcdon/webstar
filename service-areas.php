<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';

$currentPage = '';
$pageTitle = 'Web Design Service Areas in Metro Detroit | Webstar';
$pageDescription = 'City and regional web design service pages for Metro Detroit and Michigan small businesses. Clear packages, Google setup included.';
$canonicalPath = 'service-areas/';

$pages = array_merge(
    webstar_seo_by_type('region'),
    webstar_seo_by_type('city')
);

$itemList = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Web design service areas',
    'itemListElement' => [],
];
$pos = 1;
foreach ($pages as $p) {
    $itemList['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $pos++,
        'name' => (string) ($p['h1'] ?? $p['title']),
        'url' => webstar_production_site_url(rawurlencode((string) $p['slug']) . '/'),
    ];
}
$jsonLd = json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Service areas</h1>
        <p class="home-section__textbox">Local landing pages for Metro Detroit cities, counties, and statewide Michigan searches. Each page maps to the same clear website packages—pick yours and book a free consultation when ready.</p>
        <ul class="seo-hub-grid">
            <?php foreach ($pages as $p) : ?>
                <li class="seo-hub-card">
                    <a href="<?php echo webstar_h(webstar_seo_landing_url((string) $p['slug'])); ?>">
                        <span class="seo-hub-card__title"><?php echo webstar_h((string) ($p['h1'] ?? $p['title'])); ?></span>
                        <span class="seo-hub-card__meta"><?php echo webstar_h((string) ($p['meta_description'] ?? '')); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('industries.php')); ?>">Browse industry pages</a> · <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Compare packages</a></p>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

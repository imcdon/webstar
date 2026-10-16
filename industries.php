<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';

$currentPage = '';
$pageTitle = 'Web Design by Industry — Michigan Small Business | Webstar';
$pageDescription = 'Industry-specific website pages for restaurants, contractors, salons, and more. Packages include Google Business Profile and Analytics setup.';
$canonicalPath = 'industries/';

$pages = webstar_seo_by_type('industry');

$itemList = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Web design by industry',
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
        <h1 class="home-section__heading">Industries we build for</h1>
        <p class="home-section__textbox">Trade- and vertical-specific pages explain deliverables, booking paths, and Google setup for your type of business—not generic agency fluff.</p>
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
        <p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('service-areas.php')); ?>">Service areas</a> · <a href="<?php echo webstar_h(webstar_url('resources.php')); ?>">Guides &amp; resources</a></p>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

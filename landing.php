<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$slug = trim((string) ($_GET['slug'] ?? ''));
$page = webstar_seo_by_slug($slug);
if ($page === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}
$currentPage = '';
$headerVariant = 'landing';
$pageTitle = (string) $page['title'] . ' — Webstar Business Services';
$pageDescription = (string) $page['meta_description'];
$canonicalPath = rawurlencode($slug) . '/';
$recommended = webstar_package_by_slug((string) ($page['package_slug'] ?? ''));
$landingUrl = webstar_absolute_url($canonicalPath);
$h1 = (string) $page['h1'];

$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => webstar_absolute_url(''),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Packages',
            'item' => webstar_absolute_url('packages/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $h1,
            'item' => $landingUrl,
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$faqs = is_array($page['faq'] ?? null) ? $page['faq'] : [];
$relatedSlugs = is_array($page['related_slugs'] ?? null) ? $page['related_slugs'] : [];

include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
    <article class="landing-content">
        <nav class="landing-breadcrumbs" aria-label="Breadcrumb">
            <ol class="landing-breadcrumbs__list">
                <li><a href="<?php echo webstar_h(webstar_url('')); ?>">Home</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Packages</a></li>
                <li aria-current="page"><?php echo webstar_h($h1); ?></li>
            </ol>
        </nav>
        <h1><?php echo webstar_h($h1); ?></h1>
        <?php foreach ($page['sections'] as $section) : ?>
            <h2><?php echo webstar_h((string) $section['heading']); ?></h2>
            <p><?php echo webstar_h((string) $section['body']); ?></p>
        <?php endforeach; ?>

        <?php if ($faqs !== []) : ?>
            <h2>FAQ</h2>
            <div class="faq-list landing-faq">
                <?php foreach ($faqs as $faq) :
                    $q = (string) ($faq['q'] ?? '');
                    $a = (string) ($faq['a'] ?? '');
                    if ($q === '' || $a === '') {
                        continue;
                    }
                    ?>
                    <details>
                        <summary><?php echo webstar_h($q); ?></summary>
                        <p><?php echo webstar_h($a); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($recommended !== null) :
            $featured = ($recommended['badge'] ?? '') !== '';
            $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
            ?>
            <div class="<?php echo webstar_h($cardClass); ?>" style="margin-top:2rem;">
                <?php if ($featured) : ?>
                    <span class="package-card__badge"><?php echo webstar_h((string) $recommended['badge']); ?></span>
                <?php endif; ?>
                <h2 class="package-card__title">Recommended: <?php echo webstar_h($recommended['title']); ?></h2>
                <p class="package-card__price"><?php echo webstar_h($recommended['price']); ?></p>
                <p class="package-card__meta">Turnaround: <?php echo webstar_h($recommended['turnaround']); ?></p>
                <p class="package-card__summary"><?php echo webstar_h($recommended['summary']); ?></p>
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url($recommended['slug'], 'intake')); ?>">Start intake form</a>
            </div>
        <?php endif; ?>

        <?php
        $relatedPages = [];
        foreach ($relatedSlugs as $relatedSlug) {
            $related = webstar_seo_by_slug((string) $relatedSlug);
            if ($related !== null) {
                $relatedPages[] = $related;
            }
        }
        if ($relatedPages !== []) :
            ?>
            <h2>Related pages</h2>
            <ul class="landing-related">
                <?php foreach ($relatedPages as $related) : ?>
                    <li>
                        <a href="<?php echo webstar_h(webstar_seo_landing_url((string) $related['slug'])); ?>">
                            <?php echo webstar_h((string) ($related['h1'] ?? $related['title'])); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p class="landing-content__footer-links"><a href="<?php echo webstar_h(webstar_url('')); ?>">Webstar home</a> · <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">All packages</a> · <a href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Contact</a></p>
    </article>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

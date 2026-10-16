<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/landing-helpers.php';

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
$ogImagePath = (string) ($page['hero_image'] ?? 'assets/images/og-default.jpg');
$recommended = webstar_package_by_slug((string) ($page['package_slug'] ?? ''));
$landingUrl = webstar_production_site_url($canonicalPath);
$h1 = (string) $page['h1'];
$type = (string) ($page['type'] ?? 'region');
$hub = webstar_seo_hub_for_type($type);
$intro = trim((string) ($page['intro'] ?? ''));
$faqs = is_array($page['faq'] ?? null) ? $page['faq'] : [];
$relatedSlugs = is_array($page['related_slugs'] ?? null) ? $page['related_slugs'] : [];

$ldGraphs = webstar_landing_json_ld($page, $landingUrl, $h1);
$jsonLd = json_encode(
    count($ldGraphs) === 1 ? $ldGraphs[0] : $ldGraphs,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
    <article class="landing-content">
        <nav class="landing-breadcrumbs" aria-label="Breadcrumb">
            <ol class="landing-breadcrumbs__list">
                <li><a href="<?php echo webstar_h(webstar_url('')); ?>">Home</a></li>
                <li><a href="<?php echo webstar_h(webstar_url(match ($type) {
                    'industry' => 'industries.php',
                    'intent' => 'resources.php',
                    default => 'service-areas.php',
                })); ?>"><?php echo webstar_h($hub['label']); ?></a></li>
                <li aria-current="page"><?php echo webstar_h($h1); ?></li>
            </ol>
        </nav>

        <header class="landing-hero">
            <?php
            $heroImage = trim((string) ($page['hero_image'] ?? ''));
            if ($heroImage !== '') :
                ?>
                <img class="landing-hero__image" src="<?php echo webstar_h(webstar_url($heroImage)); ?>" alt="" width="1200" height="630" loading="eager">
            <?php endif; ?>
            <h1><?php echo webstar_h($h1); ?></h1>
            <?php if ($intro !== '') : ?>
                <p class="landing-hero__intro"><?php echo webstar_h($intro); ?></p>
            <?php endif; ?>
            <div class="landing-hero__actions hero__actions">
                <?php if ($recommended !== null) : ?>
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url($recommended['slug'], 'intake')); ?>">Start intake form</a>
                <?php endif; ?>
                <?php if (webstar_phone_display() !== '') : ?>
                    <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_phone_href()); ?>">Call <?php echo webstar_h(webstar_phone_display()); ?></a>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($recommended !== null && is_array($recommended['includes'] ?? null)) : ?>
            <section class="landing-includes">
                <h2>What&apos;s included in <?php echo webstar_h($recommended['title']); ?></h2>
                <ul class="landing-includes__list">
                    <?php foreach ($recommended['includes'] as $line) : ?>
                        <li><?php echo webstar_h((string) $line); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

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

        <div class="landing-cta-band">
            <?php if ($recommended !== null) : ?>
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url($recommended['slug'], 'intake')); ?>">Start <?php echo webstar_h($recommended['title']); ?> intake</a>
            <?php endif; ?>
            <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Ask a question</a>
        </div>

        <p class="landing-content__footer-links">
            <a href="<?php echo webstar_h(webstar_url('')); ?>">Webstar home</a> ·
            <a href="<?php echo webstar_h(webstar_url($hub['path'])); ?>"><?php echo webstar_h($hub['label']); ?></a> ·
            <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">All packages</a>
        </p>
    </article>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

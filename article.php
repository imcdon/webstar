<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/article-helpers.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$article = webstar_article_by_slug($slug);
if ($article === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$bodyPath = webstar_article_body_path($slug);
if (!is_readable($bodyPath)) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$currentPage = 'resources';
$headerVariant = 'landing';
$title = (string) ($article['title'] ?? $slug);
$pageTitle = $title . ' | Webstar';
$pageDescription = (string) ($article['meta_description'] ?? '');
$canonicalPath = 'resources/' . rawurlencode($slug) . '/';
$articleUrl = webstar_production_site_url($canonicalPath);

$relatedPackageSlug = (string) ($article['related_package'] ?? '');
$recommended = webstar_offer_by_slug($relatedPackageSlug);
if ($recommended === null) {
    $recommended = webstar_package_by_slug($relatedPackageSlug);
}

$relatedLandings = [];
foreach ((array) ($article['related_landings'] ?? []) as $relatedSlug) {
    $landing = webstar_seo_by_slug((string) $relatedSlug);
    if ($landing !== null) {
        $relatedLandings[] = $landing;
    }
}

$faqs = is_array($article['faq'] ?? null) ? $article['faq'] : [];
$published = (string) ($article['published'] ?? '');
$updated = (string) ($article['updated'] ?? $published);

$ldGraphs = webstar_article_json_ld($article, $articleUrl, $title);
$faqEntities = [];
foreach ($faqs as $faq) {
    $q = trim((string) ($faq['q'] ?? ''));
    $a = trim((string) ($faq['a'] ?? ''));
    if ($q === '' || $a === '') {
        continue;
    }
    $faqEntities[] = [
        '@type' => 'Question',
        'name' => $q,
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
    ];
}
if ($faqEntities !== []) {
    $ldGraphs[] = ['@type' => 'FAQPage', 'mainEntity' => $faqEntities];
}

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
                <li><a href="<?php echo webstar_h(webstar_url('resources.php')); ?>">Resources</a></li>
                <li aria-current="page"><?php echo webstar_h($title); ?></li>
            </ol>
        </nav>

        <header class="article-header">
            <h1><?php echo webstar_h($title); ?></h1>
            <?php if ($published !== '') : ?>
                <p class="article-header__meta">
                    <time datetime="<?php echo webstar_h($published); ?>">Published <?php echo webstar_h(date('F j, Y', strtotime($published))); ?></time>
                    <?php if ($updated !== '' && $updated !== $published) : ?>
                        · <time datetime="<?php echo webstar_h($updated); ?>">Updated <?php echo webstar_h(date('F j, Y', strtotime($updated))); ?></time>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </header>

        <div class="article-body">
            <?php include $bodyPath; ?>
        </div>

        <aside class="article-author" aria-labelledby="article-author-heading">
            <h2 id="article-author-heading" class="article-author__heading">About the author</h2>
            <div class="article-author__inner">
                <a class="article-author__photo-link" href="<?php echo webstar_h(webstar_url('about.php')); ?>">
                    <?php echo webstar_picture('assets/images/ian-pic.JPG', [
                        'alt' => 'Ian McDonnell',
                        'class' => 'article-author__photo',
                        'width' => 120,
                        'height' => 160,
                        'loading' => 'lazy',
                    ]); ?>
                </a>
                <div class="article-author__text">
                    <p><strong><a href="<?php echo webstar_h(webstar_url('about.php')); ?>">Ian McDonnell</a></strong> builds websites for <?php echo webstar_h(webstar_config()['service_area']); ?> through Webstar Business Services LLC — fixed packages from $350–$500, Business Website at $900–$1,800, custom growth builds at $2,500+, or Website as a Service at $150/month.</p>
                </div>
            </div>
        </aside>

        <?php if ($faqs !== []) : ?>
            <h2>Common questions</h2>
            <div class="landing-faq">
                <?php foreach ($faqs as $faq) :
                    $q = trim((string) ($faq['q'] ?? ''));
                    $a = trim((string) ($faq['a'] ?? ''));
                    if ($q === '' || $a === '') {
                        continue;
                    }
                    ?>
                    <details class="landing-faq__item">
                        <summary><?php echo webstar_h($q); ?></summary>
                        <p><?php echo webstar_h($a); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($relatedLandings !== []) : ?>
            <h2>Related pages</h2>
            <ul class="landing-related">
                <?php foreach ($relatedLandings as $related) : ?>
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
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('contact.php#book')); ?>">Book a 1-on-1</a>
            <?php endif; ?>
            <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Ask a question</a>
        </div>

        <p class="landing-content__footer-links">
            <a href="<?php echo webstar_h(webstar_url('resources.php')); ?>">All guides</a> ·
            <a href="<?php echo webstar_h(webstar_url('resources/feed/')); ?>">RSS feed</a> ·
            <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Packages</a>
        </p>
    </article>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

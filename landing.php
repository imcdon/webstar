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
$canonicalPath = 'landing.php?slug=' . rawurlencode($slug);
$recommended = webstar_package_by_slug((string) ($page['package_slug'] ?? ''));
include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
    <article class="landing-content">
        <h1><?php echo webstar_h((string) $page['h1']); ?></h1>
        <?php foreach ($page['sections'] as $section) : ?>
            <h2><?php echo webstar_h((string) $section['heading']); ?></h2>
            <p><?php echo webstar_h((string) $section['body']); ?></p>
        <?php endforeach; ?>
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
                <a class="home-section__btn home-section__btn--primary" href="package.php?slug=<?php echo webstar_h($recommended['slug']); ?>#intake">Start intake form</a>
            </div>
        <?php endif; ?>
        <p style="margin-top:2rem;"><a href="index.php">Webstar Business Services home</a> · <a href="packages.php">All packages</a></p>
    </article>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

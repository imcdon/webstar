<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$slug = trim((string) ($_GET['slug'] ?? ''));
$package = webstar_package_by_slug($slug);
if ($package === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}
$currentPage = 'packages';
$pageTitle = $package['title'] . ' — Webstar Business Services';
$pageDescription = (string) $package['meta_description'];
$canonicalPath = 'package.php?slug=' . rawurlencode($slug);
$intakeStatus = isset($_GET['intake_status']) ? (string) $_GET['intake_status'] : '';
$intakeReason = isset($_GET['intake_reason']) ? (string) $_GET['intake_reason'] : '';
$intakeFields = isset($_GET['intake_fields']) ? (string) $_GET['intake_fields'] : '';
$packageSlug = $package['slug'];
$packageTitle = $package['title'];
$intakeRedirect = webstar_url('package.php') . '?slug=' . rawurlencode($slug);
$featured = ($package['badge'] ?? '') !== '';
$packageHeaderClass = 'package-page__header' . ($featured ? ' package-page__header--featured' : '');
include __DIR__ . '/library/layout-start.php';
?>
<section class="package-page home-section home-section--night">
    <div class="home-section__inner">
        <div class="<?php echo webstar_h($packageHeaderClass); ?>">
            <?php if ($featured) : ?>
                <span class="package-card__badge"><?php echo webstar_h((string) $package['badge']); ?></span>
            <?php endif; ?>

            <div class="package-page__top">
                <div class="package-page__intro">
                    <h1 class="home-section__heading"><?php echo webstar_h($package['title']); ?></h1>
                    <p class="package-page__summary"><?php echo webstar_h($package['summary']); ?></p>
                    <p class="package-page__ideal"><strong>Ideal for:</strong> <?php echo webstar_h($package['ideal_for']); ?></p>
                </div>
                <div class="package-page__facts">
                    <p class="package-page__price"><?php echo webstar_h($package['price']); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($package['turnaround']); ?></p>
                </div>
            </div>

            <h2 class="package-page__list-title">Deliverables</h2>
            <ul class="package-page__deliverables">
                <?php foreach ($package['includes'] as $item) : ?>
                    <li><?php echo webstar_h($item); ?></li>
                <?php endforeach; ?>
            </ul>

            <div class="package-page__actions">
                <a class="home-section__btn home-section__btn--primary" href="#intake">Start intake form</a>
                <a class="home-section__btn home-section__btn--secondary" href="contact.php">Contact</a>
            </div>
            <p class="package-page__compare"><a href="packages.php">Back to all packages</a></p>
        </div>

        <?php include __DIR__ . '/library/package-intake-form.php'; ?>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

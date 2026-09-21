<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'packages';
$pageTitle = 'Service Packages — Webstar Business Services';
$pageDescription = 'Marketing, website, and custom growth packages with clear pricing and turnaround times.';
$canonicalPath = 'packages.php';
$packages = webstar_packages();
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night" id="packages">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Service packages</h1>
        <p class="home-section__textbox">Four clear tiers—from a one-day marketing kit to a fully custom growth build. Select a package to see deliverables below, then start the intake form.</p>

        <div class="package-grid" role="list">
            <?php foreach ($packages as $pkg) :
                $featured = ($pkg['badge'] ?? '') !== '';
                $cardClass = 'package-card package-card--selectable' . ($featured ? ' package-card--featured' : '');
                $includesJson = htmlspecialchars(json_encode(array_values($pkg['includes']), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                ?>
                <button
                    type="button"
                    class="<?php echo webstar_h($cardClass); ?>"
                    role="listitem"
                    data-package-select
                    data-slug="<?php echo webstar_h($pkg['slug']); ?>"
                    data-title="<?php echo webstar_h($pkg['title']); ?>"
                    data-price="<?php echo webstar_h($pkg['price']); ?>"
                    data-turnaround="<?php echo webstar_h($pkg['turnaround']); ?>"
                    data-includes="<?php echo $includesJson; ?>"
                    aria-expanded="false"
                    aria-controls="package-detail"
                >
                    <?php if ($featured) : ?>
                        <span class="package-card__badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                    <?php endif; ?>
                    <h2 class="package-card__title"><?php echo webstar_h($pkg['title']); ?></h2>
                    <p class="package-card__price"><?php echo webstar_h($pkg['price']); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($pkg['turnaround']); ?></p>
                    <p class="package-card__summary"><?php echo webstar_h($pkg['summary']); ?></p>
                    <span class="package-card__hint">View details</span>
                </button>
            <?php endforeach; ?>
        </div>

        <div
            id="package-detail"
            class="package-detail"
            hidden
            data-package-detail
            aria-live="polite"
        >
            <div class="package-detail__inner">
                <h2 class="package-detail__title" data-detail-title></h2>
                <p class="package-detail__price" data-detail-price></p>
                <p class="package-detail__meta" data-detail-turnaround></p>
                <h3 class="package-detail__list-title">Deliverables</h3>
                <ul class="package-card__list" data-detail-includes></ul>
                <div class="package-detail__actions">
                    <a class="home-section__btn home-section__btn--primary" data-detail-intake href="#">Start intake</a>
                    <a class="home-section__btn home-section__btn--secondary" href="contact.php">Contact</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

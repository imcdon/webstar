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
        <p class="home-section__textbox">Four clear tiers—from a one-day marketing kit to a fully custom growth build. Open a package to see deliverables and submit the intake form.</p>

        <div class="package-grid">
            <?php foreach ($packages as $pkg) :
                $featured = ($pkg['badge'] ?? '') !== '';
                $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
                $href = 'package.php?slug=' . rawurlencode((string) $pkg['slug']);
                ?>
                <article class="<?php echo webstar_h($cardClass); ?>">
                    <?php if ($featured) : ?>
                        <span class="package-card__badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                    <?php endif; ?>
                    <h2 class="package-card__title"><?php echo webstar_h($pkg['title']); ?></h2>
                    <p class="package-card__price"><?php echo webstar_h($pkg['price']); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($pkg['turnaround']); ?></p>
                    <p class="package-card__summary"><?php echo webstar_h($pkg['summary']); ?></p>
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($href); ?>">View package &amp; intake</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'packages';
$pageTitle = 'Website & Marketing Packages | Metro Detroit — Webstar Business Services';
$pageDescription = 'Compare Webstar service packages for Metro Detroit and Michigan: marketing from $50, websites from $200, full business builds $500–$1,000+. Clear deliverables and turnaround times.';
$canonicalPath = 'packages.php';
$packages = webstar_packages();
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night" id="packages">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Website &amp; marketing packages</h1>
        <p class="home-section__textbox">One-time project pricing for small businesses in <?php echo webstar_h($config['service_area']); ?>. Open a package to see deliverables, then submit the intake form on that page to start.</p>

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

<div class="cta-band">
    <p>Not sure which tier fits? <a href="contact.php">Send a general question</a> or start with the package closest to your budget—you can clarify scope on the intake form.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="package.php?slug=simple-website">Most popular: Simple Website</a>
        <a class="home-section__btn home-section__btn--secondary" href="examples.php">See examples</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

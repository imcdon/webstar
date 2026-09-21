<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'packages';
$pageTitle = 'Service Packages — Webstar Business Services';
$pageDescription = 'Marketing, website, and custom growth packages with clear pricing and turnaround times.';
$canonicalPath = 'packages.php';
$intakeStatus = isset($_GET['intake_status']) ? (string) $_GET['intake_status'] : '';
$intakeReason = isset($_GET['intake_reason']) ? (string) $_GET['intake_reason'] : '';
$intakeFields = isset($_GET['intake_fields']) ? (string) $_GET['intake_fields'] : '';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night" id="compare">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Service packages</h1>
        <p class="home-section__textbox">Compare every deliverable below, then open a package to submit your intake form (logo, company name, contact info, business summary, and desired services).</p>
        <?php include __DIR__ . '/library/package-comparison.php'; ?>
    </div>
</section>

<section class="home-section home-section--deep">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Package details &amp; intake</h2>
        <?php foreach (webstar_packages() as $pkg) :
            $featured = ($pkg['badge'] ?? '') !== '';
            $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
            ?>
            <article class="<?php echo webstar_h($cardClass); ?>" style="margin-bottom:1.25rem;">
                <?php if ($featured) : ?>
                    <span class="package-card__badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                <?php endif; ?>
                <h2 class="package-card__title"><?php echo webstar_h($pkg['title']); ?></h2>
                <p class="package-card__price"><?php echo webstar_h($pkg['price']); ?> · <?php echo webstar_h($pkg['turnaround']); ?></p>
                <ul class="package-card__list"><?php foreach ($pkg['includes'] as $item) : ?><li><?php echo webstar_h($item); ?></li><?php endforeach; ?></ul>
                <p><a href="package.php?slug=<?php echo webstar_h($pkg['slug']); ?>">Dedicated package page</a></p>
                <?php
                $packageSlug = $pkg['slug'];
                $packageTitle = $pkg['title'];
                $intakeRedirect = 'packages.php';
                include __DIR__ . '/library/package-intake-form.php';
                ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

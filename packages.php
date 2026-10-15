<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'packages';
$pageTitle = 'Website Packages & Pricing | Metro Detroit — Webstar Business Services';
$pageDescription = 'Compare Webstar website packages for Metro Detroit and Michigan: Simple Website $350–$500, Business Website $900–$1,800, Business Growth from $2,500. Google Business Profile and Analytics included in every tier, or $150/mo Website as a Service.';
$canonicalPath = 'packages/';
$packages = webstar_packages();
$listItems = [];
$position = 1;
foreach ($packages as $pkg) {
    $listItems[] = [
        '@type' => 'ListItem',
        'position' => $position,
        'name' => (string) $pkg['title'],
        'url' => webstar_absolute_url('packages/' . rawurlencode((string) $pkg['slug']) . '/'),
    ];
    $position++;
}
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Webstar website packages',
    'itemListElement' => $listItems,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night" id="packages">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Website packages &amp; pricing</h1>
        <p class="home-section__textbox">Three one-time packages for small businesses in <?php echo webstar_h($config['service_area']); ?>. Every tier includes Google Business Profile, Analytics 4, and Search Console setup, a 30-day post-launch fix window, and full ownership of your domain and files. Open a package to see deliverables, then submit the intake form on that page to start.</p>

        <div class="package-grid">
            <?php foreach ($packages as $pkg) :
                $featured = ($pkg['badge'] ?? '') !== '';
                $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
                $href = webstar_package_url((string) $pkg['slug']);
                ?>
                <article class="<?php echo webstar_h($cardClass); ?>">
                    <?php if ($featured) : ?>
                        <span class="package-card__badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                    <?php endif; ?>
                    <h2 class="package-card__title"><?php echo webstar_h($pkg['title']); ?></h2>
                    <p class="package-card__price"><?php echo webstar_h($pkg['price']); ?></p>
                    <p class="package-card__price-note"><?php echo webstar_h((string) ($pkg['price_note'] ?? 'One-time project fee')); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($pkg['turnaround']); ?></p>
                    <p class="package-card__summary"><?php echo webstar_h($pkg['summary']); ?></p>
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($href); ?>">View package &amp; intake</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section home-section--panel" id="compare">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Compare packages</h2>
        <?php include __DIR__ . '/library/package-comparison.php'; ?>
    </div>
</section>

<?php include __DIR__ . '/library/plans-band.php'; ?>

<section class="home-section home-section--night" id="hosting">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Hosting and ownership</h2>
        <div class="faq-list">
            <details>
                <summary>Where will my site be hosted?</summary>
                <p>By default we deploy to a cPanel host in your name (we recommend a short list and set it up with you), so you own the account. If you choose a Care Plan or Website as a Service, the site lives on Webstar’s managed server with daily backups, SSL, and monitoring. Either way your domain is registered to you with Cloudflare DNS, so moving later is a single DNS change plus a full export we provide.</p>
            </details>
            <details>
                <summary>What does the 30-day fix window cover?</summary>
                <p>Anything that was in scope and does not work as delivered — broken links, form errors, layout bugs — fixed at no charge for 30 days after launch. New features and content changes are quoted separately or handled under a Care Plan.</p>
            </details>
            <details>
                <summary>Who owns the Google accounts?</summary>
                <p>You do. We create or claim Google Business Profile, Analytics, and Search Console with your Google account as the owner and add Webstar as a manager only for as long as you want help.</p>
            </details>
            <details>
                <summary>Why ranges instead of one price?</summary>
                <p>Page count, booking or integration complexity, content volume, and brand-kit scope determine where a project falls. We confirm the exact fee in writing on the agreement before any work starts.</p>
            </details>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Not sure which tier fits? <a href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Send a general question</a> or start with the package closest to your budget—you can clarify scope on the intake form.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url('simple-website')); ?>">Most popular: Simple Website</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('plans.php')); ?>">Monthly: Website as a Service</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

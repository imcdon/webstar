<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'home';
$pageTitle = $config['business_name'] . ' — Web Design & Marketing | Metro Detroit';
$pageDescription = 'Responsive websites and marketing packages for Metro Detroit small businesses. Clear pricing, fast turnaround, one-time builds.';
$canonicalPath = 'index.php';
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => $config['business_name'],
    'url' => webstar_absolute_url('index.php'),
    'telephone' => '+1-' . $config['phone_tel'],
    'email' => $config['email'],
    'areaServed' => $config['service_area'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="hero">
    <img class="hero__badge" src="assets/images/star-icon-gold.png" alt="" width="52" height="52">
    <h1 class="hero__title">Web design &amp; marketing that stays fast and simple</h1>
    <p class="hero__lead">Webstar Business Services builds responsive websites and marketing materials for <?php echo webstar_h($config['service_area']); ?>. Clean HTML and CSS, clear packages, and turnaround times you can plan around.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="packages.php">View packages</a>
        <a class="home-section__btn home-section__btn--secondary" href="tel:<?php echo webstar_h($config['phone_tel']); ?>">Call now</a>
    </div>
</section>

<section class="home-section home-section--night" id="packages-preview">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Packages at a glance</h2>
        <p class="home-section__textbox">Four clear tiers—from a one-day marketing kit to a fully custom growth build. Compare the full feature matrix below.</p>
        <div class="package-grid">
            <?php foreach (webstar_packages() as $pkg) :
                $featured = ($pkg['badge'] ?? '') !== '';
                $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
                ?>
                <article class="<?php echo webstar_h($cardClass); ?>">
                    <?php if ($featured) : ?>
                        <span class="package-card__badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                    <?php endif; ?>
                    <h3 class="package-card__title"><?php echo webstar_h($pkg['title']); ?></h3>
                    <p class="package-card__price"><?php echo webstar_h($pkg['price']); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($pkg['turnaround']); ?></p>
                    <p class="package-card__summary"><?php echo webstar_h($pkg['summary']); ?></p>
                    <a class="home-section__btn home-section__btn--primary" href="package.php?slug=<?php echo webstar_h($pkg['slug']); ?>">Details &amp; intake</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section home-section--deep" id="compare">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Compare packages</h2>
        <p class="home-section__textbox">See what each tier includes side by side—pages, booking, marketing materials, and more.</p>
        <?php include __DIR__ . '/library/package-comparison.php'; ?>
    </div>
</section>

<section class="home-section home-section--panel" id="process">
    <div class="home-section__inner">
        <h2 class="home-section__heading">How it works</h2>
        <ol class="home-section__textbox">
            <li>Choose a package and submit the intake form (logo, company info, business summary, desired services).</li>
            <li>We confirm scope, timeline, and any clarifying questions.</li>
            <li>We deliver designs and your site or marketing files within the listed turnaround time.</li>
        </ol>
    </div>
</section>

<section class="home-section home-section--night" id="faq">
    <div class="home-section__inner">
        <h2 class="home-section__heading">FAQ</h2>
        <div class="faq-list">
            <details><summary>Do I own my website?</summary><p>Yes. You own your hosting and domain accounts. We deliver a site built with standard HTML/CSS (and PHP where needed for forms).</p></details>
            <details><summary>Are there monthly fees to Webstar?</summary><p>Packages are priced as projects. Hosting and third-party tools bill separately through those providers unless you arrange ongoing support.</p></details>
            <details><summary>What affects the $500–$1,000 business package price?</summary><p>Page count, booking integration complexity, content volume, and marketing collateral scope determine where your project falls in that range.</p></details>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Ready to start? Talk to Ian or submit an intake form.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="contact.php">Contact</a>
        <a class="home-section__btn home-section__btn--secondary" href="tel:<?php echo webstar_h($config['phone_tel']); ?>">Call</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

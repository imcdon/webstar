<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'home';
$pageTitle = 'Affordable Web Design & Marketing | Metro Detroit — ' . $config['business_name'];
$pageDescription = 'One-time website and marketing packages for Metro Detroit and Michigan small businesses. Own your domain and hosting—clear pricing from $50, fast turnaround, custom HTML/CSS builds.';
$canonicalPath = '';
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => $config['business_name'],
    'url' => webstar_absolute_url(''),
    'email' => $config['email'],
    'image' => webstar_absolute_url('assets/images/star-icon-gold.png'),
    'description' => $pageDescription,
    'areaServed' => $config['service_area'],
    'priceRange' => '$50–$1000+',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="hero">
    <img class="hero__badge" src="<?php echo webstar_h(webstar_url('assets/images/star-icon-gold.png')); ?>" alt="" width="52" height="52">
    <h1 class="hero__title">Affordable websites and marketing for Metro Detroit businesses</h1>
    <p class="hero__lead">Skip subscription page builders and surprise monthly fees. Webstar delivers one-time, custom websites and marketing packages—you own your domain, hosting, and files.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View packages &amp; pricing</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Questions? Contact</a>
    </div>
</section>

<section class="home-section home-section--night" id="packages-preview">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Packages at a glance</h2>
        <p class="home-section__textbox">Four clear tiers—from a $50 marketing kit to a fully custom growth build. Serving <?php echo webstar_h($config['service_area']); ?>.</p>
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
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url((string) $pkg['slug'])); ?>">View package &amp; intake</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section home-section--panel" id="process">
    <div class="home-section__inner">
        <h2 class="home-section__heading">How it works</h2>
        <ol class="home-section__textbox">
            <li>Choose a package and submit the intake form (logo, company name, business summary, goals, and desired services).</li>
            <li>Schedule a call with Ian to confirm timeline, scope, and any questions.</li>
            <li>Pay the deposit and track progress in the Client Portal as your site or deliverables take shape.</li>
            <li>You buy your domain and hosting, pay the remaining balance, and receive your files deployed on your independent hosting.</li>
        </ol>
        <p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Compare all packages</a> or <a href="<?php echo webstar_h(webstar_url('examples.php')); ?>">see client examples</a>.</p>
    </div>
</section>

<section class="home-section home-section--night" id="faq">
    <div class="home-section__inner">
        <h2 class="home-section__heading">FAQ</h2>
        <div class="faq-list">
            <details><summary>Do I own my website?</summary><p>Yes. You own your hosting and domain accounts. We deliver a site built with standard HTML/CSS (and PHP where needed for forms).</p></details>
            <details><summary>Are there monthly fees to Webstar?</summary><p>Packages are priced as projects. Hosting and third-party tools bill separately through those providers unless you arrange ongoing support.</p></details>
            <details><summary>What affects the $500–$1,000 business package price?</summary><p>Page count, booking integration complexity, content volume, and marketing collateral scope determine where your project falls in that range.</p></details>
            <details><summary>How do I start a project?</summary><p>Open the package that fits your needs, scroll to the intake form, and submit your business details. That starts the official project queue—not the general contact form.</p></details>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Ready to start? Pick a package and submit the intake form—we’ll follow up to confirm scope and timeline.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View packages</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">General questions</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

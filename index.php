<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'home';
$pageTitle = $config['business_name'] . ' — Web Design & Marketing | Metro Detroit';
$pageDescription = 'Low-cost, simple services for your business, group, or organization. Own your domain and hosting—custom products without the custom cost.';
$canonicalPath = 'index.php';
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => $config['business_name'],
    'url' => webstar_absolute_url('index.php'),
    'email' => $config['email'],
    'areaServed' => $config['service_area'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="hero">
    <img class="hero__badge" src="assets/images/star-icon-gold.png" alt="" width="52" height="52">
    <h1 class="hero__title">Low-Cost, Simple Services for Your Business, Group, or Organization.</h1>
    <p class="hero__lead">Ditch high-cost, subscription based, theme-based websites and marketing packages. Own your domain, your hosting, and get custom products without the custom cost.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="packages.php">View packages</a>
        <a class="home-section__btn home-section__btn--secondary" href="contact.php">Contact</a>
    </div>
</section>

<section class="home-section home-section--night" id="packages-preview">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Packages at a glance</h2>
        <p class="home-section__textbox">Four clear tiers—from a one-day marketing kit to a fully custom growth build.</p>
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

<section class="home-section home-section--panel" id="process">
    <div class="home-section__inner">
        <h2 class="home-section__heading">How it works</h2>
        <ol class="home-section__textbox">
            <li>Choose a package and submit the intake form (logo, company name, company info, business summary, goals, and desired services.)</li>
            <li>Schedule a call with Ian to confirm a timeline, your desired services, and any questions.</li>
            <li>Pay the deposit and see your website or deliverables update in real time in the Client Portal.</li>
            <li>You buy your domain and hosting, pay the remaining balance, then I transfer your site and deliverables to you and deploy your site on your independent hosting.</li>
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
        <a class="home-section__btn home-section__btn--secondary" href="packages.php">View packages</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

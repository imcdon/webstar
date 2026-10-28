<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'home';
$pageTitle = 'Web Design & Marketing for Metro Detroit | Webstar';
$pageDescription = 'Custom websites for Metro Detroit small businesses with Google Business Profile and Analytics included. Book a free 1-on-1 consultation. Pay once and own it, or $150/month Website as a Service.';
$canonicalPath = '';
$preloadImages = [
    ['path' => 'assets/images/hero-home-768.webp', 'media' => '(max-width: 767px)'],
    ['path' => 'assets/images/hero-home.webp', 'media' => '(min-width: 768px)'],
];

$homeFaqs = [
    [
        'q' => 'Do I own my website?',
        'a' => 'Yes. Your domain is registered in your name and you own the site files on full payment (or after month 6 on Website as a Service). We build with standard HTML/CSS and PHP, so any host can run it.',
    ],
    [
        'q' => 'Are there monthly fees to Webstar?',
        'a' => 'Only if you choose them. Website packages are one-time fees and we hand off to hosting you own. An optional Care Plan ($50/mo) covers hosting, backups, updates, edits, and reporting, and Website as a Service bundles everything for $150/mo with a 6-month minimum.',
    ],
    [
        'q' => 'Why are prices shown as ranges?',
        'a' => 'Page count, booking or integration complexity, content volume, and brand-kit scope determine where your project falls. We confirm the exact fee in writing before any work starts.',
    ],
    [
        'q' => 'What Google services are included?',
        'a' => 'Every website tier includes Google Business Profile setup or claim, Google Analytics 4 with call and form click tracking, and Search Console — with you as the owner of each account.',
    ],
    [
        'q' => 'How do I start?',
        'a' => 'Book a free 1-on-1 consultation. On that call we confirm scope, timeline, and the exact fee in writing before any agreement or payment.',
    ],
];

$businessLd = [
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => $config['business_name'],
    'url' => webstar_absolute_url(''),
    'email' => $config['email'],
    'image' => webstar_absolute_url('assets/images/star-icon-gold.png'),
    'description' => $pageDescription,
    'areaServed' => $config['service_area'],
    'priceRange' => '$350–$2500+',
];
$phoneE164 = webstar_phone_e164();
if ($phoneE164 !== '') {
    $businessLd['telephone'] = $phoneE164;
}
$sameAs = array_values(array_filter(array_map('strval', $config['sameAs'] ?? [])));
if ($sameAs !== []) {
    $businessLd['sameAs'] = $sameAs;
}

$faqLd = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static function (array $item): array {
        return [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['a'],
            ],
        ];
    }, $homeFaqs),
];

$jsonLd = json_encode([$businessLd, $faqLd], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="hero">
    <img class="hero__badge" src="<?php echo webstar_h(webstar_url('assets/images/star-icon-gold.png')); ?>" alt="" width="52" height="52">
    <h1 class="hero__title">Websites that get Metro Detroit businesses found on Google</h1>
    <p class="hero__lead">Custom-built sites with Google Business Profile, Analytics, and Search Console included in every tier. Pay once and own it outright, or go month-to-month with Website as a Service for $150/mo. Either way, your domain and files are yours.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('contact.php#book')); ?>">Book a free 1-on-1</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View packages &amp; pricing</a>
    </div>
</section>

<?php
require_once __DIR__ . '/library/as-seen-on.php';
include __DIR__ . '/library/social-proof-band.php';
?>

<section class="home-section home-section--night" id="packages-preview">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Three website packages</h2>
        <p class="home-section__textbox">Every tier includes Google Business Profile, Analytics 4, and Search Console setup, a 30-day post-launch fix window, and full ownership. Serving <?php echo webstar_h($config['service_area']); ?>.</p>
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
                    <p class="package-card__price-note"><?php echo webstar_h((string) ($pkg['price_note'] ?? 'One-time project fee')); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($pkg['turnaround']); ?></p>
                    <p class="package-card__summary"><?php echo webstar_h($pkg['summary']); ?></p>
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url((string) $pkg['slug'])); ?>">View package</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/library/plans-band.php'; ?>

<section class="home-section home-section--night" id="process">
    <div class="home-section__inner">
        <h2 class="home-section__heading">How it works</h2>
        <ol class="home-section__textbox">
            <li>Book a free 1-on-1 consultation. Company details, what you want to talk about, and a logo are optional on the booking form.</li>
            <li>On the call, Ian confirms scope, timeline, and the exact fee in writing.</li>
            <li>Sign the agreement, pay the deposit (or first monthly payment), and track progress in the Client Portal.</li>
            <li>Review the build on a private staging link. We launch after your approval and the balance clears.</li>
            <li>We set up Google Business Profile, Analytics, and Search Console in your name, then hand off to hosting you own — or keep it maintained under a Care Plan.</li>
        </ol>
        <p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('process.php')); ?>">See the full process</a>, <a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">compare all packages</a>, or <a href="<?php echo webstar_h(webstar_url('examples.php')); ?>">see client examples</a>.</p>
    </div>
</section>

<section class="home-section home-section--night" id="explore">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Explore by city, industry, or guide</h2>
        <p class="home-section__textbox">Local service pages and practical guides help Google and customers find the right starting point. Pick a hub, then open the page that matches your search.</p>
        <p class="home-section__textbox">
            <a href="<?php echo webstar_h(webstar_url('service-areas/')); ?>">Service areas</a>
            · <a href="<?php echo webstar_h(webstar_url('industries/')); ?>">Industries</a>
            · <a href="<?php echo webstar_h(webstar_url('resources/')); ?>">Resources</a>
            · <a href="<?php echo webstar_h(webstar_url('web-design-services-metro-detroit/')); ?>">Metro Detroit web design</a>
        </p>
    </div>
</section>

<section class="home-section home-section--night" id="faq">
    <div class="home-section__inner">
        <h2 class="home-section__heading">FAQ</h2>
        <div class="faq-list">
            <?php foreach ($homeFaqs as $faq) : ?>
                <details>
                    <summary><?php echo webstar_h($faq['q']); ?></summary>
                    <p><?php echo webstar_h($faq['a']); ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Ready to talk? Book a free 1-on-1 and we’ll confirm scope and price on the call.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('contact.php#book')); ?>">Book a free 1-on-1</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php#inquiry')); ?>">General questions</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

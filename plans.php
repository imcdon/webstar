<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'plans';
$waas = webstar_waas();
$carePlans = webstar_care_plans();
$pageTitle = 'Website as a Service & Care Plans | Metro Detroit — Webstar Business Services';
$pageDescription = (string) ($waas['meta_description'] ?? 'Website as a Service for $150 per month and optional Care Plans for hosting, backups, updates, and reporting.');
$canonicalPath = 'plans/';

$intakeStatus = isset($_GET['intake_status']) ? (string) $_GET['intake_status'] : '';
$intakeReason = isset($_GET['intake_reason']) ? (string) $_GET['intake_reason'] : '';
$intakeFields = isset($_GET['intake_fields']) ? (string) $_GET['intake_fields'] : '';
$packageSlug = (string) ($waas['slug'] ?? 'website-as-a-service');
$packageTitle = (string) ($waas['title'] ?? 'Website as a Service');
$intakeRedirect = webstar_url('plans.php');

$planFaqs = is_array($waas['faq'] ?? null) ? $waas['faq'] : [];
$planFaqs[] = [
    'q' => 'Do I have to buy a Care Plan after a one-time website package?',
    'a' => 'No. Handoff to your own hosting is the default and you can run the site yourself. Care Plans are optional and month-to-month for owners who would rather have Webstar handle hosting, backups, updates, and reporting.',
];
$planFaqs[] = [
    'q' => 'Where will my site be hosted?',
    'a' => 'Care Plan and Website as a Service sites live on Webstar’s managed cPanel server with daily backups, SSL, and monitoring. Your domain stays registered in your name with Cloudflare DNS, so moving to your own host later is a single DNS change plus a full export we provide.',
];
$planFaqs[] = [
    'q' => 'Can I cancel a Care Plan?',
    'a' => 'Yes, any time with 30 days’ notice. We give you a complete export of your site and help point your domain at the new host.',
];

$offerLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => (string) $waas['title'],
    'description' => $pageDescription,
    'provider' => [
        '@type' => 'ProfessionalService',
        'name' => $config['business_name'],
        'url' => webstar_absolute_url(''),
    ],
    'areaServed' => $config['service_area'],
    'url' => webstar_absolute_url($canonicalPath),
    'offers' => [
        '@type' => 'Offer',
        'price' => '150',
        'priceCurrency' => 'USD',
        'priceSpecification' => [
            '@type' => 'UnitPriceSpecification',
            'price' => '150',
            'priceCurrency' => 'USD',
            'unitText' => 'MONTH',
        ],
        'availability' => 'https://schema.org/InStock',
    ],
];
$faqLd = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static function (array $item): array {
        return [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ];
    }, $planFaqs),
];
$jsonLd = json_encode([$offerLd, $faqLd], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night" id="waas">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Website as a Service</h1>
        <p class="home-section__textbox">Prefer a monthly payment to a one-time project fee? <?php echo webstar_h((string) $waas['summary']); ?></p>

        <div class="plan-hero">
            <article class="package-card package-card--featured plan-hero__card">
                <span class="package-card__badge"><?php echo webstar_h((string) $waas['price']); ?> / month · <?php echo webstar_h((string) $waas['minimum_term']); ?></span>
                <h2 class="package-card__title"><?php echo webstar_h((string) $waas['title']); ?></h2>
                <p class="package-card__price"><?php echo webstar_h((string) $waas['price']); ?> <span class="plan-price__period"><?php echo webstar_h((string) $waas['period']); ?></span></p>
                <p class="package-card__meta"><?php echo webstar_h((string) $waas['minimum_term']); ?> · Stripe autopay · you own the files after month 6</p>
                <ul class="package-card__list">
                    <?php foreach ($waas['includes'] as $item) : ?>
                        <li><?php echo webstar_h((string) $item); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="package-card__meta"><strong>Ideal for:</strong> <?php echo webstar_h((string) $waas['ideal_for']); ?></p>
                <a class="home-section__btn home-section__btn--primary" href="#intake">Start Website as a Service</a>
            </article>
            <div class="plan-hero__aside">
                <h2 class="plan-hero__aside-title">Or own it outright</h2>
                <p>Every website package is a one-time fee and includes the same Google setup. Pay once, host anywhere, and add a Care Plan only if you want ongoing help.</p>
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Compare one-time packages</a>
            </div>
        </div>
    </div>
</section>

<section class="home-section home-section--panel" id="care-plans">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Care Plans</h2>
        <p class="home-section__textbox">Optional, month-to-month support after any website launch. Cancel with 30 days’ notice and take a full export with you.</p>
        <div class="package-grid">
            <?php foreach ($carePlans as $plan) :
                $featured = ($plan['badge'] ?? '') !== '';
                $cardClass = 'package-card' . ($featured ? ' package-card--featured' : '');
                ?>
                <article class="<?php echo webstar_h($cardClass); ?>">
                    <?php if ($featured) : ?>
                        <span class="package-card__badge"><?php echo webstar_h((string) $plan['badge']); ?></span>
                    <?php endif; ?>
                    <h3 class="package-card__title"><?php echo webstar_h((string) $plan['title']); ?></h3>
                    <p class="package-card__price"><?php echo webstar_h((string) $plan['price']); ?> <span class="plan-price__period"><?php echo webstar_h((string) $plan['period']); ?></span></p>
                    <p class="package-card__summary"><?php echo webstar_h((string) $plan['summary']); ?></p>
                    <ul class="package-card__list">
                        <?php foreach ($plan['includes'] as $item) : ?>
                            <li><?php echo webstar_h((string) $item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>?plan=<?php echo webstar_h((string) $plan['slug']); ?>#inquiry">Ask about <?php echo webstar_h((string) $plan['title']); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="home-section__textbox plan-note">Unused edit minutes do not roll over. Larger changes are quoted separately or handled under a Business Growth change order.</p>
    </div>
</section>

<section class="home-section home-section--night" id="faq">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Plans FAQ</h2>
        <div class="faq-list">
            <?php foreach ($planFaqs as $faq) : ?>
                <details>
                    <summary><?php echo webstar_h($faq['q']); ?></summary>
                    <p><?php echo webstar_h($faq['a']); ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section home-section--panel">
    <div class="home-section__inner">
        <?php include __DIR__ . '/library/package-intake-form.php'; ?>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

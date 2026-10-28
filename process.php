<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'process';
$pageTitle = 'Our Process — From Consultation to Launch | Webstar';
$pageDescription = 'How a Webstar website project runs: a free consultation, design, build, review, launch, Google setup, 30-day fix window, then handoff or a Care Plan.';
$canonicalPath = 'process/';

$steps = [
    [
        'title' => 'Free consultation',
        'when' => 'Day 0',
        'body' => 'Book a free 1-on-1 consultation. On that 20–30 minute call we confirm goals, pages, booking needs, and the exact fee in writing.',
        'you' => ['Logo files and brand colors', 'Business name, phone, hours, service area', 'Photos and any existing copy', 'Booking or scheduling tool you already use'],
        'we' => ['Written scope and fixed price', 'Agreement for eSignature', 'Stripe invoice for the deposit or first month', 'Client Portal login'],
    ],
    [
        'title' => 'Design',
        'when' => 'Week 1',
        'body' => 'We build a mobile-first design on a private staging link using your brand. You review and we revise — two rounds are included in every package.',
        'you' => ['Feedback within 3 business days', 'Approval of the sitemap and layout'],
        'we' => ['Design system (type, color, components)', 'Home page first, then inner pages', 'Copy structure and calls to action'],
    ],
    [
        'title' => 'Build',
        'when' => 'Week 1–2',
        'body' => 'Pages, contact form, call and book buttons, and any booking integration are built and tested on real phones. Business tiers also receive flyers, card, letterhead, and email signature drafts.',
        'you' => ['Final copy approvals', 'Access to booking/calendar tools if in scope'],
        'we' => ['Accessibility basics (WCAG 2.1 AA target)', 'Performance and SEO fundamentals (titles, schema, sitemap)', 'Spam-protected forms that email you'],
    ],
    [
        'title' => 'Review and approval',
        'when' => 'Before launch',
        'body' => 'You walk through the finished site on staging. We fix anything in scope, then you approve launch. For 50/50 projects the balance is invoiced at this point.',
        'you' => ['Final sign-off', 'Balance payment (one-time packages)'],
        'we' => ['Launch checklist: DNS, SSL, redirects, forms, analytics', 'Backup of the final build'],
    ],
    [
        'title' => 'Launch',
        'when' => 'Launch day',
        'body' => 'We deploy to hosting in your name (or to Webstar’s managed server under a Care Plan or Website as a Service), confirm SSL and forms, and submit the sitemap to Google.',
        'you' => ['Hosting account in your name if you are self-hosting (we help you choose and set it up)'],
        'we' => ['Deployment and smoke test', 'Cloudflare DNS on your domain', 'Sitemap submitted to Search Console'],
    ],
    [
        'title' => 'Google setup',
        'when' => 'Launch week',
        'body' => 'Included in every tier: Google Business Profile create or claim and full setup, Analytics 4 with call and form tracking, and Search Console — all owned by your Google account.',
        'you' => ['A Google account for the business', 'Completion of Google’s verification step (postcard, phone, or video)'],
        'we' => ['Profile categories, hours, services, photos, description', 'Analytics and Search Console connected', 'Plain-English first check-in when data arrives'],
    ],
    [
        'title' => '30-day fix window',
        'when' => 'Days 1–30 after launch',
        'body' => 'Anything in scope that does not work as delivered is fixed at no charge for 30 days. New features or content changes are quoted separately or covered by a Care Plan.',
        'you' => ['Report anything odd — we would rather hear about it'],
        'we' => ['Fixes within 2 business days for defects', 'Final handoff notes and credentials'],
    ],
    [
        'title' => 'Handoff or Care Plan',
        'when' => 'Ongoing',
        'body' => 'Own it outright and run it yourself, or keep Webstar on a month-to-month Care Plan for hosting, backups, updates, edits, and monthly reporting. Website as a Service clients own their files after month 6.',
        'you' => ['Choose: self-host, Care Plan, or stay on Website as a Service'],
        'we' => ['Full export on request, any time', 'Help pointing your domain wherever you go'],
    ],
];

$howToLd = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => 'How a Webstar website project works',
    'description' => $pageDescription,
    'step' => array_map(static function (array $s, int $i): array {
        return [
            '@type' => 'HowToStep',
            'position' => $i + 1,
            'name' => $s['title'],
            'text' => $s['body'],
        ];
    }, $steps, array_keys($steps)),
];
$jsonLd = json_encode($howToLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">How a project works</h1>
        <p class="home-section__textbox">The same eight steps run every Webstar website, whether you pay once or go month-to-month. Nothing starts without a written scope, a signed agreement, and a first payment — and nothing launches without your approval.</p>

        <ol class="process-steps">
            <?php foreach ($steps as $i => $step) : ?>
                <li class="process-step">
                    <div class="process-step__num" aria-hidden="true"><?php echo str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT); ?></div>
                    <div>
                        <h2 class="process-step__title"><?php echo webstar_h($step['title']); ?> <span class="package-card__meta">· <?php echo webstar_h($step['when']); ?></span></h2>
                        <p><?php echo webstar_h($step['body']); ?></p>
                        <div class="process-step__cols">
                            <div>
                                <h4>What we need from you</h4>
                                <ul>
                                    <?php foreach ($step['you'] as $item) : ?>
                                        <li><?php echo webstar_h($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div>
                                <h4>What you get from us</h4>
                                <ul>
                                    <?php foreach ($step['we'] as $item) : ?>
                                        <li><?php echo webstar_h($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="home-section home-section--panel">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Communication you can count on</h2>
        <ul class="home-section__textbox">
            <li><strong>One point of contact.</strong> You work directly with Ian from intake to launch.</li>
            <li><strong>Response time.</strong> Email and portal messages answered within one business day; Care Plan clients get the response times on their plan.</li>
            <li><strong>Weekly status.</strong> A short update every week a project is active, plus the staging link so you can look any time.</li>
            <li><strong>Honest limits.</strong> We do not guarantee rankings or lead counts. We do guarantee a site that works, that you own, and that is measured.</li>
        </ul>
    </div>
</section>

<div class="cta-band">
    <p>Ready to start step one?</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Choose a package</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('plans.php')); ?>">Website as a Service</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>

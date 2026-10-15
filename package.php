<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$slug = trim((string) ($_GET['slug'] ?? ''));
$package = webstar_package_by_slug($slug);
if ($package === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}
$currentPage = 'packages';
$pageTitle = $package['title'] . ' — Webstar Business Services';
$pageDescription = (string) $package['meta_description'];
$canonicalPath = 'packages/' . rawurlencode($slug) . '/';
$intakeStatus = isset($_GET['intake_status']) ? (string) $_GET['intake_status'] : '';
$intakeReason = isset($_GET['intake_reason']) ? (string) $_GET['intake_reason'] : '';
$intakeFields = isset($_GET['intake_fields']) ? (string) $_GET['intake_fields'] : '';
$packageSlug = $package['slug'];
$packageTitle = $package['title'];
$intakeRedirect = webstar_package_url($slug);
$featured = ($package['badge'] ?? '') !== '';
$packageHeaderClass = 'package-page__header' . ($featured ? ' package-page__header--featured' : '');

$packageUrl = webstar_absolute_url($canonicalPath);
$offer = [
    '@type' => 'Offer',
    'url' => $packageUrl,
    'priceCurrency' => 'USD',
    'availability' => 'https://schema.org/InStock',
    'description' => (string) $package['price'],
];
$priceText = trim((string) $package['price']);
if (preg_match('/^\$(\d+(?:,\d{3})*(?:\.\d+)?)$/', $priceText, $m) === 1) {
    $offer['price'] = str_replace(',', '', $m[1]);
} elseif (preg_match('/^\$(\d+(?:,\d{3})*)\s*[–-]\s*\$(\d+(?:,\d{3})*)$/u', $priceText, $m) === 1) {
    $offer['priceSpecification'] = [
        '@type' => 'PriceSpecification',
        'minPrice' => str_replace(',', '', $m[1]),
        'maxPrice' => str_replace(',', '', $m[2]),
        'priceCurrency' => 'USD',
    ];
} elseif (preg_match('/^\$(\d+(?:,\d{3})*)\+$/', $priceText, $m) === 1) {
    $offer['priceSpecification'] = [
        '@type' => 'PriceSpecification',
        'minPrice' => str_replace(',', '', $m[1]),
        'priceCurrency' => 'USD',
    ];
}

$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => (string) $package['title'],
    'description' => (string) $package['meta_description'],
    'provider' => [
        '@type' => 'ProfessionalService',
        'name' => webstar_config()['business_name'],
        'url' => webstar_absolute_url(''),
    ],
    'areaServed' => webstar_config()['service_area'],
    'url' => $packageUrl,
    'offers' => $offer,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

include __DIR__ . '/library/layout-start.php';
?>
<section class="package-page home-section home-section--night">
    <div class="home-section__inner">
        <div class="<?php echo webstar_h($packageHeaderClass); ?>">
            <?php if ($featured) : ?>
                <span class="package-card__badge"><?php echo webstar_h((string) $package['badge']); ?></span>
            <?php endif; ?>

            <div class="package-page__top">
                <div class="package-page__intro">
                    <h1 class="home-section__heading"><?php echo webstar_h($package['title']); ?></h1>
                    <p class="package-page__summary"><?php echo webstar_h($package['summary']); ?></p>
                    <p class="package-page__ideal"><strong>Ideal for:</strong> <?php echo webstar_h($package['ideal_for']); ?></p>
                </div>
                <div class="package-page__facts">
                    <p class="package-page__price"><?php echo webstar_h($package['price']); ?></p>
                    <p class="package-card__meta"><?php echo webstar_h((string) ($package['price_note'] ?? 'One-time project fee')); ?></p>
                    <p class="package-card__meta">Turnaround: <?php echo webstar_h($package['turnaround']); ?></p>
                    <p class="package-card__meta">Or <a href="<?php echo webstar_h(webstar_url('plans.php')); ?>">$150/mo Website as a Service</a></p>
                </div>
            </div>

            <h2 class="package-page__list-title">Deliverables</h2>
            <ul class="package-page__deliverables">
                <?php foreach ($package['includes'] as $item) : ?>
                    <li><?php echo webstar_h($item); ?></li>
                <?php endforeach; ?>
            </ul>

            <div class="package-page__actions">
                <a class="home-section__btn home-section__btn--primary" href="#intake">Start intake form</a>
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Contact</a>
            </div>
            <p class="package-page__compare"><a href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Back to all packages</a></p>
        </div>

        <?php if (!empty($package['process']) && is_array($package['process'])) :
            $process = $package['process'];
            $processTitle = (string) ($process['title'] ?? 'How we work');
            $processIntro = (string) ($process['intro'] ?? '');
            $phases = $process['phases'] ?? [];
            ?>
            <section class="package-process" aria-labelledby="package-process-title">
                <h2 class="package-process__title" id="package-process-title"><?php echo webstar_h($processTitle); ?></h2>
                <?php if ($processIntro !== '') : ?>
                    <p class="package-process__intro"><?php echo webstar_h($processIntro); ?></p>
                <?php endif; ?>
                <?php if (is_array($phases) && $phases !== []) : ?>
                    <ol class="package-process__phases">
                        <?php foreach ($phases as $index => $phase) :
                            if (!is_array($phase)) {
                                continue;
                            }
                            $phaseName = (string) ($phase['name'] ?? '');
                            $phaseSummary = (string) ($phase['summary'] ?? '');
                            $phaseSteps = $phase['steps'] ?? [];
                            if ($phaseName === '') {
                                continue;
                            }
                            ?>
                            <li class="package-process__phase">
                                <p class="package-process__index" aria-hidden="true"><?php echo webstar_h(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></p>
                                <div class="package-process__body">
                                    <h3 class="package-process__name"><?php echo webstar_h($phaseName); ?></h3>
                                    <?php if ($phaseSummary !== '') : ?>
                                        <p class="package-process__summary"><?php echo webstar_h($phaseSummary); ?></p>
                                    <?php endif; ?>
                                    <?php if (is_array($phaseSteps) && $phaseSteps !== []) : ?>
                                        <ul class="package-process__steps">
                                            <?php foreach ($phaseSteps as $step) : ?>
                                                <li><?php echo webstar_h((string) $step); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php include __DIR__ . '/library/package-intake-form.php'; ?>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>

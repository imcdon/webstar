<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/landing-pages.php';

$landing_slug = $landing_slug ?? '';
$landing = mdhl_landing_by_slug($landing_slug);
if ($landing === null) {
    http_response_code(404);
    require dirname(__DIR__) . '/404.php';
    exit;
}

$pages = mdhl_landing_pages();
$page_title = $landing['title'];
$page_description = $landing['description'];
$canonical_path = '/' . $landing_slug . '/';
$faq_items = $landing['faq'] ?? [];
$schema_faq = $faq_items !== [];
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => $landing['nav_label'], 'url' => absolute_url($landing_slug . '/')],
];
$schema_service = [
    'name' => $landing['service_name'],
    'description' => $landing['description'],
    'url' => absolute_url($landing_slug . '/'),
];
$body_class = 'page-landing page-landing--' . $landing_slug;

$hero_rel = (string) ($landing['hero_image'] ?? '');
$hero_file = $hero_rel !== '' ? dirname(__DIR__) . '/' . $hero_rel : '';
$landing_hero = ($hero_file !== '' && is_file($hero_file)) ? url($hero_rel) : '';
$landing_hero_alt = (string) ($landing['hero_alt'] ?? $landing['nav_label']);
if ($landing_hero !== '') {
    $og_image = absolute_url(ltrim($hero_rel, '/'));
    $preload_image = $landing_hero;
}

if (!empty($landing['gallery_files']) && is_array($landing['gallery_files'])) {
    $gallery_strip = mdhl_gallery_pick_files($landing['gallery_files']);
} else {
    $gallery_strip = mdhl_gallery_for_landing($landing['gallery_match'] ?? [], 6);
}

$related_slugs = $landing['related'] ?? [];
$related_pages = [];
foreach ($related_slugs as $relSlug) {
    if (isset($pages[$relSlug])) {
        $related_pages[$relSlug] = $pages[$relSlug];
    }
}

require __DIR__ . '/header.php';
?>
<section class="page-hero landing-hero<?= $landing_hero !== '' ? ' landing-hero--split' : '' ?>">
    <div class="container landing-hero__grid">
        <div class="landing-hero__copy">
            <p class="breadcrumbs">
                <a href="<?= htmlspecialchars(url()) ?>">Home</a> /
                <span>Services</span> /
                <?= htmlspecialchars($landing['nav_label']) ?>
            </p>
            <h1><?= htmlspecialchars($landing['h1']) ?></h1>
            <p><?= htmlspecialchars($landing['intro']) ?></p>
            <div class="landing-hero__actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Request a quote</a>
            </div>
        </div>
        <?php if ($landing_hero !== ''): ?>
            <div class="landing-hero__media">
                <img
                    src="<?= htmlspecialchars($landing_hero) ?>"
                    alt="<?= htmlspecialchars($landing_hero_alt) ?>"
                    width="900"
                    height="700"
                    fetchpriority="high"
                >
            </div>
        <?php endif; ?>
    </div>
</section>

<?php foreach ($landing['sections'] as $section): ?>
<section class="section">
    <div class="container prose-narrow">
        <h2><?= htmlspecialchars($section['h2']) ?></h2>
        <p><?= htmlspecialchars($section['body']) ?></p>
        <?php if (!empty($section['bullets'])): ?>
            <ul class="landing-bullets">
                <?php foreach ($section['bullets'] as $bullet): ?>
                    <li><?= htmlspecialchars($bullet) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
<?php endforeach; ?>

<?php if ($gallery_strip !== []): ?>
<section class="section section--tight">
    <div class="container-wide">
        <div class="section__head container" style="margin-left:0;padding-left:0;">
            <h2>Recent <?= htmlspecialchars(strtolower($landing['nav_label'])) ?> work</h2>
            <p class="section__lead">Examples from Metro Detroit installs. More in the gallery.</p>
        </div>
        <div class="gallery-preview">
            <?php foreach ($gallery_strip as $img): ?>
                <a href="<?= htmlspecialchars(url('gallery/')) ?>">
                    <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>" loading="lazy" width="800" height="600">
                </a>
            <?php endforeach; ?>
        </div>
        <p class="container" style="margin-top:1.25rem;">
            <a class="btn btn-ghost" href="<?= htmlspecialchars(url('gallery/')) ?>">Open full gallery</a>
        </p>
    </div>
</section>
<?php endif; ?>

<?php if ($faq_items !== []): ?>
<section class="section" id="faq">
    <div class="container">
        <div class="section__head">
            <h2>FAQ</h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faq_items as $i => $item): ?>
                <div class="faq-item">
                    <button type="button" aria-expanded="false" id="faq-btn-<?= (int) $i ?>"><?= htmlspecialchars($item['q']) ?></button>
                    <div><?= htmlspecialchars($item['a']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($related_pages !== []): ?>
<section class="section section--tight">
    <div class="container">
        <div class="section__head">
            <h2>Related services</h2>
        </div>
        <div class="card-grid landing-related">
            <?php foreach ($related_pages as $relSlug => $rel): ?>
                <article class="card">
                    <h3><a href="<?= htmlspecialchars(url($relSlug . '/')) ?>"><?= htmlspecialchars($rel['nav_label']) ?></a></h3>
                    <p><?= htmlspecialchars($rel['description']) ?></p>
                    <p><a href="<?= htmlspecialchars(url($relSlug . '/')) ?>">Learn more →</a></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section" id="quote">
    <div class="container">
        <div class="cta-band">
            <h2>Ready for a quote?</h2>
            <p>Text or email a few photos of the property. Include your city and what you want lit. Steven will follow up.</p>
            <div class="cta-band__actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>

<?php
/*
 * partials/service-page.php - Shared service detail layout.
 * Expects $service array from config.
 */
?>
<section class="service-hero">
    <img class="service-hero-bg"
         src="<?= htmlspecialchars(url(ltrim($service['hero_image'], '/'))) ?>"
         alt=""
         width="1600"
         height="900"
         fetchpriority="high"
         decoding="async"
         <?= !empty($service['hero_position']) ? 'style="object-position: ' . htmlspecialchars($service['hero_position']) . '"' : '' ?>>
    <div class="service-hero-overlay"></div>
    <div class="container-wide service-hero-content">
        <div class="eyebrow text-on-dark"><?= htmlspecialchars($service['meta_line']) ?></div>
        <h1 class="heading-display text-display-xl text-on-dark"><?= htmlspecialchars($service['title']) ?></h1>
        <p class="service-summary"><?= htmlspecialchars($service['summary']) ?></p>
        <div class="hero-actions">
            <a class="btn-primary btn-call-plain" href="<?= htmlspecialchars($phone_href) ?>">Call Now</a>
            <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact us</a>
        </div>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide service-layout">
        <div class="service-main prose-content">
            <div class="eyebrow">About this trip</div>
            <h2 class="heading-display text-display-md"><?= htmlspecialchars($service['best_for']) ?></h2>
            <?php foreach ($service['long_description'] as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
            <?php endforeach; ?>

            <h3 class="heading-display text-display-sm">Highlights</h3>
            <ul class="check-list">
                <?php foreach ($service['highlights'] as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <aside class="service-sidebar">
            <div class="sidebar-card">
                <h3 class="heading-display text-display-sm">What's included</h3>
                <ul class="check-list">
                    <?php foreach ($service['whats_included'] as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($service['rates_note'])): ?>
                    <p class="rates-note"><?= htmlspecialchars($service['rates_note']) ?></p>
                <?php endif; ?>
                <a class="btn-primary" href="<?= htmlspecialchars($phone_href) ?>">Call to reserve</a>
            </div>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/final-cta.php'; ?>

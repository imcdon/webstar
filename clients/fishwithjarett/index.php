<?php
require __DIR__ . '/includes/config.php';

$page_title = $site_name . ' | Salmon & Trout on Lake Michigan Tributaries';
$body_class = 'page-home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" aria-label="Welcome">
    <div class="hero__visual" aria-hidden="true"></div>
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <p class="hero__brand"><?= htmlspecialchars($site_name) ?></p>
        <h1 class="hero__title">Salmon and trout on Lake Michigan tributaries</h1>
        <p class="hero__support">Fish Wisconsin, Indiana, and Michigan waters with Jarett Carlson — guided trips built around the run and the river.</p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="<?= htmlspecialchars($phone_href) ?>">Call <?= htmlspecialchars($phone) ?></a>
            <?php if (!empty($social['instagram'])): ?>
                <a class="btn btn--ghost" href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section--fish" id="what-we-fish">
    <div class="section__inner">
        <h2 class="section__title">What we fish</h2>
        <p class="section__lead">Tributary salmon and trout seasons across the southern Lake Michigan shoreline — WI, IN, and MI.</p>
        <ul class="species-list">
            <?php foreach ($species as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
        </ul>
        <ul class="state-list">
            <?php foreach ($states as $state): ?>
                <li><?= htmlspecialchars($state) ?></li>
            <?php endforeach; ?>
        </ul>
        <p class="waters-note">Waters include <?= htmlspecialchars(implode(', ', $waters)) ?>.</p>
    </div>
</section>

<section class="section section--trust">
    <div class="section__inner section__inner--narrow">
        <p class="trust-line"><?= htmlspecialchars($trust_line) ?></p>
        <p class="trust-copy">Book directly with Jarett. He answers the phone, plans the trip, and guides on the water.</p>
    </div>
</section>

<section class="section section--cta" id="book">
    <div class="section__inner section__inner--narrow">
        <h2 class="section__title">Ready to fish?</h2>
        <p class="section__lead">Call to check dates and conditions for the next run.</p>
        <a class="btn btn--primary btn--lg" href="<?= htmlspecialchars($phone_href) ?>">Call <?= htmlspecialchars($phone) ?></a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

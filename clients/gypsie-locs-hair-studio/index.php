<?php

declare(strict_types=1);

require __DIR__ . '/includes/config.php';

$page_title = 'Gypsie Locs Hair Studio | Color & Extensions in Waterford, MI';
$page_description = $site_description;
$canonical_path = '/';
$schema_home = true;
$schema_faq = true;
$body_class = 'page-home';

require __DIR__ . '/includes/header.php';
?>

<section class="hero" aria-label="Gypsie Locs Hair Studio">
    <div class="hero__plane" aria-hidden="true">
        <svg class="hero__strands" viewBox="0 0 1200 900" preserveAspectRatio="xMidYMid slice" focusable="false">
            <defs>
                <linearGradient id="strandA" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#B8956C"/>
                    <stop offset="55%" stop-color="#DCC9C4"/>
                    <stop offset="100%" stop-color="#9AA3A7"/>
                </linearGradient>
                <linearGradient id="strandB" x1="100%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#2F5A6E"/>
                    <stop offset="100%" stop-color="#B8956C"/>
                </linearGradient>
            </defs>
            <path class="strand strand--1" d="M-40 90 C 220 20, 400 240, 640 150 S 980 20, 1240 190" fill="none" stroke="url(#strandA)" stroke-width="48" stroke-linecap="round"/>
            <path class="strand strand--2" d="M-40 280 C 280 380, 460 120, 720 250 S 1000 430, 1240 270" fill="none" stroke="url(#strandB)" stroke-width="30" stroke-linecap="round"/>
            <path class="strand strand--3" d="M-40 500 C 220 430, 440 650, 720 510 S 1000 390, 1240 560" fill="none" stroke="url(#strandA)" stroke-width="18" stroke-linecap="round" opacity="0.7"/>
            <path class="strand strand--4" d="M-40 720 C 300 640, 500 800, 780 680 S 1040 760, 1240 700" fill="none" stroke="#1B3A4B" stroke-width="12" stroke-linecap="round" opacity="0.4"/>
            <path class="strand strand--2" d="M 900 -20 C 980 180, 860 360, 1040 520 S 980 780, 1180 920" fill="none" stroke="#B8956C" stroke-width="20" stroke-linecap="round" opacity="0.35"/>
        </svg>
        <div class="hero__grain"></div>
    </div>
    <div class="hero__content">
        <p class="hero__brand">Gypsie Locs</p>
        <h1 class="hero__title">Custom color and extensions, one private chair.</h1>
        <p class="hero__lead">Waterford suite on Highland Road. Twenty years of color craft — Dream Catchers extensions, Wella, Redken, and Rusk.</p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book a consultation</a>
            <a class="btn btn--ghost" href="<?= htmlspecialchars(url('services/')) ?>">See services</a>
        </div>
    </div>
</section>

<section class="section section--contrast" id="why">
    <div class="section-inner">
        <h2 class="section-title">Not a salon floor. A suite built around your appointment.</h2>
        <p class="section-lead">
            Waterford has walk-in chains, multi-stylist collectives, and Booksy braid shops.
            Gypsie Locs is the other option: one stylist, appointment-only, color and extensions done with room to think.
        </p>
        <ul class="cred-row" role="list">
            <?php foreach ($credentials as $cred): ?>
                <li><?= htmlspecialchars($cred) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="section" id="specialties">
    <div class="section-inner">
        <h2 class="section-title">What the chair is for</h2>
        <p class="section-lead">The menu matches the work — color, dimensional formulas, Dream Catchers installs, and the cut that finishes it.</p>
        <div class="specialty-rail">
            <?php foreach ($specialties as $item): ?>
                <article class="specialty">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['body']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="section-more">
            <a href="<?= htmlspecialchars(url('services/')) ?>">Full service menu</a>
        </p>
    </div>
</section>

<section class="section section--indigo" id="place">
    <div class="section-inner place-split">
        <div>
            <h2 class="section-title">Highland Road, Suite 12</h2>
            <p class="section-lead">
                Inside Ultra Salon Suites in Waterford — serving clients from Clarkston, White Lake, and across Oakland County who want a quieter chair than a busy multi-station salon.
            </p>
            <p class="place-address">
                <?= htmlspecialchars($address['line1']) ?><br>
                <?= htmlspecialchars($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?>
            </p>
            <a class="btn btn--on-dark" href="<?= htmlspecialchars(url('book/')) ?>">Hours &amp; booking</a>
        </div>
        <div class="place-hours" aria-label="Business hours">
            <?php foreach ($hours as $row): ?>
                <div class="place-hours__row">
                    <span><?= htmlspecialchars($row['day']) ?></span>
                    <span><?= htmlspecialchars($row['hours']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="gallery">
    <div class="section-inner">
        <h2 class="section-title">Work in the chair</h2>
        <p class="section-lead">Photo frames ready for real before-and-afters. Until then, the booking gallery on GlossGenius holds current work.</p>
        <div class="frame-row" aria-hidden="true">
            <div class="frame frame--tall"><span>Color</span></div>
            <div class="frame"><span>Extensions</span></div>
            <div class="frame frame--wide"><span>Finish</span></div>
        </div>
        <p class="section-more">
            <a href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">View live work &amp; book</a>
        </p>
    </div>
</section>

<section class="section section--tight" id="faq">
    <div class="section-inner section-inner--narrow">
        <h2 class="section-title">Before you book</h2>
        <div class="faq">
            <?php foreach ($faq_items as $item): ?>
                <details class="faq__item">
                    <summary><?= htmlspecialchars($item['q']) ?></summary>
                    <p><?= htmlspecialchars($item['a']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="section-inner cta-band__inner">
        <p class="cta-band__brand">Gypsie Locs</p>
        <h2 class="cta-band__title">Start with a free consultation.</h2>
        <p>Color correction and extensions are quoted in person. Everything else is ready to book online.</p>
        <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Open booking</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

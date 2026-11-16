<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

$page_title = 'About | Gypsie Locs Hair Studio';
$page_description = 'Meet the stylist behind Gypsie Locs Hair Studio in Waterford — Dream Catchers extension specialist and Wella, Redken, and Rusk certified colorist with 20 years of experience.';
$canonical_path = '/about/';
$body_class = 'page-about';

require dirname(__DIR__) . '/includes/header.php';
?>

<header class="page-hero">
    <div class="section-inner">
        <p class="page-hero__brand">Gypsie Locs</p>
        <h1>Twenty years of color craft in one Waterford suite.</h1>
        <p class="page-hero__lead">Independent stylist. Private chair. Formulas and extensions planned for the hair in front of you.</p>
    </div>
</header>

<section class="section">
    <div class="section-inner about-grid">
        <div class="about-copy">
            <p>
                Gypsie Locs Hair Studio is a one-stylist practice inside Ultra Salon Suites on Highland Road.
                The name carries history; the day-to-day work is custom color, Dream Catchers extensions, and the cut and finish that make both last.
            </p>
            <p>
                That is a different promise than a multi-service beauty collective or a walk-in chain.
                You book the chair, not a rotating floor.
            </p>
        </div>
        <ul class="cred-stack" role="list">
            <?php foreach ($credentials as $cred): ?>
                <li><?= htmlspecialchars($cred) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="section section--contrast">
    <div class="section-inner section-inner--narrow">
        <h2 class="section-title">How appointments work</h2>
        <p class="section-lead">
            New color correction and extension clients start with a free consultation.
            Returning color and cut appointments book directly online. A 50% fee applies for no-shows or cancellations without 48 hours’ notice.
        </p>
        <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book a consultation</a>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

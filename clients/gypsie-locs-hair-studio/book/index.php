<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

$page_title = 'Book | Gypsie Locs Hair Studio';
$page_description = 'Book Gypsie Locs Hair Studio in Waterford online via GlossGenius. Hours, address, and cancellation policy.';
$canonical_path = '/book/';
$body_class = 'page-book';

require dirname(__DIR__) . '/includes/header.php';
?>

<header class="page-hero">
    <div class="section-inner">
        <p class="page-hero__brand">Gypsie Locs</p>
        <h1>Book the chair</h1>
        <p class="page-hero__lead">Online scheduling runs through GlossGenius. Prefer to talk first? Call the suite.</p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Open GlossGenius</a>
            <a class="btn btn--ghost" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
        </div>
    </div>
</header>

<section class="section">
    <div class="section-inner place-split">
        <div>
            <h2 class="section-title">Studio</h2>
            <p class="place-address">
                <?= htmlspecialchars($address['line1']) ?><br>
                <?= htmlspecialchars($address['line2']) ?><br>
                <?= htmlspecialchars($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?>
            </p>
            <p class="fine-print"><?= htmlspecialchars($policy_note) ?></p>
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

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

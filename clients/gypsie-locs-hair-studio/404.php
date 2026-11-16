<?php

declare(strict_types=1);

http_response_code(404);
require __DIR__ . '/includes/config.php';

$page_title = 'Page not found | Gypsie Locs Hair Studio';
$page_description = 'That page is not available.';
$canonical_path = '/404';
$body_class = 'page-404';
$meta_robots = 'noindex, nofollow';

require __DIR__ . '/includes/header.php';
?>

<header class="page-hero">
    <div class="section-inner">
        <p class="page-hero__brand">Gypsie Locs</p>
        <h1>That page isn’t here.</h1>
        <p class="page-hero__lead">Head home or open booking to find an appointment.</p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="<?= htmlspecialchars(url()) ?>">Home</a>
            <a class="btn btn--ghost" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book online</a>
        </div>
    </div>
</header>

<?php require __DIR__ . '/includes/footer.php'; ?>

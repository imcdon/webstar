<?php
/*
 * 404.php - Branded not-found page.
 */
http_response_code(404);
require __DIR__ . '/includes/config.php';

$page_title = 'Page not found | ' . $site_name;
$page_description = 'The page you requested could not be found.';
$meta_robots = 'noindex, follow';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container-wide">
        <div class="eyebrow text-on-dark-muted">404</div>
        <h1 class="heading-display text-display-xl text-on-dark">Page not found</h1>
        <p class="page-hero-intro">That link may be outdated or the page was moved. Try one of these instead.</p>
        <div class="hero-actions" style="margin-top: 1.75rem;">
            <a class="btn-primary" href="<?= htmlspecialchars(url()) ?>">Home</a>
            <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact</a>
            <a class="btn-outline-light" href="<?= htmlspecialchars(url('faq/')) ?>">FAQ</a>
        </div>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide">
        <div class="section-header">
            <div>
                <div class="eyebrow">Popular</div>
                <h2 class="heading-display text-display-md">Services</h2>
            </div>
        </div>
        <ul class="check-list">
            <?php foreach ($nav_services as $svc): ?>
                <li><a href="<?= htmlspecialchars(url(ltrim($svc['href'], '/'))) ?>"><?= htmlspecialchars($svc['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

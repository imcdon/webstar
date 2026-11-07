<?php
declare(strict_types=1);
http_response_code(404);
require_once __DIR__ . '/includes/config.php';

$page_title = 'Page not found | Metro Detroit Holiday Lighting';
$page_description = 'That page could not be found. Call Metro Detroit Holiday Lighting at (248) 525-4715.';
$meta_robots = 'noindex, follow';
$canonical_path = '/404.php';

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <h1>Page not found</h1>
        <p>That link does not exist. Head home, browse the gallery, or call <?= htmlspecialchars($phone) ?>.</p>
        <p style="margin-top:1.25rem;">
            <a class="btn btn-primary" href="<?= htmlspecialchars(url()) ?>">Home</a>
            <a class="btn btn-ghost" href="<?= htmlspecialchars($phone_href) ?>">Call</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

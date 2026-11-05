<?php

declare(strict_types=1);

http_response_code(404);
require __DIR__ . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Page not found | DJ's Tackle";
$meta_robots = 'noindex, nofollow';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <h1>Page not found</h1>
    <p class="muted">That page doesn’t exist.</p>
    <a class="btn btn-primary" href="<?= Helpers::e(url()) ?>">Home</a>
    <a class="btn btn-secondary" href="<?= Helpers::e(url('shop/')) ?>">Shop</a>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

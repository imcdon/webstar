<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Terms | DJ's Tackle";
$canonical_path = '/terms/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Terms of use</h1></div></section>
<section class="section" style="padding-top:0"><div class="container prose">
  <p>By shopping at <?= Helpers::e($site_name) ?> you agree to provide accurate order information and to our shipping and return policies. Prices and stock are subject to change. Product images are representative; colors may vary.</p>
  <p>Checkout and payments are processed by Square. Gift cards follow Square’s eGift terms when enabled.</p>
  <p>Questions? <?= Helpers::e($email) ?> · <?= Helpers::e($phone) ?></p>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

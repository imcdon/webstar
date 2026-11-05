<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Privacy | DJ's Tackle";
$canonical_path = '/privacy/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Privacy policy</h1></div></section>
<section class="section" style="padding-top:0"><div class="container prose">
  <p>We collect the information you provide (name, email, shipping address) for cart recovery, accounts, and support. Checkout, payments, receipts, and order fulfillment are handled by Square — we never store card numbers on our servers.</p>
  <p>We use cookies for cart, wishlist, and sign-in sessions. Order and account emails are used for transactional messages (confirmations, shipping, magic-link login, optional cart reminders and back-in-stock alerts). You can unsubscribe from cart reminders via the link in those emails.</p>
  <p>Contact <?= Helpers::e($email) ?> with privacy questions.</p>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

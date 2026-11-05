<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Shipping & Returns | DJ's Tackle";
$canonical_path = '/shipping-returns/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Shipping &amp; returns</h1></div></section>
<section class="section" style="padding-top:0"><div class="container prose">
  <h2>Shipping</h2>
  <ul>
    <li>USPS Ground Advantage — flat rate <?= Helpers::e(Helpers::money($shopConfig->shippingFlatCents)) ?></li>
    <li>Free shipping on orders of <?= Helpers::e(Helpers::money($shopConfig->shippingFreeOverCents)) ?> or more</li>
    <li>Local pickup in Sterling Heights, MI (select at checkout)</li>
  </ul>
  <p>After you pay, Square emails your receipt. We fulfill orders (pack, ship, or schedule pickup) in the <strong>Square Dashboard</strong>.</p>
  <h2>Returns</h2>
  <p>Unused items in original condition may be returned within 14 days of delivery. Contact us at <?= Helpers::e($email) ?> or <?= Helpers::e($phone) ?> with your Square receipt to start a return. Custom-color orders are final sale unless defective.</p>
  <h2>Sales tax</h2>
  <p>Michigan sales tax (6%) is applied by Square on eligible orders.</p>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

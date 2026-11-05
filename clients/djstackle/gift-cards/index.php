<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Gift Cards | DJ's Tackle";
$canonical_path = '/gift-cards/';
$giftUrl = $shopConfig->giftCardPurchaseUrl;
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Gift cards</h1><p class="muted">Give the gift of tackle — redeemed at Square checkout.</p></div></section>
<section class="section" style="padding-top:0"><div class="container">
  <div class="card" style="max-width:640px">
    <?php if ($giftUrl): ?>
      <p>Purchase a Square eGift card securely on Square’s hosted page.</p>
      <p><a class="btn btn-primary" href="<?= Helpers::e($giftUrl) ?>" rel="noopener" target="_blank">Buy a gift card</a></p>
    <?php else: ?>
      <p>Gift cards are sold through Square eGift Cards. Once the owner enables eGift cards in the Square Dashboard, paste the purchase URL into <code>gift_card_purchase_url</code> in shop-config.</p>
      <p class="muted">In demo mode this page is a placeholder. Redemption still works on Square checkout when gift cards are configured in production.</p>
      <p><a class="btn btn-secondary" href="<?= Helpers::e(url('contact/')) ?>">Ask us about gift cards</a></p>
    <?php endif; ?>
  </div>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

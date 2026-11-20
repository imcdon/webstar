<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "Gift Cards | DJ's Tackle";
$page_description = "DJ's Tackle eGift cards — let them pick jigs, River Ready boxes, and walleye tackle. Buy online; redeem at checkout.";
$canonical_path = '/gift-cards/';
$og_image = absolute_url('assets/img/gift-card.jpg');
$giftUrl = $shopConfig->giftCardPurchaseUrl;
require dirname(__DIR__) . '/includes/header.php';
?>

<section class="gift-card-hero" aria-labelledby="gift-card-title">
  <div class="container gift-card-hero__grid">
    <figure class="gift-card-hero__art">
      <img
        src="<?= Helpers::e(url('assets/img/gift-card.jpg')) ?>"
        alt="DJ's Tackle gift card — Gettin' Jiggy With It"
        loading="eager"
        decoding="async"
      >
    </figure>

    <div class="gift-card-hero__body">
      <h1 id="gift-card-title">Gift cards</h1>
      <p class="gift-card-hero__lead">
        Let them pick the jig color and weight — River Ready boxes, harnesses, plastics, and everything in the shop.
        You choose the dollar amount; they spend it when the bite&rsquo;s on.
      </p>
      <p class="gift-card-hero__detail">
        Purchase through Square&rsquo;s secure checkout. Email the eGift or print it.
        At our checkout they enter the card number and PIN — same as any card payment.
      </p>

      <div class="gift-card-hero__cta">
        <?php if ($giftUrl): ?>
          <a class="btn btn-primary" href="<?= Helpers::e($giftUrl) ?>" rel="noopener noreferrer" target="_blank">Buy a gift card</a>
          <a class="btn btn-secondary" href="<?= Helpers::e(url('shop/')) ?>">Shop all tackle</a>
        <?php else: ?>
          <a class="btn btn-secondary" href="<?= Helpers::e(url('contact/')) ?>">Ask about gift cards</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="gift-card-redeem">
  <div class="container gift-card-redeem__inner">
    <p>
      Good for in-stock and standard shop items. Custom color pours need a quote from Jeff first —
      call <a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a>
      or <a href="<?= Helpers::e(url('contact/')) ?>">send a message</a> if you&rsquo;re not sure.
    </p>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
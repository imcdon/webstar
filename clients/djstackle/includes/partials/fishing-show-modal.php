<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;
?>
<div class="show-modal" data-show-modal hidden>
  <div class="show-modal__backdrop" data-show-modal-close tabindex="-1" aria-hidden="true"></div>
  <div
    class="show-modal__panel"
    role="dialog"
    aria-modal="true"
    aria-labelledby="show-modal-title"
    aria-describedby="show-modal-desc"
  >
    <button type="button" class="show-modal__close" data-show-modal-close aria-label="Close announcement">&times;</button>
    <div class="show-modal__logos">
      <div class="show-modal__logos-main">
        <img
          class="show-modal__logo-dj"
          src="<?= Helpers::e(url('assets/img/logo-full.png')) ?>"
          alt="DJ's Tackle — Gettin' Jiggy With It"
          width="200"
          height="203"
        >
        <img
          class="show-modal__badge-usa"
          src="<?= Helpers::e(url('assets/img/badges/made-in-usa.png')) ?>"
          alt="Proudly Made in USA"
          width="132"
          height="132"
        >
        <img
          class="show-modal__logo-rip"
          src="<?= Helpers::e(url('assets/img/brands/rip-dip-fishing.jpg')) ?>"
          alt="Rip Dip Fishing"
          width="220"
          height="215"
        >
      </div>
    </div>
    <h2 id="show-modal-title" class="show-modal__title">We&rsquo;ll be at the Novi Fishing Show!</h2>
    <p class="show-modal__dates"><strong>January 4th, 5th, and 6th, 2027</strong></p>
    <div id="show-modal-desc" class="show-modal__body">
      <p>We&rsquo;ll have a full lineup of our products alongside Rip Dip Fishing!</p>
      <p>We are prepping for the fishing show, so please allow a week for your order to arrive!</p>
    </div>
    <div class="show-modal__actions">
      <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Shop before the show</a>
      <button type="button" class="btn btn-secondary" data-show-modal-close>See you there!</button>
    </div>
  </div>
</div>

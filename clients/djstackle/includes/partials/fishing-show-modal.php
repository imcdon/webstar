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
    <div class="show-modal__media">
      <img
        class="show-modal__collage"
        src="<?= Helpers::e(url('assets/img/show-modal-collage.jpg')) ?>"
        alt=""
        width="1200"
        height="900"
        decoding="async"
      >
    </div>
    <div class="show-modal__content">
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
</div>

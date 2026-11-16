<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;

$showModalLabel = "DJ's Tackle at the Novi Fishing Show, January 4–6, 2027.";
?>
<div class="show-modal" data-show-modal hidden>
  <div class="show-modal__backdrop" data-show-modal-close tabindex="-1" aria-hidden="true"></div>
  <div class="show-modal__frame">
    <button type="button" class="show-modal__close" data-show-modal-close aria-label="Close announcement">&times;</button>
    <div
      class="show-modal__panel"
      role="dialog"
      aria-modal="true"
      aria-label="<?= Helpers::e($showModalLabel) ?>"
    >
      <img
        class="show-modal__ad"
        src="<?= Helpers::e(url('assets/img/popup-ad.png')) ?>"
        alt="<?= Helpers::e($showModalLabel) ?>"
        width="600"
        height="900"
        decoding="async"
      >
    </div>
  </div>
</div>
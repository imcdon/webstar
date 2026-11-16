<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;

/**
 * Custom color / mix-your-own CTA.
 * Optional: set $custom_color_context before including (e.g. product name).
 */
$custom_color_context = isset($custom_color_context) ? trim((string) $custom_color_context) : '';
if ($custom_color_context !== '') {
    $custom_color_lead = match (true) {
        str_contains(strtolower($custom_color_context), 'river ready')
            => 'Want custom jig colors packed in your River Ready Box? Dawn mixes plastics in Sterling Heights — tell us the shades and box size.',
        str_contains(strtolower($custom_color_context), 'crawler')
            => 'Want a blade or bead mix that isn’t listed? Dawn builds custom crawler harnesses in Sterling Heights — tell us the colors and quantity.',
        default
            => 'Want this jig in a shade that isn’t listed? Dawn mixes plastics in Sterling Heights — tell us the color and quantity.',
    };
} else {
    $custom_color_lead = 'Don’t see the shade you’re fishing? Dawn mixes plastics in Sterling Heights — name the color, size, and how many you need.';
}
?>
<section class="section custom-color" aria-labelledby="custom-color-heading">
  <div class="container custom-color-inner">
    <div class="custom-color-media">
      <figure class="custom-color-chart">
        <img
          src="<?= Helpers::e(url('assets/img/djs-colors.jpg')) ?>"
          alt="DJ's Tackle color chart — hand-painted jig color options"
          width="960"
          height="720"
          loading="lazy"
          decoding="async"
        >
      </figure>
      <figure class="custom-color-workshop">
        <img
          src="<?= Helpers::e(url('assets/img/dawn-painting.jpg')) ?>"
          alt=""
          width="480"
          height="640"
          loading="lazy"
          decoding="async"
        >
      </figure>
    </div>

    <div class="custom-color-copy">
      <h2 id="custom-color-heading">Mix your own</h2>
      <p class="custom-color-lead"><?= Helpers::e($custom_color_lead) ?></p>
      <p class="custom-color-note">Custom pours ship with the next batch — or pick up by appointment.</p>

      <ul class="custom-color-tray" aria-label="Example lure colors we can mix">
        <li class="custom-color-chip" style="--chip:#b8d400" title="Chartreuse"><span>Chartreuse</span></li>
        <li class="custom-color-chip" style="--chip:#e85d04" title="Blaze orange"><span>Blaze</span></li>
        <li class="custom-color-chip" style="--chip:#f4f1e8" title="Pearl"><span>Pearl</span></li>
        <li class="custom-color-chip" style="--chip:#6b2d8b" title="Purple"><span>Purple</span></li>
        <li class="custom-color-chip" style="--chip:#c41e3a" title="Fire red"><span>Fire</span></li>
        <li class="custom-color-chip" style="--chip:#1a1a1a" title="Black"><span>Black</span></li>
        <li class="custom-color-chip" style="--chip:#2f6fed" title="Electric blue"><span>Blue</span></li>
        <li class="custom-color-chip custom-color-chip--you" title="Your mix"><span>Yours</span></li>
      </ul>

      <div class="custom-color-actions">
        <a class="btn btn-primary" href="<?= Helpers::e(url('contact/')) ?>">Request a mix</a>
        <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call Jeff</a>
      </div>
    </div>
  </div>
</section>

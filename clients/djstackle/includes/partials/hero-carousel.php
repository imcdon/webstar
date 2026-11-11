<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;

/** @var array{items:list<array<string,mixed>>} $hero_catalog */
$hero_catalog = $hero_catalog ?? ['items' => []];
$hero_slides = $hero_catalog['items'];
$hero_slide_count = count($hero_slides) + 1;

$hero_excerpt = static function (array $product, int $max = 220): string {
    $text = trim(strip_tags((string) ($product['description'] ?? '')));
    if ($text === '') {
        return 'Professional walleye tackle built by hand in Sterling Heights, Michigan.';
    }
    if (mb_strlen($text) <= $max) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $max - 1)) . '…';
};

$hero_price = static function (array $product): string {
    if (!isset($product['min_price'])) {
        return '';
    }
    $price = Helpers::money((int) $product['min_price']);
    if (!empty($product['max_price']) && (int) $product['max_price'] !== (int) $product['min_price']) {
        $price .= ' – ' . Helpers::money((int) $product['max_price']);
    }

    return $price;
};
?>
<section class="hero hero--carousel" aria-label="Featured products">
  <div class="container">
    <div class="hero-carousel" data-hero-carousel>
      <div class="hero-carousel-viewport">
        <div class="hero-carousel-track" data-hero-carousel-track>
          <?php foreach ($hero_slides as $i => $product): ?>
            <article class="hero-carousel-slide" data-hero-slide aria-label="Slide <?= (int) $i + 1 ?> of <?= (int) $hero_slide_count ?>">
              <div class="hero-carousel-slide__media">
                <img
                  src="<?= Helpers::e(product_image_url($product)) ?>"
                  alt="<?= Helpers::e($product['name']) ?>"
                  width="480"
                  height="480"
                  loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                  <?= $i === 0 ? 'fetchpriority="high"' : '' ?>
                >
              </div>
              <div class="hero-carousel-slide__copy">
                <p class="eyebrow"><?= Helpers::e($product['category_name'] ?? 'DJ\'s Tackle') ?></p>
                <h1><?= Helpers::e($product['name']) ?></h1>
                <p class="hero-carousel-desc"><?= Helpers::e($hero_excerpt($product)) ?></p>
                <?php $price = $hero_price($product); ?>
                <?php if ($price !== ''): ?>
                  <p class="hero-carousel-price"><?= Helpers::e($price) ?></p>
                <?php endif; ?>
                <div class="hero-actions">
                  <a class="btn btn-primary" href="<?= Helpers::e(url('product/?slug=' . urlencode((string) $product['slug']))) ?>">View product</a>
                  <a class="btn btn-secondary" href="<?= Helpers::e(url('shop/')) ?>">Shop all</a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>

          <article class="hero-carousel-slide hero-carousel-slide--custom" data-hero-slide aria-label="Slide <?= (int) $hero_slide_count ?> of <?= (int) $hero_slide_count ?>">
            <div class="hero-carousel-slide__media">
              <img
                src="<?= Helpers::e(url('assets/img/djs-colors.jpg')) ?>"
                alt=""
                width="480"
                height="480"
                loading="lazy"
              >
            </div>
            <div class="hero-carousel-slide__copy">
              <p class="eyebrow">Custom orders</p>
              <h1>Custom colors &amp; quantities</h1>
              <p class="hero-carousel-desc hero-carousel-desc--lead">Choose custom jigs, crawler harnesses, or plastics.</p>
              <p class="hero-carousel-desc">Tell us the color, size, and quantity you need — Jeff can build it in the workshop and quote pickup or shipping.</p>
              <div class="hero-actions">
                <a class="btn btn-primary" href="<?= Helpers::e(url('contact/')) ?>">Request a custom order</a>
                <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call <?= Helpers::e($phone) ?></a>
              </div>
            </div>
          </article>
        </div>
      </div>

      <div class="hero-carousel-controls">
        <button type="button" class="hero-carousel-btn" data-hero-prev aria-label="Previous slide">
          <span aria-hidden="true">&#8249;</span>
        </button>
        <div class="hero-carousel-dots" data-hero-dots role="tablist" aria-label="Choose slide">
          <?php foreach ($hero_slides as $i => $product): ?>
            <button
              type="button"
              class="hero-carousel-dot<?= $i === 0 ? ' is-active' : '' ?>"
              data-hero-dot="<?= (int) $i ?>"
              role="tab"
              aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
              aria-label="<?= Helpers::e($product['name']) ?>"
            ></button>
          <?php endforeach; ?>
          <button
            type="button"
            class="hero-carousel-dot"
            data-hero-dot="<?= count($hero_slides) ?>"
            role="tab"
            aria-selected="false"
            aria-label="Custom colors and quantities"
          ></button>
        </div>
        <button type="button" class="hero-carousel-btn" data-hero-next aria-label="Next slide">
          <span aria-hidden="true">&#8250;</span>
        </button>
      </div>
    </div>
  </div>
</section>

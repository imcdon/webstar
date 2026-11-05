<?php

declare(strict_types=1);

require __DIR__ . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Reviews;
use Webstar\Shop\Seo;

$categories = $shopCatalog->categories();
$featured = $shopCatalog->search(['per_page' => 8, 'sort' => 'featured']);
$reviews = Reviews::make($shopConfig)->latestApproved(4);
$seo = Seo::make($shopConfig);

$page_title = "DJ's Tackle | Walleye Jigs & River Ready Boxes";
$page_description = $site_description;
$body_class = 'home';
$canonical_path = '/';
$json_ld = [$seo->organizationJsonLd()];

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <div class="eyebrow">Sterling Heights, MI · Family owned</div>
      <h1>Walleye tackle built by fishermen, for fishermen.</h1>
      <p>Chip-free jig heads, durable plastics (including colors sourced from Rip Dip Fishing), River Ready boxes, stinger hooks, bottom bouncers, and Arctic Spinners. Custom colors available — just ask.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Shop tackle</a>
        <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call <?= Helpers::e($phone) ?></a>
      </div>
    </div>
    <aside class="hero-card">
      <strong>Local pickup available</strong>
      <p class="muted">Order online and pick up in Sterling Heights, or ship USPS Ground Advantage. Free shipping over <?= Helpers::e(Helpers::money($shopConfig->shippingFreeOverCents)) ?>.</p>
      <p><a href="<?= Helpers::e(url('shipping-returns/')) ?>">Shipping &amp; returns →</a></p>
    </aside>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Shop by category</h2>
        <p>Everything for your next walleye trip.</p>
      </div>
    </div>
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a class="category-card" href="<?= Helpers::e(url('shop/?category=' . urlencode($cat['slug']))) ?>">
          <strong><?= Helpers::e($cat['name']) ?></strong>
          <span><?= (int) $cat['product_count'] ?> products</span>
        </a>
      <?php endforeach; ?>
      <?php if (!$categories): ?>
        <div class="card">
          <p>Catalog not loaded yet. Run <code>php bin/seed-local.php</code> from the djstackle folder.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Featured products</h2>
        <p>River Ready boxes, jigs, hooks, and more.</p>
      </div>
      <a href="<?= Helpers::e(url('shop/')) ?>">View all</a>
    </div>
    <div class="product-grid">
      <?php foreach ($featured['items'] as $product): ?>
        <?php
          $badge = Helpers::stockBadge((int) ($product['total_qty'] ?? 0), $shopConfig->lowStockThreshold);
          $price = isset($product['min_price']) ? Helpers::money((int) $product['min_price']) : '';
          if (!empty($product['max_price']) && (int) $product['max_price'] !== (int) $product['min_price']) {
              $price .= ' – ' . Helpers::money((int) $product['max_price']);
          }
        ?>
        <article class="product-card">
          <a href="<?= Helpers::e(url('product/?slug=' . urlencode($product['slug']))) ?>">
            <img src="<?= Helpers::e(product_image_url($product)) ?>" alt="<?= Helpers::e($product['name']) ?>" loading="lazy" width="400" height="400">
            <div class="body">
              <span class="badge <?= Helpers::e($badge['class']) ?>"><?= Helpers::e($badge['label']) ?></span>
              <strong><?= Helpers::e($product['name']) ?></strong>
              <span class="meta"><?= Helpers::e($product['category_name'] ?? '') ?></span>
              <span class="price"><?= Helpers::e($price) ?></span>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($reviews): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>What anglers say</h2>
        <p>Verified reviews from customers.</p>
      </div>
    </div>
    <div class="product-grid">
      <?php foreach ($reviews as $r): ?>
        <article class="card">
          <div class="stars"><?= str_repeat('★', (int) $r['rating']) . str_repeat('☆', 5 - (int) $r['rating']) ?></div>
          <p><?= Helpers::e($r['body']) ?></p>
          <p class="muted">— <?= Helpers::e($r['author_name']) ?> on <a href="<?= Helpers::e(url('product/?slug=' . urlencode($r['product_slug']))) ?>"><?= Helpers::e($r['product_name']) ?></a></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container">
    <div class="band">
      <div>
        <strong>Need a custom color?</strong>
        <p style="margin:.35rem 0 0;opacity:.9">If you don’t see what you’re looking for, reach out — we can do custom colors.</p>
      </div>
      <a class="btn btn-primary" href="<?= Helpers::e(url('contact/')) ?>">Contact us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

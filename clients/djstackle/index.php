<?php

declare(strict_types=1);

require __DIR__ . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Reviews;
use Webstar\Shop\Seo;

$hero_catalog = $shopCatalog->search(['per_page' => 48, 'sort' => 'name']);
$featured = $shopCatalog->search(['per_page' => 6, 'sort' => 'featured']);
$reviews = Reviews::make($shopConfig)->latestApproved(4);
$seo = Seo::make($shopConfig);

$page_title = "DJ's Tackle | Walleye Jigs & River Ready Boxes";
$page_description = $site_description;
$body_class = 'home';
$canonical_path = '/';
$og_image = absolute_url('assets/img/logo-full.png');
$json_ld = [$seo->organizationJsonLd()];

require __DIR__ . '/includes/header.php';
?>
<?php require __DIR__ . '/includes/partials/hero-carousel.php'; ?>

<section class="section about-hero">
  <div class="container about-block-grid about-hero-grid">
    <div class="about-photo-wrap about-hero-logo-wrap">
      <img
        class="about-hero-logo"
        src="<?= Helpers::e(url('assets/img/logo-full.png')) ?>"
        alt="DJ's Tackle — Gettin' Jiggy With It"
        width="318"
        height="323"
      >
      <img
        class="about-badge about-hero-badge-usa"
        src="<?= Helpers::e(url('assets/img/badges/made-in-usa.png')) ?>"
        alt="Proudly Made in USA"
        width="112"
        height="112"
      >
      <img
        class="about-badge about-hero-badge-family"
        src="<?= Helpers::e(url('assets/img/badges/family-owned.png')) ?>"
        alt="Family Owned and Operated"
        width="112"
        height="112"
      >
    </div>
    <div class="about-block-copy about-hero-copy">
      <p class="about-kicker">Sterling Heights, Michigan · Est. 2020</p>
      <h2>Family owned. Hand crafted. USA made.</h2>
      <p>DJ's Tackle was founded in 2020 by Dawn and Jeff Campbell — the D and J in DJ's Tackle — with one goal: high-quality custom tackle for Walleye anglers in the Great Lakes area.</p>
      <p>Founded in Sterling Heights, Michigan, we serve customers online around the USA, and our customers that buy from the stores we supply.</p>
      <p>We offer premium Walleye fishing tackle, as well as custom ordering. Anglers with a particular color in mind or custom quantities can place a custom order by calling Jeff, or sending us an email through the <a href="<?= Helpers::e(url('contact/')) ?>">contact form</a>.</p>
      <p>All of our products are made by hand right here in Sterling Heights. Our goal is to provide quality baits and lures for customers fishing the Detroit River, St. Clair River, Lake St. Clair, Lake Erie, and surrounding Great Lakes and tributaries.</p>
      <p class="about-thanks">Thank you for supporting small businesses!</p>
      <?php require __DIR__ . '/includes/partials/retail-stores-marquee.php'; ?>
      <div class="about-block-actions">
        <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Shop tackle</a>
        <a class="btn btn-secondary" href="<?= Helpers::e(url('about/')) ?>">More about us</a>
        <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call Jeff</a>
      </div>
    </div>
  </div>
</section>

<section class="section home-products">
  <div class="home-products-bg" aria-hidden="true">
    <img src="<?= Helpers::e(url('assets/img/products/showcase-a.jpg')) ?>" alt="" width="1920" height="1280" loading="lazy" decoding="async">
  </div>
  <div class="home-products-shade" aria-hidden="true"></div>
  <div class="container home-products-inner">
    <div class="section-head">
      <div>
        <h2>Our products</h2>
        <p>River Ready boxes, jigs, hooks, and more.</p>
      </div>
      <a href="<?= Helpers::e(url('shop/')) ?>">View all</a>
    </div>
    <div class="product-grid home-product-grid">
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
            <img src="<?= Helpers::e(product_image_url($product)) ?>" alt="<?= Helpers::e($product['name']) ?>" loading="lazy" width="224" height="224">
            <div class="body">
              <span class="badge <?= Helpers::e($badge['class']) ?>"><?= Helpers::e($badge['label']) ?></span>
              <strong><?= Helpers::e($product['name']) ?></strong>
              <span class="meta"><?= Helpers::e($product['category_name'] ?? '') ?></span>
              <span class="price"><?= Helpers::e($price) ?></span>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
      <?php if ($featured['items'] === []): ?>
        <div class="card">
          <p>Catalog not loaded yet. Run <code>php bin/seed-local.php</code> from the djstackle folder.</p>
        </div>
      <?php endif; ?>
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

<section class="section contact-band">
  <div class="container contact-band-inner">
    <div>
      <h2>Contact us</h2>
      <p>Questions, custom colors, or pickup — we're happy to help.</p>
      <p><a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a> · <a href="<?= Helpers::e($email_href) ?>"><?= Helpers::e($email) ?></a></p>
      <p><?= Helpers::e($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?> · Online only · Local pickup by appointment</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <a class="btn btn-primary" href="<?= Helpers::e(url('contact/')) ?>">Send a message</a>
      <a class="btn btn-secondary" href="<?= Helpers::e(url('shipping-returns/')) ?>">Shipping &amp; returns</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="band">
      <div>
        <strong>Need a custom color?</strong>
        <p style="margin:.35rem 0 0;opacity:.95">If you don’t see what you’re looking for, reach out — we can do custom colors.</p>
      </div>
      <a class="btn btn-primary" href="<?= Helpers::e(url('contact/')) ?>">Contact us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

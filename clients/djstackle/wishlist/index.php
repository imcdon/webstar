<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

if ($shopAccount) {
    $shopWishlist->mergeToAccount($shopAccount['id']);
}
$items = $shopWishlist->products($shopCatalog);

$page_title = "Wishlist | DJ's Tackle";
$canonical_path = '/wishlist/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Wishlist</h1></div></section>
<section class="section" style="padding-top:0">
  <div class="container">
    <?php if (!$items): ?>
      <div class="card"><p>No saved items yet.</p><a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Browse shop</a></div>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($items as $product): ?>
          <article class="product-card">
            <a href="<?= Helpers::e(url('product/?slug=' . urlencode($product['slug']))) ?>">
              <img src="<?= Helpers::e(product_image_url($product)) ?>" alt="<?= Helpers::e($product['name']) ?>">
              <div class="body">
                <strong><?= Helpers::e($product['name']) ?></strong>
                <span class="price"><?= Helpers::e(Helpers::money((int) ($product['min_price'] ?? 0))) ?></span>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

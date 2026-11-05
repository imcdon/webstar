<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Reviews;
use Webstar\Shop\Seo;
use Webstar\Shop\StockAlerts;

$slug = trim((string) ($_GET['slug'] ?? ''));
$product = $slug !== '' ? $shopCatalog->getBySlug($slug) : null;
if (!$product) {
    http_response_code(404);
    require dirname(__DIR__) . '/404.php';
    exit;
}

$flash = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'review') {
            Reviews::make($shopConfig)->submit(
                $product['id'],
                (string) ($_POST['author_name'] ?? ''),
                (string) ($_POST['body'] ?? ''),
                (int) ($_POST['rating'] ?? 5),
                (string) ($_POST['author_email'] ?? ($shopAccount['email'] ?? '')),
                $shopAccount['id'] ?? null,
            );
            $flash = ['ok', 'Thanks! Your review is pending moderation.'];
        } elseif ($action === 'stock_alert') {
            $varId = (string) ($_POST['variation_id'] ?? '');
            StockAlerts::make($shopConfig)->subscribe($varId, (string) ($_POST['email'] ?? ''));
            $flash = ['ok', 'We will email you when this is back in stock.'];
        }
    } catch (Throwable $e) {
        $flash = ['err', $e->getMessage()];
    }
    // refresh reviews
    $product = $shopCatalog->getBySlug($slug);
}

$variations = $product['variations'];
$selected = $variations[0] ?? null;
foreach ($variations as $v) {
    if ((int) $v['quantity'] > 0 || !(int) $v['track_inventory']) {
        $selected = $v;
        break;
    }
}
$related = $shopCatalog->related($product['id'], $product['category_id'] ?? null, 4);
$seo = Seo::make($shopConfig);
$badge = $selected
    ? Helpers::stockBadge((int) $selected['quantity'], $shopConfig->lowStockThreshold, (bool) $selected['track_inventory'])
    : Helpers::stockBadge(0, $shopConfig->lowStockThreshold);

$page_title = $product['name'] . " | DJ's Tackle";
$page_description = $product['description'] !== '' ? $product['description'] : $product['name'];
$og_image = product_image_url($product);
if (!str_starts_with($og_image, 'http')) {
    $og_image = absolute_url(ltrim(str_replace(url(), '', $og_image), '/'));
}
$canonical_path = '/product/?slug=' . rawurlencode($product['slug']);
$body_class = 'product-page';
$body_attrs = 'data-product-id="' . Helpers::e($product['id']) . '"';
$json_ld = [
    $seo->productJsonLd($product),
    $seo->breadcrumbJsonLd([
        ['name' => 'Home', 'url' => $shopConfig->absoluteUrl()],
        ['name' => 'Shop', 'url' => $shopConfig->absoluteUrl('shop/')],
        ['name' => $product['name'], 'url' => $shopConfig->absoluteUrl('product/?slug=' . rawurlencode($product['slug']))],
    ]),
];

require dirname(__DIR__) . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <?php if ($flash): ?>
      <div class="flash <?= Helpers::e($flash[0]) ?>"><?= Helpers::e($flash[1]) ?></div>
    <?php endif; ?>
    <div class="product-hero">
      <div class="product-gallery">
        <img src="<?= Helpers::e(product_image_url($product)) ?>" alt="<?= Helpers::e($product['name']) ?>" width="800" height="800">
      </div>
      <div>
        <div class="eyebrow"><?= Helpers::e($product['category_name'] ?? 'Tackle') ?></div>
        <h1><?= Helpers::e($product['name']) ?></h1>
        <p class="price" data-product-price style="font-size:1.4rem;font-weight:800;color:var(--accent)">
          <?= Helpers::e($selected ? Helpers::money((int) $selected['price_cents']) : '') ?>
        </p>
        <span class="badge <?= Helpers::e($badge['class']) ?>" data-stock-badge><?= Helpers::e($badge['label']) ?></span>
        <?php if (!empty($product['avg_rating'])): ?>
          <p class="stars"><?= str_repeat('★', (int) round((float) $product['avg_rating'])) ?> <span class="muted">(<?= (int) $product['review_count'] ?>)</span></p>
        <?php endif; ?>
        <?php if ($product['description']): ?>
          <p><?= nl2br(Helpers::e($product['description'])) ?></p>
        <?php endif; ?>

        <?php
        $options = $product['options'] ?? [];
        $variationMap = [];
        foreach ($variations as $v) {
            $vb = Helpers::stockBadge((int) $v['quantity'], $shopConfig->lowStockThreshold, (bool) $v['track_inventory']);
            $variationMap[] = [
                'id' => $v['id'],
                'name' => $v['name'],
                'sku' => $v['sku'],
                'price' => Helpers::money((int) $v['price_cents']),
                'stock' => $vb['label'],
                'stock_class' => $vb['class'],
                'sold_out' => ($vb['state'] ?? '') === 'sold_out',
                'options' => $v['option_values'] ?? [],
            ];
        }
        ?>
        <?php if ($options && count($variations) > 1): ?>
          <div class="option-groups" data-option-picker>
            <?php foreach ($options as $opt): ?>
              <div class="option-group" data-option-group="<?= Helpers::e($opt['name']) ?>">
                <label><strong><?= Helpers::e($opt['name']) ?>:</strong> <span class="muted" data-option-current><?= Helpers::e($selected['option_values'][$opt['name']] ?? '') ?></span></label>
                <div class="variation-list" role="radiogroup" aria-label="<?= Helpers::e($opt['name']) ?>">
                  <?php foreach ($opt['values'] as $val): ?>
                    <button type="button" role="radio"
                      data-option-name="<?= Helpers::e($opt['name']) ?>"
                      data-option-value="<?= Helpers::e($val) ?>"
                      aria-checked="<?= ($selected['option_values'][$opt['name']] ?? null) === $val ? 'true' : 'false' ?>">
                      <?= Helpers::e($val) ?>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <p class="muted" data-selected-sku>SKU: <?= Helpers::e($selected['sku'] ?? '') ?></p>
        <?php elseif (count($variations) > 1): ?>
          <label><strong>Options</strong></label>
          <div class="variation-list" role="list">
            <?php foreach ($variationMap as $vm): ?>
              <button type="button"
                data-variation="<?= Helpers::e($vm['id']) ?>"
                data-price="<?= Helpers::e($vm['price']) ?>"
                data-stock="<?= Helpers::e($vm['stock']) ?>"
                data-stock-class="<?= Helpers::e($vm['stock_class']) ?>"
                aria-pressed="<?= $selected && $selected['id'] === $vm['id'] ? 'true' : 'false' ?>">
                <?= Helpers::e($vm['name']) ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <script type="application/json" data-variations-json><?= json_encode($variationMap, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
        <input type="hidden" name="variation_id" value="<?= Helpers::e($selected['id'] ?? '') ?>">
        <div class="qty-row">
          <label for="qty">Qty</label>
          <input id="qty" name="qty" type="number" min="1" value="1">
        </div>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap">
          <button class="btn btn-primary" type="button" data-add-to-cart data-variation-id="<?= Helpers::e($selected['id'] ?? '') ?>"<?= ($badge['state'] ?? '') === 'sold_out' ? ' disabled' : '' ?>>Add to cart</button>
          <button class="btn btn-secondary" type="button" data-wishlist-toggle data-product-id="<?= Helpers::e($product['id']) ?>" aria-pressed="<?= $shopWishlist->has($product['id']) ? 'true' : 'false' ?>">
            <?= $shopWishlist->has($product['id']) ? 'Saved' : 'Save' ?>
          </button>
        </div>

        <?php if (($badge['state'] ?? '') === 'sold_out' && $selected): ?>
          <form method="post" class="card" style="margin-top:1rem">
            <input type="hidden" name="action" value="stock_alert">
            <input type="hidden" name="variation_id" value="<?= Helpers::e($selected['id']) ?>">
            <strong>Back in stock alert</strong>
            <label>Email
              <input type="email" name="email" required value="<?= Helpers::e($shopAccount['email'] ?? '') ?>">
            </label>
            <button class="btn btn-secondary" type="submit">Notify me</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section">
  <div class="container">
    <div class="section-head"><h2>Related products</h2></div>
    <div class="product-grid">
      <?php foreach ($related as $rel): ?>
        <article class="product-card">
          <a href="<?= Helpers::e(url('product/?slug=' . urlencode($rel['slug']))) ?>">
            <img src="<?= Helpers::e(product_image_url($rel)) ?>" alt="<?= Helpers::e($rel['name']) ?>" loading="lazy">
            <div class="body">
              <strong><?= Helpers::e($rel['name']) ?></strong>
              <span class="price"><?= Helpers::e(Helpers::money((int) ($rel['min_price'] ?? 0))) ?></span>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container" style="display:grid;grid-template-columns:1.2fr .8fr;gap:1.5rem">
    <div>
      <h2>Reviews</h2>
      <?php if (!$product['reviews']): ?>
        <p class="muted">No reviews yet. Be the first.</p>
      <?php endif; ?>
      <?php foreach ($product['reviews'] as $r): ?>
        <article class="review">
          <div class="stars"><?= str_repeat('★', (int) $r['rating']) ?></div>
          <p><?= Helpers::e($r['body']) ?></p>
          <p class="muted">
            — <?= Helpers::e($r['author_name']) ?>
            <?php if ($r['is_verified']): ?><span class="badge verified">Verified buyer</span><?php endif; ?>
          </p>
        </article>
      <?php endforeach; ?>
    </div>
    <form method="post" class="card review-form">
      <h3>Write a review</h3>
      <input type="hidden" name="action" value="review">
      <label>Name<input name="author_name" required value="<?= Helpers::e($shopAccount['name'] ?? '') ?>"></label>
      <label>Email<input type="email" name="author_email" value="<?= Helpers::e($shopAccount['email'] ?? '') ?>"></label>
      <label>Rating
        <select name="rating">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>"><?= $i ?> stars</option>
          <?php endfor; ?>
        </select>
      </label>
      <label>Review<textarea name="body" rows="4" required></textarea></label>
      <button class="btn btn-primary" type="submit">Submit review</button>
    </form>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

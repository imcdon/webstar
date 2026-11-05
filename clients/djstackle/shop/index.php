<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? '')),
    'weight' => trim((string) ($_GET['weight'] ?? '')),
    'min_price' => isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int) round(((float) $_GET['min_price']) * 100) : '',
    'max_price' => isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int) round(((float) $_GET['max_price']) * 100) : '',
    'sort' => (string) ($_GET['sort'] ?? 'featured'),
    'page' => max(1, (int) ($_GET['page'] ?? 1)),
    'per_page' => 24,
];

$result = $shopCatalog->search($filters);
$categories = $shopCatalog->categories();
$weights = $shopCatalog->weightLabels();
$totalPages = max(1, (int) ceil($result['total'] / $result['per_page']));

$page_title = "Shop | DJ's Tackle";
$page_description = 'Browse walleye jigs, River Ready boxes, stinger hooks, bottom bouncers, crawler harnesses, and Arctic Spinners.';
$canonical_path = '/shop/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <div class="eyebrow">Catalog</div>
    <h1>Shop</h1>
    <p class="muted"><?= (int) $result['total'] ?> products<?= $filters['q'] !== '' ? ' matching “' . Helpers::e($filters['q']) . '”' : '' ?></p>
  </div>
</section>
<section class="section" style="padding-top:0">
  <div class="container shop-layout">
    <aside class="filters">
      <form method="get" action="">
        <label for="q">Search</label>
        <input id="q" type="search" name="q" value="<?= Helpers::e($filters['q']) ?>" placeholder="Jigs, boxes…">

        <label for="category">Category</label>
        <select id="category" name="category">
          <option value="">All</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= Helpers::e($cat['slug']) ?>"<?= $filters['category'] === $cat['slug'] ? ' selected' : '' ?>><?= Helpers::e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <?php if ($weights): ?>
        <label for="weight">Weight</label>
        <select id="weight" name="weight">
          <option value="">Any</option>
          <?php foreach ($weights as $w): ?>
            <option value="<?= Helpers::e($w) ?>"<?= $filters['weight'] === $w ? ' selected' : '' ?>><?= Helpers::e($w) ?></option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <label for="min_price">Min price ($)</label>
        <input id="min_price" type="number" step="0.01" min="0" name="min_price" value="<?= Helpers::e(isset($_GET['min_price']) ? (string) $_GET['min_price'] : '') ?>">

        <label for="max_price">Max price ($)</label>
        <input id="max_price" type="number" step="0.01" min="0" name="max_price" value="<?= Helpers::e(isset($_GET['max_price']) ? (string) $_GET['max_price'] : '') ?>">

        <label for="sort">Sort</label>
        <select id="sort" name="sort">
          <?php
          $sorts = ['featured' => 'Featured', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name', 'newest' => 'Newest'];
          foreach ($sorts as $k => $label):
          ?>
            <option value="<?= Helpers::e($k) ?>"<?= $filters['sort'] === $k ? ' selected' : '' ?>><?= Helpers::e($label) ?></option>
          <?php endforeach; ?>
        </select>

        <p style="margin-top:1rem">
          <button class="btn btn-primary" type="submit">Apply</button>
          <a class="btn btn-ghost" href="<?= Helpers::e(url('shop/')) ?>">Reset</a>
        </p>
      </form>
    </aside>

    <div>
      <div class="product-grid">
        <?php foreach ($result['items'] as $product): ?>
          <?php
            $badge = Helpers::stockBadge((int) ($product['total_qty'] ?? 0), $shopConfig->lowStockThreshold);
            $price = Helpers::money((int) ($product['min_price'] ?? 0));
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
                <?php if (!empty($product['avg_rating'])): ?>
                  <span class="stars" aria-label="<?= Helpers::e((string) $product['avg_rating']) ?> stars"><?= str_repeat('★', (int) round((float) $product['avg_rating'])) ?></span>
                <?php endif; ?>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if (!$result['items']): ?>
        <div class="card"><p>No products match those filters.</p></div>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <nav class="section" aria-label="Pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php
              $qs = $_GET;
              $qs['page'] = $p;
              $href = url('shop/?') . http_build_query($qs);
            ?>
            <a class="btn <?= $p === $result['page'] ? 'btn-primary' : 'btn-secondary' ?>" href="<?= Helpers::e($href) ?>"><?= $p ?></a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

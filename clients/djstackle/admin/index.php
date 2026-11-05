<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Reviews;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$authed = !empty($_SESSION['djt_admin']);

if (isset($_GET['logout'])) {
    unset($_SESSION['djt_admin']);
    header('Location: ' . url('admin/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $pass = (string) ($_POST['password'] ?? '');
    if ($shopConfig->adminPasswordHash && password_verify($pass, $shopConfig->adminPasswordHash)) {
        $_SESSION['djt_admin'] = true;
        $authed = true;
    } else {
        $loginError = 'Invalid password.';
    }
}

if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'approve_review') {
        Reviews::make($shopConfig)->approve((int) ($_POST['id'] ?? 0));
    } elseif ($action === 'reject_review') {
        Reviews::make($shopConfig)->reject((int) ($_POST['id'] ?? 0));
    } elseif ($action === 'sync' && !$shopConfig->demoMode) {
        $shopCatalog->syncFromSquare();
        $syncMsg = 'Catalog synced from Square.';
    } elseif ($action === 'seed_local') {
        $json = dirname(__DIR__) . '/seed/products.json';
        $data = json_decode((string) file_get_contents($json), true) ?: [];
        $n = $shopCatalog->seedFromArray($data);
        $syncMsg = "Seeded {$n} local products.";
    }
}

$page_title = 'Admin';
$meta_robots = 'noindex, nofollow';
require dirname(__DIR__) . '/includes/header.php';

if (!$authed):
?>
<section class="section"><div class="container" style="max-width:420px">
  <form method="post" class="card account-form">
    <h1>Admin</h1>
    <?php if (!empty($loginError)): ?><div class="flash err"><?= Helpers::e($loginError) ?></div><?php endif; ?>
    <input type="hidden" name="action" value="login">
    <label>Password<input type="password" name="password" required></label>
    <button class="btn btn-primary" type="submit">Sign in</button>
  </form>
</div></section>
<?php
require dirname(__DIR__) . '/includes/footer.php';
exit;
endif;

$pending = Reviews::make($shopConfig)->pending();
$productCount = (int) \Webstar\Shop\Db::pdo()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$openCarts = (int) \Webstar\Shop\Db::pdo()->query("SELECT COUNT(*) FROM carts WHERE status='open' AND email IS NOT NULL")->fetchColumn();
$alerts = (int) \Webstar\Shop\Db::pdo()->query('SELECT COUNT(*) FROM stock_alerts WHERE notified_at IS NULL')->fetchColumn();
?>
<section class="page-hero"><div class="container" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
  <h1>Shop admin</h1>
  <a href="<?= Helpers::e(url('admin/?logout=1')) ?>">Sign out</a>
</div></section>
<section class="section" style="padding-top:0"><div class="container stack">
  <?php if (!empty($syncMsg)): ?><div class="flash ok"><?= Helpers::e($syncMsg) ?></div><?php endif; ?>
  <div class="flash info">Orders, packing, shipping, and pickup are managed in the <strong>Square Dashboard</strong> (and Square app). This admin is only for site catalog sync and review moderation.</div>
  <div class="product-grid">
    <div class="card"><strong><?= $productCount ?></strong><div class="muted">Products (cached)</div></div>
    <div class="card"><strong><?= $openCarts ?></strong><div class="muted">Open carts w/ email</div></div>
    <div class="card"><strong><?= $alerts ?></strong><div class="muted">Stock alerts</div></div>
    <div class="card"><strong><?= count($pending) ?></strong><div class="muted">Pending reviews</div></div>
  </div>

  <div class="card">
    <h2>Catalog</h2>
    <form method="post" style="display:flex;gap:.5rem;flex-wrap:wrap">
      <button class="btn btn-secondary" name="action" value="seed_local" type="submit">Seed from products.json</button>
      <?php if (!$shopConfig->demoMode): ?>
        <button class="btn btn-primary" name="action" value="sync" type="submit">Sync from Square</button>
      <?php else: ?>
        <span class="demo-pill">Demo mode — add Square token to enable sync</span>
      <?php endif; ?>
    </form>
  </div>

  <div class="card">
    <h2>Pending reviews</h2>
    <?php if (!$pending): ?><p class="muted">None</p><?php endif; ?>
    <?php foreach ($pending as $r): ?>
      <div class="review">
        <strong><?= Helpers::e($r['product_name']) ?></strong> — <?= (int) $r['rating'] ?>★ by <?= Helpers::e($r['author_name']) ?>
        <p><?= Helpers::e($r['body']) ?></p>
        <form method="post" style="display:inline">
          <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
          <button class="btn btn-primary" name="action" value="approve_review">Approve</button>
          <button class="btn btn-secondary" name="action" value="reject_review">Reject</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

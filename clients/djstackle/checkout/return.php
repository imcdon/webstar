<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Orders;

$order = null;
if (!empty($_GET['order'])) {
    $order = Orders::make($shopConfig)->getByNumber((string) $_GET['order']);
    if ($order) {
        $shopCart->markConverted($order['id']);
        // ensure session cart is empty after thank-you
        if ($shopCart->count() > 0) {
            $shopCart->clear();
        }
    }
} elseif (!empty($_GET['cart'])) {
    $cartId = (string) $_GET['cart'];
    \Webstar\Shop\Db::pdo()->prepare(
        "UPDATE carts SET status = 'converted', updated_at = datetime('now') WHERE id = ? AND status = 'open'"
    )->execute([$cartId]);
    $shopCart->clear();
}

$page_title = "Thank you | DJ's Tackle";
$canonical_path = '/checkout/return.php';
$meta_robots = 'noindex, nofollow';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container" style="max-width:640px">
    <h1>Thank you</h1>
    <div class="flash ok">Your payment went through.</div>
    <div class="card">
      <?php if ($order): ?>
        <p>Order <strong><?= Helpers::e($order['order_number']) ?></strong>
          — <?= Helpers::e(Helpers::money((int) $order['total_cents'])) ?></p>
        <?php if (!empty($order['items'])): ?>
          <ul>
            <?php foreach ($order['items'] as $item): ?>
              <li>
                <?= Helpers::e($item['name']) ?>
                <?php if (!empty($item['variation_name'])): ?>
                  <span class="muted">(<?= Helpers::e($item['variation_name']) ?>)</span>
                <?php endif; ?>
                × <?= (int) $item['quantity'] ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
      <p>A confirmation was sent to your email (when mail is configured). Shipping, pickup, and fulfillment are handled in the <strong>Square Dashboard</strong>.</p>
      <p class="muted">Questions? Call <a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a>
        or email <a href="<?= Helpers::e($email_href) ?>"><?= Helpers::e($email) ?></a>
        <?php if ($order): ?>with order <?= Helpers::e($order['order_number']) ?><?php endif; ?>.</p>
      <p><a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Keep shopping</a></p>
    </div>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

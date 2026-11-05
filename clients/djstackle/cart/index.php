<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\AbandonedCarts;
use Webstar\Shop\Helpers;

if (!empty($_GET['restore'])) {
    $token = (string) $_GET['restore'];
    if (!empty($_GET['unsubscribe'])) {
        AbandonedCarts::make($shopConfig)->unsubscribe($token);
        $flash = ['ok', 'You have been unsubscribed from cart reminders.'];
    } else {
        $shopCart->restoreFromToken($token);
        $flash = ['ok', 'Cart restored.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'update') {
        foreach ((array) ($_POST['qty'] ?? []) as $varId => $qty) {
            $shopCart->update((string) $varId, (int) $qty);
        }
        if (isset($_POST['shipping_method'])) {
            $shopCart->setShippingMethod((string) $_POST['shipping_method']);
        }
        if (isset($_POST['email'])) {
            $shopCart->setEmail((string) $_POST['email']);
        }
        header('Location: ' . url('cart/'));
        exit;
    }
    if ($action === 'remove') {
        $shopCart->remove((string) ($_POST['variation_id'] ?? ''));
        header('Location: ' . url('cart/'));
        exit;
    }
}

$detailed = $shopCart->detailed();
if (!empty($_SESSION['shop_flash']) && is_array($_SESSION['shop_flash'])) {
    $flash = $_SESSION['shop_flash'];
    unset($_SESSION['shop_flash']);
}
$page_title = "Cart | DJ's Tackle";
$canonical_path = '/cart/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1>Your cart</h1>
  </div>
</section>
<section class="section" style="padding-top:0">
  <div class="container">
    <?php if (!empty($flash)): ?>
      <div class="flash <?= Helpers::e($flash[0]) ?>"><?= Helpers::e($flash[1]) ?></div>
    <?php endif; ?>
    <?php foreach ($detailed['errors'] as $err): ?>
      <div class="flash err"><?= Helpers::e($err) ?></div>
    <?php endforeach; ?>

    <?php if (!$detailed['lines']): ?>
      <div class="card">
        <p>Your cart is empty.</p>
        <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Continue shopping</a>
      </div>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="action" value="update">
        <table class="cart-table">
          <thead>
            <tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($detailed['lines'] as $line): ?>
              <tr>
                <td>
                  <div style="display:flex;gap:.75rem;align-items:center">
                    <img src="<?= Helpers::e($line['local_image'] && is_file(dirname(__DIR__) . '/' . $line['local_image']) ? url($line['local_image']) : ($line['image_url'] ?: url('assets/img/placeholder.svg'))) ?>" alt="">
                    <div>
                      <a href="<?= Helpers::e(url('product/?slug=' . urlencode($line['product_slug']))) ?>"><strong><?= Helpers::e($line['product_name']) ?></strong></a>
                      <?php if ($line['variation_name'] && $line['variation_name'] !== 'Default'): ?>
                        <div class="muted"><?= Helpers::e($line['variation_name']) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td><?= Helpers::e(Helpers::money($line['unit_price_cents'])) ?></td>
                <td><input type="number" min="1" name="qty[<?= Helpers::e($line['variation_id']) ?>]" value="<?= (int) $line['qty'] ?>" style="width:4.5rem"></td>
                <td><?= Helpers::e(Helpers::money($line['line_total_cents'])) ?></td>
                <td>
                  <button class="btn btn-ghost" type="submit" formaction="<?= Helpers::e(url('cart/remove.php')) ?>" formmethod="post" name="variation_id" value="<?= Helpers::e($line['variation_id']) ?>">Remove</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="shop-layout" style="margin-top:1.5rem">
          <div class="card">
            <h3>Shipping</h3>
            <label><input type="radio" name="shipping_method" value="flat"<?= $detailed['shipping_method'] === 'flat' ? ' checked' : '' ?>> Ship — <?= Helpers::e($detailed['shipping_label']) ?> (<?= Helpers::e(Helpers::money($detailed['shipping_cents'])) ?>)</label><br>
            <?php if ($shopConfig->pickupEnabled): ?>
              <label><input type="radio" name="shipping_method" value="pickup"<?= $detailed['shipping_method'] === 'pickup' ? ' checked' : '' ?>> <?= Helpers::e($shopConfig->pickupLabel) ?> (Free)</label>
            <?php endif; ?>
            <label style="display:block;margin-top:1rem">Email for cart recovery
              <input type="email" name="email" value="<?= Helpers::e($shopCart->email() ?? ($shopAccount['email'] ?? '')) ?>" placeholder="you@example.com">
            </label>
            <p class="muted" style="margin:.4rem 0 0;font-size:.9rem">Order receipts and shipping updates come from Square after checkout.</p>
            <button class="btn btn-secondary" type="submit">Update cart</button>
          </div>
          <div class="card">
            <p>Subtotal: <strong><?= Helpers::e(Helpers::money($detailed['subtotal_cents'])) ?></strong></p>
            <p>Shipping: <strong><?= Helpers::e(Helpers::money($detailed['shipping_cents'])) ?></strong></p>
            <p style="font-size:1.2rem">Total: <strong><?= Helpers::e(Helpers::money($detailed['total_cents'])) ?></strong></p>
            <p class="muted">Tax calculated at checkout (Michigan 6% where applicable).</p>
            <a class="btn btn-primary" href="<?= Helpers::e(url('checkout/')) ?>">Checkout</a>
          </div>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

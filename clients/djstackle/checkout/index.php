<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$detailed = $shopCart->detailed();
if ($detailed['lines'] === []) {
    if (!empty($detailed['errors'])) {
        $_SESSION['shop_flash'] = ['err', implode(' ', $detailed['errors'])];
    } else {
        $_SESSION['shop_flash'] = ['err', 'Your cart is empty. Add an item from the shop, then checkout.'];
    }
    header('Location: ' . url('cart/'));
    exit;
}

$prefill = $shopAccounts->prefillForCheckout($shopAccount) ?? [];
$addr = is_array($prefill['address'] ?? null) ? $prefill['address'] : [];
$email = (string) ($prefill['email'] ?? $shopCart->email() ?? '');
$phone = (string) ($prefill['phone'] ?? '');
$name = (string) ($shopAccount['name'] ?? '');
$method = $shopCart->shippingMethod();

$states = [
    'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD',
    'MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC',
    'SD','TN','TX','UT','VT','VA','WA','WV','WI','WY','DC',
];

$page_title = "Checkout | DJ's Tackle";
$canonical_path = '/checkout/';
$meta_robots = 'noindex, nofollow';
$body_class = 'checkout-page';
$extra_head = '<script src="' . Helpers::e($shopConfig->webPaymentsSdkUrl()) . '"></script>';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1>Checkout</h1>
  </div>
</section>
<section class="section" style="padding-top:0">
  <div class="container checkout-layout">
    <form id="checkout-form" class="checkout-form" novalidate>
      <div class="card">
        <h2>Contact</h2>
        <label>Name
          <input type="text" name="name" required autocomplete="name" value="<?= Helpers::e($name) ?>">
        </label>
        <label>Email
          <input type="email" name="email" required autocomplete="email" value="<?= Helpers::e($email) ?>">
        </label>
        <label>Phone
          <input type="tel" name="phone" autocomplete="tel" value="<?= Helpers::e($phone) ?>">
        </label>
      </div>

      <div class="card">
        <h2>Fulfillment</h2>
        <label class="radio-row">
          <input type="radio" name="shipping_method" value="flat"<?= $method !== 'pickup' ? ' checked' : '' ?>>
          Ship — USPS Ground Advantage
        </label>
        <?php if ($shopConfig->pickupEnabled): ?>
          <label class="radio-row">
            <input type="radio" name="shipping_method" value="pickup"<?= $method === 'pickup' ? ' checked' : '' ?>>
            <?= Helpers::e($shopConfig->pickupLabel) ?> (Free)
          </label>
        <?php endif; ?>

        <div data-shipping-fields class="shipping-fields"<?= $method === 'pickup' ? ' hidden' : '' ?>>
          <h3>Shipping address</h3>
          <label>Address
            <input type="text" name="address_line1" autocomplete="address-line1" value="<?= Helpers::e((string) ($addr['line1'] ?? $addr['address_line1'] ?? '')) ?>">
          </label>
          <label>Apt / suite
            <input type="text" name="address_line2" autocomplete="address-line2" value="<?= Helpers::e((string) ($addr['line2'] ?? $addr['address_line2'] ?? '')) ?>">
          </label>
          <div class="field-row">
            <label>City
              <input type="text" name="address_city" autocomplete="address-level2" value="<?= Helpers::e((string) ($addr['city'] ?? $addr['locality'] ?? '')) ?>">
            </label>
            <label>State
              <select name="address_state" autocomplete="address-level1">
                <option value="">—</option>
                <?php
                $selState = strtoupper((string) ($addr['state'] ?? $addr['administrative_district_level_1'] ?? 'MI'));
                foreach ($states as $st):
                ?>
                  <option value="<?= $st ?>"<?= $selState === $st ? ' selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>ZIP
              <input type="text" name="address_postal" autocomplete="postal-code" value="<?= Helpers::e((string) ($addr['postal'] ?? $addr['postal_code'] ?? '')) ?>">
            </label>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Coupon</h2>
        <div class="inline-apply">
          <input type="text" name="coupon" placeholder="Code" autocomplete="off">
          <button type="button" class="btn btn-secondary" data-apply-coupon>Apply</button>
        </div>
        <p class="muted" data-coupon-msg></p>
      </div>

      <?php if (!$shopConfig->demoMode): ?>
      <div class="card">
        <h2>Gift card</h2>
        <div id="gift-card-container"></div>
        <button type="button" class="btn btn-secondary" data-apply-gift style="margin-top:.6rem">Apply gift card</button>
        <p class="muted" data-gift-msg></p>
      </div>
      <?php endif; ?>
    </form>

    <aside class="checkout-summary">
      <div class="card">
        <h2>Order summary</h2>
        <ul class="checkout-lines">
          <?php foreach ($detailed['lines'] as $line): ?>
            <li>
              <span>
                <strong><?= Helpers::e($line['product_name']) ?></strong>
                <?php if ($line['variation_name'] && !in_array($line['variation_name'], ['Default', 'Regular'], true)): ?>
                  <span class="muted">(<?= Helpers::e($line['variation_name']) ?>)</span>
                <?php endif; ?>
                × <?= (int) $line['qty'] ?>
              </span>
              <span><?= Helpers::e(Helpers::money($line['line_total_cents'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <dl class="checkout-totals" data-totals>
          <div><dt>Subtotal</dt><dd data-total="subtotal"><?= Helpers::e(Helpers::money($detailed['subtotal_cents'])) ?></dd></div>
          <div><dt>Shipping</dt><dd data-total="shipping"><?= Helpers::e(Helpers::money($detailed['shipping_cents'])) ?></dd></div>
          <div><dt>Tax</dt><dd data-total="tax">—</dd></div>
          <div data-discount-row hidden><dt>Discount</dt><dd data-total="discount">—</dd></div>
          <div data-gift-row hidden><dt>Gift card</dt><dd data-total="gift">—</dd></div>
          <div class="due"><dt>Due</dt><dd data-total="due"><?= Helpers::e(Helpers::money($detailed['total_cents'])) ?></dd></div>
        </dl>

        <?php if ($shopConfig->demoMode): ?>
          <button type="button" class="btn btn-primary btn-block" data-demo-pay>Place demo order</button>
          <p class="muted">Demo mode — no real charge.</p>
        <?php else: ?>
          <div class="wallet-buttons">
            <div id="apple-pay"></div>
            <div id="google-pay"></div>
            <div id="cash-app-pay"></div>
          </div>
          <div id="card-container"></div>
          <button type="button" class="btn btn-primary btn-block" data-card-pay disabled>Pay now</button>
        <?php endif; ?>
        <p class="flash err" data-checkout-error hidden></p>
        <p class="muted"><a href="<?= Helpers::e(url('cart/')) ?>">← Back to cart</a></p>
      </div>
    </aside>
  </div>
</section>

<script>
  window.DJT_CHECKOUT = {
    calcUrl: <?= json_encode(url('checkout/calc.php')) ?>,
    payUrl: <?= json_encode(url('checkout/pay.php')) ?>,
    appId: <?= json_encode($shopConfig->squareApplicationId) ?>,
    locationId: <?= json_encode($shopConfig->squareLocationId) ?>,
    demo: <?= $shopConfig->demoMode ? 'true' : 'false' ?>,
    currency: <?= json_encode($shopConfig->currency) ?>,
    country: 'US'
  };
</script>
<script src="<?= Helpers::e(url('../shared/shop/assets/checkout.js')) ?>" defer></script>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

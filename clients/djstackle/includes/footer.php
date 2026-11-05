<?php

declare(strict_types=1);
?>
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong><?= \Webstar\Shop\Helpers::e($site_name) ?></strong>
            <p><?= \Webstar\Shop\Helpers::e($site_tagline) ?></p>
            <p><a href="<?= \Webstar\Shop\Helpers::e($phone_href) ?>"><?= \Webstar\Shop\Helpers::e($phone) ?></a><br>
            <a href="<?= \Webstar\Shop\Helpers::e($email_href) ?>"><?= \Webstar\Shop\Helpers::e($email) ?></a></p>
            <p><?= \Webstar\Shop\Helpers::e($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?></p>
        </div>
        <div>
            <strong>Shop</strong>
            <ul class="footer-list">
                <li><a href="<?= \Webstar\Shop\Helpers::e(url('shop/')) ?>">All products</a></li>
                <li><a href="<?= \Webstar\Shop\Helpers::e(url('gift-cards/')) ?>">Gift cards</a></li>
                <li><a href="<?= \Webstar\Shop\Helpers::e(url('cart/')) ?>">Cart</a></li>
            </ul>
        </div>
        <div>
            <strong>Help</strong>
            <ul class="footer-list">
                <?php foreach ($footer_links as $label => $href): ?>
                    <li><a href="<?= \Webstar\Shop\Helpers::e(url(ltrim($href, '/'))) ?>"><?= \Webstar\Shop\Helpers::e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= \Webstar\Shop\Helpers::e($company_name) ?></span>
        <?php if (!empty($shopConfig) && $shopConfig->demoMode): ?>
            <span class="demo-pill">Demo mode — Square sandbox not connected</span>
        <?php endif; ?>
    </div>
</footer>
<div class="cart-drawer" data-cart-drawer hidden>
    <div class="cart-drawer-panel">
        <header>
            <strong>Cart</strong>
            <button type="button" data-cart-close aria-label="Close cart">&times;</button>
        </header>
        <div data-cart-drawer-body></div>
        <footer style="display:flex;flex-direction:column;gap:.5rem">
            <a class="btn btn-primary" href="<?= \Webstar\Shop\Helpers::e(url('checkout/')) ?>">Checkout</a>
            <a class="btn btn-secondary" href="<?= \Webstar\Shop\Helpers::e(url('cart/')) ?>">View cart</a>
        </footer>
    </div>
</div>
<script>
  window.DJT = {
    base: <?= json_encode(url()) ?>,
    cartCount: <?= (int) $cart_count ?>,
    csrf: <?= json_encode(session_id() ?: '') ?>
  };
</script>
<script src="<?= \Webstar\Shop\Helpers::e(url('../shared/shop/assets/shop.js')) ?>" defer></script>
<script src="<?= \Webstar\Shop\Helpers::e(url('assets/js/main.js')) ?>" defer></script>
</body>
</html>

<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Mailer;

$flash = null;
$form = [
    'store_name' => '',
    'name' => '',
    'email' => '',
    'phone' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $honeypot = trim((string) ($_POST['website'] ?? ''));
    $form['store_name'] = trim((string) ($_POST['store_name'] ?? ''));
    $form['name'] = trim((string) ($_POST['name'] ?? ''));
    $form['email'] = trim((string) ($_POST['email'] ?? ''));
    $form['phone'] = trim((string) ($_POST['phone'] ?? ''));
    $form['message'] = trim((string) ($_POST['message'] ?? ''));

    if ($honeypot !== '') {
        $flash = ['ok', 'Thanks — we received your dealer order request. We will follow up by email or phone.'];
        $form = ['store_name' => '', 'name' => '', 'email' => '', 'phone' => '', 'message' => ''];
    } elseif (
        $form['store_name'] === ''
        || $form['name'] === ''
        || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)
        || $form['phone'] === ''
        || $form['message'] === ''
    ) {
        $flash = ['err', 'Please complete all required fields and use a valid email address.'];
    } else {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
        $html = '<p><strong>Dealer order request</strong></p>'
            . '<p><strong>Store:</strong> ' . Helpers::e($form['store_name']) . '</p>'
            . '<p><strong>Contact:</strong> ' . Helpers::e($form['name']) . '</p>'
            . '<p><strong>Email:</strong> ' . Helpers::e($form['email']) . '</p>'
            . '<p><strong>Phone:</strong> ' . Helpers::e($form['phone']) . '</p>'
            . '<p><strong>Order details:</strong></p><p>' . nl2br(Helpers::e($form['message'])) . '</p>'
            . '<p><small>Submitted ' . Helpers::e(date('c'))
            . ' · host ' . Helpers::e($host)
            . ($ip !== '' ? ' · IP ' . Helpers::e($ip) : '')
            . '</small></p>';

        Mailer::make($shopConfig)->send(
            $shopConfig->ownerEmail !== '' ? $shopConfig->ownerEmail : $email,
            'Dealer order: ' . $form['store_name'],
            $html
        );
        $flash = ['ok', 'Thanks — we received your dealer order request. We will follow up by email or phone.'];
        $form = ['store_name' => '', 'name' => '', 'email' => '', 'phone' => '', 'message' => ''];
    }
}

$page_title = "Dealerships | DJ's Tackle";
$page_description = "Wholesale and restock orders for bait shops and outfitters carrying DJ's Tackle. See our retail partners and submit a dealer order request. Sterling Heights, MI.";
$canonical_path = '/dealerships/';
$og_image = absolute_url('assets/img/djartek-detroit-974867_1920.jpg');
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero page-hero--brand page-hero--dealerships">
  <div class="hero-brand__media" aria-hidden="true">
    <img
      src="<?= Helpers::e(url('assets/img/djartek-detroit-974867_1920.jpg')) ?>"
      alt=""
      width="1920"
      height="1280"
      fetchpriority="high"
      decoding="async"
    >
  </div>
  <div class="hero-brand__shade" aria-hidden="true"></div>
  <div class="container">
    <div class="eyebrow">Wholesale &amp; restock</div>
    <h1>Dealerships</h1>
    <p class="muted">Tackle stores and fishermen are choosing DJ's Tackle for chip-free jigs, stinger hooks, crawler harnesses, bottom bouncers, and more.</p>
    <p class="dealerships-hero-cta">
      <a class="btn btn-primary" href="<?= Helpers::e($phone_href) ?>">Call to place an order</a>
      <a class="btn btn-secondary" href="#dealer-order">Or send a request</a>
    </p>
  </div>
</section>

<section class="section dealerships-intro">
  <div class="container shop-layout dealerships-layout">
    <div class="prose dealerships-copy">
      <h2>How dealer orders work</h2>
      <p>DJ's Tackle is family-owned and hand-made in Sterling Heights, Michigan. If your store already carries our jigs, River Ready boxes, hooks, or plastics — or you'd like to start — <strong>call us first</strong>. We'll walk through your list, confirm availability and pricing, and get your restock moving. Prefer to write it out? Use the form below and we will review and confirm your order, then follow up by phone or email.</p>
      <ul class="dealerships-steps">
        <li><strong>Call first</strong> — The fastest way to place or restock a dealer order is to call and talk through what you need.</li>
        <li><strong>Or send your list</strong> — Include product names, sizes/weights, colors, and quantities in the order details box.</li>
        <li><strong>Custom colors</strong> — Note custom pours when you call or in your message; they may ship with the next production batch.</li>
        <li><strong>Arctic Spinners</strong> — We're an authorized dealer; special colors may take extra time (we order from Arctic Spinners when needed).</li>
        <li><strong>Fulfillment</strong> — We confirm lead time, shipping or delivery options, and payment before the order is built.</li>
      </ul>
    </div>
    <aside class="card dealerships-help">
      <h3>Call first</h3>
      <p><strong>Phone</strong><br><a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a></p>
      <p><a class="btn btn-primary" href="<?= Helpers::e($phone_href) ?>">Call now</a></p>
      <p><strong>Email</strong><br><a href="<?= Helpers::e($email_href) ?>"><?= Helpers::e($email) ?></a></p>
      <p class="muted">Can't call right now? <a href="#dealer-order">Send a dealer order request</a>.</p>
      <p class="muted">Not a retailer? Shop the <a href="<?= Helpers::e(url('shop/')) ?>">online store</a> or find us at the shops below.</p>
    </aside>
  </div>
</section>

<section class="section dealerships-partners">
  <div class="container">
    <?php
    $retail_stores_heading = 'Outfitters we supply';
    $retail_stores_lead = "These bait shops and tackle retailers carry DJ's Tackle — ask for us by name on your next visit.";
    require dirname(__DIR__) . '/includes/partials/retail-stores-marquee.php';
    ?>
  </div>
</section>

<section class="section" id="dealer-order">
  <div class="container dealerships-form-wrap">
    <div class="section-head">
      <div>
        <h2>Dealer order form</h2>
        <p>Can't reach us by phone? Send a restock or opening order request here — we'll reply to confirm details, usually within one business day. Prefer to talk it through? <a href="<?= Helpers::e($phone_href) ?>">Call <?= Helpers::e($phone) ?></a>.</p>
      </div>
    </div>
    <form method="post" class="card contact-form dealer-order-form">
      <?php if ($flash): ?>
        <div class="flash <?= Helpers::e($flash[0]) ?>"><?= Helpers::e($flash[1]) ?></div>
      <?php endif; ?>

      <label class="visually-hidden" for="dealer-website" aria-hidden="true">Website</label>
      <input
        id="dealer-website"
        class="visually-hidden"
        type="text"
        name="website"
        value=""
        tabindex="-1"
        autocomplete="off"
      >

      <label>Store / business name
        <input name="store_name" required value="<?= Helpers::e($form['store_name']) ?>">
      </label>
      <label>Your name
        <input name="name" required value="<?= Helpers::e($form['name']) ?>">
      </label>
      <label>Email
        <input type="email" name="email" required value="<?= Helpers::e($form['email']) ?>">
      </label>
      <label>Phone
        <input type="tel" name="phone" required value="<?= Helpers::e($form['phone']) ?>">
      </label>
      <label>Order details
        <textarea name="message" rows="8" required placeholder="Example:&#10;- 1/4 oz jig heads, Fire Tiger, qty 48&#10;- 3/8 oz jig heads, Purple Pearl, qty 24&#10;- River Ready Box — Detroit River, qty 12"><?= Helpers::e($form['message']) ?></textarea>
      </label>
      <button class="btn btn-primary" type="submit">Send order request</button>
    </form>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

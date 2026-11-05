<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$flash = null;
$host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
global $base_path;
$verifyUrl = $scheme . '://' . $host . ($base_path ?? '') . '/account/verify.php';

if (isset($_GET['logout'])) {
    $shopAccounts->logout();
    header('Location: ' . url('account/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'login') {
            $shopAccounts->requestMagicLink((string) ($_POST['email'] ?? ''), $verifyUrl);
            $flash = ['ok', 'Check your email for a sign-in link (also logged to data/mail.log in demo mode).'];
        } elseif ($action === 'profile' && $shopAccount) {
            $shopAccounts->updateProfile(
                $shopAccount['id'],
                (string) ($_POST['name'] ?? ''),
                (string) ($_POST['phone'] ?? ''),
                [
                    'line1' => (string) ($_POST['line1'] ?? ''),
                    'city' => (string) ($_POST['city'] ?? ''),
                    'region' => (string) ($_POST['region'] ?? ''),
                    'postal' => (string) ($_POST['postal'] ?? ''),
                ]
            );
            $shopAccount = $shopAccounts->current();
            $flash = ['ok', 'Profile saved. Your address will prefill Square checkout.'];
        }
    } catch (Throwable $e) {
        $flash = ['err', $e->getMessage()];
    }
}

$addr = $shopAccount && $shopAccount['shipping_address_json']
    ? (json_decode((string) $shopAccount['shipping_address_json'], true) ?: [])
    : [];

$page_title = "Account | DJ's Tackle";
$canonical_path = '/account/';
$meta_robots = 'noindex, nofollow';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Account</h1></div></section>
<section class="section" style="padding-top:0">
  <div class="container stack">
    <?php if ($flash): ?><div class="flash <?= Helpers::e($flash[0]) ?>"><?= Helpers::e($flash[1]) ?></div><?php endif; ?>

    <?php if (!$shopAccount): ?>
      <form method="post" class="card account-form" style="max-width:420px">
        <input type="hidden" name="action" value="login">
        <h2>Sign in with email</h2>
        <p class="muted">We’ll send a one-time magic link — no password.</p>
        <label>Email<input type="email" name="email" required></label>
        <button class="btn btn-primary" type="submit">Send sign-in link</button>
      </form>
    <?php else: ?>
      <div class="card">
        <p>Signed in as <strong><?= Helpers::e($shopAccount['email']) ?></strong>
          <a href="<?= Helpers::e(url('account/?logout=1')) ?>">Sign out</a></p>
      </div>
      <form method="post" class="card account-form">
        <input type="hidden" name="action" value="profile">
        <h2>Profile &amp; shipping</h2>
        <label>Name<input name="name" value="<?= Helpers::e($shopAccount['name'] ?? '') ?>"></label>
        <label>Phone<input name="phone" value="<?= Helpers::e($shopAccount['phone'] ?? '') ?>"></label>
        <label>Address<input name="line1" value="<?= Helpers::e($addr['line1'] ?? '') ?>"></label>
        <label>City<input name="city" value="<?= Helpers::e($addr['city'] ?? '') ?>"></label>
        <label>State<input name="region" value="<?= Helpers::e($addr['region'] ?? 'MI') ?>"></label>
        <label>ZIP<input name="postal" value="<?= Helpers::e($addr['postal'] ?? '') ?>"></label>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
      <div class="card">
        <h2>Orders</h2>
        <p class="muted">Receipts, shipping, and pickup are managed in <strong>Square</strong>. Check the email Square sent after checkout, or call <?= Helpers::e($phone) ?> with questions.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

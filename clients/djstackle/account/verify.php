<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$token = (string) ($_GET['token'] ?? '');
$account = $token !== '' ? $shopAccounts->verifyMagicLink($token) : null;
if ($account) {
    $shopWishlist->mergeToAccount($account['id']);
    header('Location: ' . url('account/'));
    exit;
}

$page_title = 'Sign-in link expired';
$meta_robots = 'noindex, nofollow';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="flash err">That sign-in link is invalid or expired.</div>
    <a class="btn btn-primary" href="<?= Helpers::e(url('account/')) ?>">Request a new link</a>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

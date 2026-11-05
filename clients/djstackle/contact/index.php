<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;
use Webstar\Shop\Mailer;

$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $from = trim((string) ($_POST['email'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    if ($name === '' || !filter_var($from, FILTER_VALIDATE_EMAIL) || $message === '') {
        $flash = ['err', 'Please fill in name, a valid email, and a message.'];
    } else {
        $html = '<p>Contact form from ' . Helpers::e($name) . ' &lt;' . Helpers::e($from) . '&gt;</p><p>'
            . nl2br(Helpers::e($message)) . '</p>';
        Mailer::make($shopConfig)->send($shopConfig->ownerEmail, 'Contact form: ' . $name, $html);
        $flash = ['ok', 'Thanks — we will get back to you soon. Or call ' . $phone . '.'];
    }
}

$page_title = "Contact | DJ's Tackle";
$canonical_path = '/contact/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>Contact</h1><p class="muted">Questions, custom colors, or pickup — we're happy to help.</p></div></section>
<section class="section" style="padding-top:0"><div class="container shop-layout">
  <div class="card">
    <p><strong>Phone</strong><br><a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a></p>
    <p><strong>Email</strong><br><a href="<?= Helpers::e($email_href) ?>"><?= Helpers::e($email) ?></a></p>
    <p><strong>Location</strong><br><?= Helpers::e($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?><br><span class="muted"><?= Helpers::e($address['note']) ?></span></p>
  </div>
  <form method="post" class="card contact-form">
    <?php if ($flash): ?><div class="flash <?= Helpers::e($flash[0]) ?>"><?= Helpers::e($flash[1]) ?></div><?php endif; ?>
    <label>Name<input name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Message<textarea name="message" rows="5" required></textarea></label>
    <button class="btn btn-primary" type="submit">Send message</button>
  </form>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

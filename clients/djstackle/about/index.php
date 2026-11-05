<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "About | DJ's Tackle";
$page_description = "DJ's Tackle was created by fishermen for fishermen — a family-owned LLC in Sterling Heights, MI.";
$canonical_path = '/about/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><div class="eyebrow">Our story</div><h1>About DJ's Tackle</h1></div></section>
<section class="section" style="padding-top:0"><div class="container prose">
  <p>DJ's Tackle was created by fisherman for fisherman. A one-stop-shop for your walleye jigging needs. We are a small family owned and operated LLC and thrive being a local business in Sterling Heights, MI.</p>
  <p>Our goal is to provide quality, chip-free jig heads as well as durable plastics — some of our colors are sourced locally from Rip Dip Fishing, some of the best on the market today.</p>
  <p>We hope you enjoy our small business just as much as we do. We are always available if you have questions or concerns. We are able to do custom colors — if you don't see something you are looking for, please reach out.</p>
  <p><a href="<?= Helpers::e($phone_href) ?>"><?= Helpers::e($phone) ?></a> · <a href="<?= Helpers::e($email_href) ?>"><?= Helpers::e($email) ?></a></p>
  <p>Thank you for supporting our small business!</p>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

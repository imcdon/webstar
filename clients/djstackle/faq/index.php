<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$faqs = [
    ['q' => 'Do you offer local pickup?', 'a' => 'Yes. Choose local pickup at checkout (Sterling Heights, MI) and we will confirm arrangements by phone or email.'],
    ['q' => 'How is shipping calculated?', 'a' => 'We ship USPS Ground Advantage at a flat rate. Orders over $75 ship free.'],
    ['q' => 'Can I get custom colors?', 'a' => 'Absolutely. Contact us with what you need and we will work with you.'],
    ['q' => 'Where do your plastics come from?', 'a' => 'Some colors are sourced locally from Rip Dip Fishing.'],
    ['q' => 'What payment methods do you accept?', 'a' => 'Checkout is powered by Square — cards, Apple Pay, Google Pay, and Cash App Pay where available. Coupons apply on the Square checkout page.'],
    ['q' => 'How do I track my order?', 'a' => 'Square emails your receipt after checkout. Shipping and pickup are fulfilled through Square — contact us with that receipt if you need help.'],
    ['q' => 'How do gift cards work?', 'a' => 'Purchase a Square eGift card from our Gift Cards page. Redeem it during Square checkout.'],
];

$page_title = "FAQ | DJ's Tackle";
$canonical_path = '/faq/';
$json_ld = [[
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ], $faqs),
]];
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><h1>FAQ</h1></div></section>
<section class="section" style="padding-top:0"><div class="container prose">
  <?php foreach ($faqs as $f): ?>
    <h2><?= Helpers::e($f['q']) ?></h2>
    <p><?= Helpers::e($f['a']) ?></p>
  <?php endforeach; ?>
</div></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>

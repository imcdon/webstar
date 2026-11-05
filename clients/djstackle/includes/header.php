<?php

declare(strict_types=1);

$page_title = $page_title ?? $site_name;
$page_description = $page_description ?? $site_description;
$body_class = $body_class ?? '';
$canonical_path = $canonical_path ?? current_path();
$canonical_url = absolute_url(ltrim($canonical_path === '/' ? '' : $canonical_path, '/'));
$og_image = $og_image ?? absolute_url('assets/img/og-default.svg');
$og_type = $og_type ?? 'website';
$meta_robots = $meta_robots ?? null;
$json_ld = $json_ld ?? [];

$path_now = current_path();
if ($meta_robots === null && (str_starts_with($path_now, '/admin') || str_contains($path_now, '/admin/'))) {
    $meta_robots = 'noindex, nofollow';
}

$staging_banner = __DIR__ . '/partials/webstar-staging-banner.php';
$hostNow = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$show_staging_banner = str_contains($hostNow, 'webstarbusinessservices.com');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= Helpers::e($page_description) ?>">
    <?php if ($meta_robots): ?>
        <meta name="robots" content="<?= Helpers::e($meta_robots) ?>">
    <?php endif; ?>
    <title><?= Helpers::e($page_title) ?></title>
    <link rel="canonical" href="<?= Helpers::e($canonical_url) ?>">
    <meta property="og:site_name" content="<?= Helpers::e($site_name) ?>">
    <meta property="og:title" content="<?= Helpers::e($page_title) ?>">
    <meta property="og:description" content="<?= Helpers::e($page_description) ?>">
    <meta property="og:url" content="<?= Helpers::e($canonical_url) ?>">
    <meta property="og:type" content="<?= Helpers::e($og_type) ?>">
    <meta property="og:image" content="<?= Helpers::e($og_image) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= Helpers::e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= Helpers::e(url('assets/css/main.css')) ?>">
    <link rel="stylesheet" href="<?= Helpers::e(url('../shared/shop/assets/shop.css')) ?>">
    <?php foreach ((array) $json_ld as $block): ?>
        <?= Helpers::jsonLd($block) ?>
    <?php endforeach; ?>
    <?= $extra_head ?? '' ?>
</head>
<body class="<?= Helpers::e($body_class) ?>"<?= !empty($body_attrs) ? ' ' . $body_attrs : '' ?>>
<?php if ($show_staging_banner && is_file($staging_banner)) { require $staging_banner; } ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= Helpers::e(url()) ?>">
            <span class="brand-mark">DJ</span>
            <span class="brand-text">
                <strong>DJ's Tackle</strong>
                <small>Sterling Heights, MI</small>
            </span>
        </a>
        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>Menu</button>
        <nav id="site-nav" class="site-nav" data-nav>
            <?php foreach ($nav_links as $label => $href): ?>
                <a href="<?= Helpers::e(url(ltrim($href, '/'))) ?>"<?= str_starts_with($path_now, rtrim($href, '/')) ? ' aria-current="page"' : '' ?>><?= Helpers::e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="header-actions">
            <a class="icon-link" href="<?= Helpers::e(url('account/')) ?>" title="Account">Account</a>
            <a class="icon-link" href="<?= Helpers::e(url('wishlist/')) ?>" title="Wishlist">Wishlist</a>
            <a class="cart-link" href="<?= Helpers::e(url('cart/')) ?>">
                Cart
                <span class="cart-badge" data-cart-count><?= (int) $cart_count ?></span>
            </a>
        </div>
    </div>
</header>
<main id="main">

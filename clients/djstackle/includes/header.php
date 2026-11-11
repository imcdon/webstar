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
    <link rel="icon" href="<?= Helpers::e(url('assets/img/favicon.png')) ?>" type="image/png">
    <link rel="icon" href="<?= Helpers::e(url('assets/img/favicon-32.png')) ?>" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= Helpers::e(url('assets/img/apple-touch-icon.png')) ?>">
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
            <img
                class="brand-logo"
                src="<?= Helpers::e(url('assets/img/logo-nav.png')) ?>"
                alt="DJ's Tackle — Sterling Heights, MI"
                width="260"
                height="79"
            >
        </a>
        <div class="header-actions">
            <a class="cart-link" href="<?= Helpers::e(url('cart/')) ?>">
                Cart
                <span class="cart-badge" data-cart-count><?= (int) $cart_count ?></span>
            </a>
            <button
                type="button"
                class="nav-toggle"
                aria-expanded="false"
                aria-controls="site-nav"
                aria-label="Open menu"
                data-nav-toggle
            >
                <span class="nav-toggle-bars" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <div id="site-nav" class="nav-panel" data-nav hidden>
        <div class="container nav-panel-inner">
            <form class="nav-search" method="get" action="<?= Helpers::e(url('shop/')) ?>" role="search">
                <label class="visually-hidden" for="nav-search-q">Search products</label>
                <input
                    id="nav-search-q"
                    type="search"
                    name="q"
                    placeholder="Search jigs, boxes…"
                    autocomplete="off"
                    data-nav-search
                >
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
            <nav class="site-nav" aria-label="Main">
                <?php foreach ($nav_links as $label => $href): ?>
                    <a href="<?= Helpers::e(url(ltrim($href, '/'))) ?>"<?= str_starts_with($path_now, rtrim($href, '/')) ? ' aria-current="page"' : '' ?>><?= Helpers::e($label) ?></a>
                <?php endforeach; ?>
                <a href="<?= Helpers::e(url('account/')) ?>">Account</a>
                <a href="<?= Helpers::e(url('wishlist/')) ?>">Wishlist</a>
            </nav>
        </div>
    </div>
</header>
<main id="main">

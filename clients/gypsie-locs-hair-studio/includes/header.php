<?php

declare(strict_types=1);

require_once __DIR__ . '/schema.php';

$page_title = $page_title ?? $site_name;
$page_description = $page_description ?? $site_description;
$body_class = $body_class ?? '';
$canonical_path = $canonical_path ?? current_path();
$canonical_url = absolute_url(ltrim($canonical_path === '/' ? '' : $canonical_path, '/'));
$og_image = $og_image ?? absolute_url('assets/img/og-default.svg');
$og_type = $og_type ?? 'website';
$meta_robots = $meta_robots ?? 'noindex, nofollow';
$schema_home = $schema_home ?? false;
$schema_faq = $schema_faq ?? false;
$current = current_path();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="robots" content="<?= htmlspecialchars($meta_robots) ?>">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($site_name) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:type" content="<?= htmlspecialchars($og_type) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#1B3A4B">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,500;6..96,600&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/css/main.css')) ?>?v=<?= (int) @filemtime(dirname(__DIR__) . '/assets/css/main.css') ?>">
    <?php
    if ($schema_home) {
        glhs_print_json_ld(glhs_schema_local_business());
    }
    if ($schema_faq && !empty($faq_items)) {
        glhs_print_json_ld(glhs_schema_faq($faq_items));
    }
    ?>
</head>
<body<?= $body_class !== '' ? ' class="' . htmlspecialchars($body_class) . '"' : '' ?>>
    <?php require __DIR__ . '/partials/webstar-staging-banner.php'; ?>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header" id="site-header">
        <div class="header-inner">
            <a class="logo" href="<?= htmlspecialchars(url()) ?>">
                <span class="logo-mark" aria-hidden="true"></span>
                <span class="logo-text">
                    <span class="logo-text__name">Gypsie Locs</span>
                    <span class="logo-text__sub">Hair Studio</span>
                </span>
            </a>
            <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu">
                <span></span><span></span>
            </button>
            <nav id="site-nav" class="site-nav" aria-label="Primary">
                <ul>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <?php
                        $hrefPath = $href === '' ? '/' : '/' . trim($href, '/') . '/';
                        $isActive = ($href === '' && ($current === '/' || $current === ''))
                            || ($href !== '' && str_starts_with($current, '/' . trim($href, '/')));
                        ?>
                        <li>
                            <a href="<?= htmlspecialchars(url($href)) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn btn--header" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book online</a>
            </nav>
        </div>
    </header>
    <main id="main">

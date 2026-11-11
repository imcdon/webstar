<?php
/*
 * header.php - Opens the HTML document, head, and site header.
 */
require_once __DIR__ . '/schema.php';

$page_title = $page_title ?? $site_name;
$page_description = $page_description ?? $site_description;
$body_class = $body_class ?? '';
$canonical_path = $canonical_path ?? current_path();
$canonical_url = absolute_url(ltrim($canonical_path === '/' ? '' : $canonical_path, '/'));
$og_image = $og_image ?? ($hero_image !== '' ? (str_starts_with($hero_image, 'http') ? $hero_image : absolute_url(ltrim(str_replace(url(), '', $hero_image), '/'))) : absolute_url('assets/img/og-default.svg'));
$og_type = $og_type ?? 'website';
$meta_robots = $meta_robots ?? null;
$preload_image = $preload_image ?? null;
$schema_home = $schema_home ?? false;
$schema_faq = $schema_faq ?? false;
$schema_breadcrumbs = $schema_breadcrumbs ?? null;
$schema_service = $schema_service ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <?php if ($meta_robots): ?>
        <meta name="robots" content="<?= htmlspecialchars($meta_robots) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($site_name) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:type" content="<?= htmlspecialchars($og_type) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0a0a0c">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.png')) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars(url('assets/img/logo-tree.png')) ?>">
    <?php if ($preload_image): ?>
        <link rel="preload" as="image" href="<?= htmlspecialchars($preload_image) ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/css/main.css')) ?>?v=<?= (int) @filemtime(dirname(__DIR__) . '/assets/css/main.css') ?>">
    <?php
    if ($schema_home) {
        mdhl_print_json_ld(mdhl_schema_local_business());
    }
    if (is_array($schema_service) && !empty($schema_service['name'])) {
        mdhl_print_json_ld(mdhl_schema_service(
            (string) $schema_service['name'],
            (string) ($schema_service['description'] ?? $page_description),
            (string) ($schema_service['url'] ?? $canonical_url)
        ));
    }
    if ($schema_faq && !empty($faq_items)) {
        mdhl_print_json_ld(mdhl_schema_faq($faq_items));
    }
    if (is_array($schema_breadcrumbs) && $schema_breadcrumbs) {
        mdhl_print_json_ld(mdhl_schema_breadcrumbs($schema_breadcrumbs));
    }
    ?>
</head>
<body<?= $body_class !== '' ? ' class="' . htmlspecialchars($body_class) . '"' : '' ?>>
    <div class="home-glow" aria-hidden="true"></div>
    <?php require __DIR__ . '/partials/webstar-staging-banner.php'; ?>
    <header class="site-header" id="site-header">
        <div class="header-inner">
            <a class="logo" href="<?= htmlspecialchars(url()) ?>">
                <img class="logo-mark" src="<?= htmlspecialchars(url('assets/img/logo-tree.png')) ?>" alt="" width="36" height="36">
                <span class="logo-text"><?= htmlspecialchars($site_name) ?></span>
            </a>
            <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
            <nav id="site-nav" class="site-nav" aria-label="Primary">
                <ul>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <?php if ($label === 'Services' && !empty($service_nav)): ?>
                            <li class="nav-item--dropdown">
                                <button type="button" class="nav-dropdown-toggle" aria-expanded="false" aria-controls="nav-services">
                                    Services
                                </button>
                                <ul id="nav-services" class="nav-dropdown">
                                    <?php foreach ($service_nav as $svcLabel => $svcHref): ?>
                                        <li><a href="<?= htmlspecialchars(url($svcHref)) ?>"><?= htmlspecialchars($svcLabel) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?= htmlspecialchars(url($href)) ?>"><?= htmlspecialchars($label) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="nav-ctas">
                    <a class="btn btn-ghost" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                    <a class="btn btn-primary" href="<?= htmlspecialchars(url('contact/')) ?>">Get a quote</a>
                </div>
            </nav>
        </div>
    </header>
    <main id="main">

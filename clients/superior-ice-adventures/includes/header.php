<?php
/*
 * header.php - Opens the HTML document, head, and site header with navigation.
 */
$page_title = $page_title ?? $site_name;
$page_description = $page_description ?? $site_description;
$body_class = $body_class ?? '';
$canonical_path = $canonical_path ?? current_path();
$canonical_url = absolute_url(ltrim($canonical_path === '/' ? '' : $canonical_path, '/'));
$og_image = $og_image ?? absolute_url('assets/img/hero/inland-splake-fishing.webp');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($site_name) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.png')) ?>" type="image/png" sizes="64x64">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars(url('assets/img/apple-touch-icon.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/css/main.css')) ?>">
</head>
<body<?= $body_class !== '' ? ' class="' . htmlspecialchars($body_class) . '"' : '' ?>>
    <?php require __DIR__ . '/partials/webstar-staging-banner.php'; ?>
    <header class="site-header" id="site-header">
        <div class="header-inner container-wide">
            <a class="logo" href="<?= htmlspecialchars(url()) ?>">
                <span class="logo-text"><?= htmlspecialchars($site_name) ?></span>
            </a>

            <nav class="header-nav" aria-label="Main">
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="nav-menu" id="nav-toggle">
                    <span class="nav-toggle-bars" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="visually-hidden">Menu</span>
                </button>

                <ul class="nav-list" id="nav-menu">
                    <li class="nav-item has-dropdown">
                        <button type="button" class="nav-dropdown-toggle" aria-expanded="false">
                            Services
                            <svg class="chevron" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5L6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <ul class="nav-dropdown">
                            <?php foreach ($nav_services as $svc): ?>
                                <li>
                                    <a href="<?= htmlspecialchars(url(ltrim($svc['href'], '/'))) ?>">
                                        <?= htmlspecialchars($svc['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <li class="nav-item">
                            <a href="<?= htmlspecialchars(url(ltrim($href, '/'))) ?>"><?= htmlspecialchars($label) ?></a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav-item nav-phone-mobile">
                        <?php
                        $call_class = 'btn-call-sm';
                        require __DIR__ . '/partials/call-button.php';
                        ?>
                    </li>
                </ul>
            </nav>

            <div class="header-cta">
                <?php if ($phone !== ''): ?>
                    <a class="header-phone" href="<?= htmlspecialchars($phone_href) ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span><?= htmlspecialchars($phone) ?></span>
                    </a>
                <?php endif; ?>
                <?php require __DIR__ . '/partials/call-button.php'; ?>
            </div>
        </div>
    </header>
    <main>

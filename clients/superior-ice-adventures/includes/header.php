<?php
/*
 * header.php - Opens the HTML document, head, and site header with navigation.
 */
require_once __DIR__ . '/schema.php';

$page_title = $page_title ?? $site_name;
$page_description = $page_description ?? $site_description;
$body_class = $body_class ?? '';
$canonical_path = $canonical_path ?? current_path();
$canonical_url = absolute_url(ltrim($canonical_path === '/' ? '' : $canonical_path, '/'));
$og_image = $og_image ?? absolute_url('assets/img/hero/inland-splake-fishing.webp');
$og_type = $og_type ?? 'website';
$meta_robots = $meta_robots ?? null;
$preload_image = $preload_image ?? null;
$schema_type = $schema_type ?? null;
$schema_service = $schema_service ?? null;
$schema_article = $schema_article ?? null;
$schema_breadcrumbs = $schema_breadcrumbs ?? null;
$article_published_time = $article_published_time ?? null;
$article_modified_time = $article_modified_time ?? null;
$ga4_measurement_id = $ga4_measurement_id ?? '';

// Admin / CMS: never index
$path_now = current_path();
if ($meta_robots === null && (str_starts_with($path_now, '/admin') || str_contains($path_now, '/admin/'))) {
    $meta_robots = 'noindex, nofollow';
}
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
    <?php if ($og_type === 'article'): ?>
        <?php if ($article_published_time): ?>
            <meta property="article:published_time" content="<?= htmlspecialchars($article_published_time) ?>">
        <?php endif; ?>
        <?php if ($article_modified_time): ?>
            <meta property="article:modified_time" content="<?= htmlspecialchars($article_modified_time) ?>">
        <?php endif; ?>
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.png')) ?>" type="image/png" sizes="64x64">
    <link rel="icon" href="<?= htmlspecialchars(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars(url('assets/img/apple-touch-icon.png')) ?>">
    <?php if ($preload_image): ?>
        <link rel="preload" as="image" href="<?= htmlspecialchars($preload_image) ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/fonts/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(url('assets/css/main.css')) ?>">
    <?php
    // JSON-LD
    if ($schema_type === 'home' || $schema_type === 'local') {
        sia_print_json_ld(sia_schema_local_business());
    }
    if ($schema_type === 'service' && is_array($schema_service)) {
        sia_print_json_ld(sia_schema_local_business());
        sia_print_json_ld(sia_schema_service($schema_service));
    }
    if ($schema_type === 'faq') {
        global $faq_categories;
        sia_print_json_ld(sia_schema_faq($faq_categories));
    }
    if ($schema_type === 'article' && is_array($schema_article)) {
        sia_print_json_ld(sia_schema_article($schema_article));
    }
    if (is_array($schema_breadcrumbs) && $schema_breadcrumbs) {
        sia_print_json_ld(sia_schema_breadcrumbs($schema_breadcrumbs));
    }
    ?>
    <?php if ($ga4_measurement_id !== ''): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga4_measurement_id) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= htmlspecialchars($ga4_measurement_id) ?>');
        </script>
    <?php endif; ?>
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

<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
$config = webstar_config();
$pageTitle = $pageTitle ?? $config['business_name'];
$pageDescription = $pageDescription ?? 'Websites with Google Business Profile, Analytics, and Search Console setup for Metro Detroit small businesses.';
$canonicalPath = $canonicalPath ?? '';
$skipCanonical = !empty($skipCanonical);
$robotsMeta = $robotsMeta ?? '';
$canonicalUrl = $canonicalPath !== '' ? webstar_absolute_url($canonicalPath) : webstar_absolute_url('');
$ogImagePath = $ogImagePath ?? 'assets/images/og-default.jpg';
$ogImageUrl = webstar_absolute_url($ogImagePath);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo webstar_h($config['gtm_container_id'] ?? ''); ?>');</script>
    <!-- End Google Tag Manager -->
    <?php
    $ga4Id = trim((string) ($config['ga4_measurement_id'] ?? ''));
    if ($ga4Id !== '') :
        $ga4JsUrl = 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode($ga4Id);
        ?>
    <!-- Google tag (gtag.js) -->
    <script async src="<?php echo webstar_h($ga4JsUrl); ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?php echo webstar_h($ga4Id); ?>');
    </script>
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="HFGBTaMM5j9LrSA3VpHdyX-6mFDYKF40Zx3Iy4VMFcw">
    <title><?php echo webstar_h($pageTitle); ?></title>
    <meta name="description" content="<?php echo webstar_h($pageDescription); ?>">
    <?php if ($robotsMeta !== '') : ?>
    <meta name="robots" content="<?php echo webstar_h($robotsMeta); ?>">
    <?php endif; ?>
    <?php if (!$skipCanonical) : ?>
    <link rel="canonical" href="<?php echo webstar_h($canonicalUrl); ?>">
    <?php endif; ?>
    <link rel="icon" href="<?php echo webstar_h(webstar_url('assets/images/favicon.png')); ?>" type="image/png">
    <meta property="og:title" content="<?php echo webstar_h($pageTitle); ?>">
    <meta property="og:description" content="<?php echo webstar_h($pageDescription); ?>">
    <meta property="og:type" content="website">
    <?php if (!$skipCanonical) : ?>
    <meta property="og:url" content="<?php echo webstar_h($canonicalUrl); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?php echo webstar_h($config['business_name']); ?>">
    <meta property="og:image" content="<?php echo webstar_h($ogImageUrl); ?>">
    <meta name="theme-color" content="#0a2f2c">
    <link rel="preload" href="<?php echo webstar_h(webstar_url('assets/fonts/galano/GalanoClassicAltRegular.otf')); ?>" as="font" type="font/otf" crossorigin>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo webstar_h($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo webstar_h($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo webstar_h($ogImageUrl); ?>">
    <link rel="stylesheet" href="<?php echo webstar_h(webstar_url('library/styles.css')); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/styles.css'); ?>">
    <?php
    $jsonLdOut = webstar_json_ld_script($jsonLd ?? null);
    if ($jsonLdOut !== '') :
        ?>
    <script type="application/ld+json"><?php echo $jsonLdOut; ?></script>
    <?php endif; ?>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo webstar_h($config['gtm_container_id'] ?? ''); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php include __DIR__ . '/header.php'; ?>
<main>


<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/library/helpers.php';
$config = webstar_config();
$pageTitle = $pageTitle ?? 'Webstar Weddings';
$pageDescription = $pageDescription ?? 'Custom wedding websites and digital invitations by Webstar.';
$canonicalPath = $canonicalPath ?? 'weddings/';
$weddingsPage = $weddingsPage ?? 'home';
$showSeal = !empty($showSeal);
$canonicalUrl = webstar_absolute_url($canonicalPath);
$ogImageUrl = webstar_absolute_url('assets/images/star-icon-gold.png');
$starUrl = webstar_url('assets/images/star-icon-gold.png');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo webstar_h($pageTitle); ?></title>
    <meta name="description" content="<?php echo webstar_h($pageDescription); ?>">
    <link rel="canonical" href="<?php echo webstar_h($canonicalUrl); ?>">
    <link rel="icon" href="<?php echo webstar_h(webstar_url('assets/images/favicon.png')); ?>" type="image/png">
    <meta property="og:title" content="<?php echo webstar_h($pageTitle); ?>">
    <meta property="og:description" content="<?php echo webstar_h($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo webstar_h($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo webstar_h($ogImageUrl); ?>">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo webstar_h($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo webstar_h($pageDescription); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo webstar_h(webstar_url('weddings/assets/weddings.css')); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/../assets/weddings.css'); ?>">
</head>
<body class="ww-body<?php echo $showSeal ? ' ww-body--sealed' : ''; ?><?php echo $weddingsPage === 'home' ? ' ww-body--home' : ''; ?><?php echo $weddingsPage === 'examples' ? ' ww-body--examples' : ''; ?>" data-ww-page="<?php echo webstar_h($weddingsPage); ?>">
<header class="ww-masthead" id="ww-masthead">
    <a class="ww-masthead__brand" href="<?php echo webstar_h(webstar_weddings_url()); ?>">
        <img class="ww-masthead__star" src="<?php echo webstar_h($starUrl); ?>" alt="" width="56" height="56">
        <span>Webstar Weddings</span>
    </a>
    <?php if ($weddingsPage !== 'home'): ?>
    <a class="ww-masthead__home" href="<?php echo webstar_h(webstar_weddings_url()); ?>">Back to home</a>
    <?php endif; ?>
</header>
<main class="ww-main" id="ww-main">

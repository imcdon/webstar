<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
$config = webstar_config();
$pageTitle = $pageTitle ?? $config['business_name'];
$pageDescription = $pageDescription ?? 'Web design and marketing packages for Metro Detroit small businesses.';
$canonicalPath = $canonicalPath ?? '';
$skipCanonical = !empty($skipCanonical);
$robotsMeta = $robotsMeta ?? '';
$canonicalUrl = $canonicalPath !== '' ? webstar_absolute_url($canonicalPath) : webstar_absolute_url('');
$ogImagePath = $ogImagePath ?? 'assets/images/IMG_4687.JPG';
$ogImageUrl = webstar_absolute_url($ogImagePath);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <meta property="og:image" content="<?php echo webstar_h($ogImageUrl); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo webstar_h($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo webstar_h($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo webstar_h($ogImageUrl); ?>">
    <link rel="stylesheet" href="<?php echo webstar_h(webstar_url('library/styles.css')); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/styles.css'); ?>">
    <?php if (!empty($jsonLd)) : ?>
    <script type="application/ld+json"><?php echo $jsonLd; ?></script>
    <?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<main>


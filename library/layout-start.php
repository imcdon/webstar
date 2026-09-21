<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
$config = webstar_config();
$pageTitle = $pageTitle ?? $config['business_name'];
$pageDescription = $pageDescription ?? 'Web design and marketing packages for Metro Detroit small businesses.';
$canonicalPath = $canonicalPath ?? '';
$canonicalUrl = $canonicalPath !== '' ? webstar_absolute_url($canonicalPath) : webstar_absolute_url('');
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
    <link rel="stylesheet" href="<?php echo webstar_h(webstar_url('library/styles.css')); ?>">
    <?php if (!empty($jsonLd)) : ?>
    <script type="application/ld+json"><?php echo $jsonLd; ?></script>
    <?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<main>


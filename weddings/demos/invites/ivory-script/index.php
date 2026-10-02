<?php
declare(strict_types=1);
require dirname(__DIR__, 4) . '/library/helpers.php';
$back = webstar_weddings_url('examples');
$home = webstar_weddings_url();
$css = webstar_url('weddings/assets/weddings.css');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camille &amp; Owen — Digital Invite Demo | Webstar Weddings</title>
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="<?php echo webstar_h($css); ?>">
</head>
<body class="ww-invite ww-invite--ivory">
    <p class="ww-demo__bar">Demo invite · <a href="<?php echo webstar_h($back); ?>">Back to examples</a> · <a href="<?php echo webstar_h($home); ?>">Back to home</a></p>
    <div class="ww-invite__card">
        <p class="ww-invite__eyebrow">You’re invited</p>
        <h1 class="ww-invite__names">Camille <span>&amp;</span> Owen</h1>
        <p class="ww-invite__rule"></p>
        <p class="ww-invite__detail">As we begin our forever</p>
        <p class="ww-invite__date">October 10, 2027 · four o’clock in the afternoon</p>
        <p class="ww-invite__place">Harbor Light Chapel</p>
        <a class="ww-invite__link" href="#">Kindly reply</a>
    </div>
</body>
</html>

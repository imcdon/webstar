<?php
declare(strict_types=1);
require dirname(__DIR__, 3) . '/library/helpers.php';
$back = webstar_weddings_url('examples');
$home = webstar_weddings_url();
$css = webstar_url('weddings/assets/weddings.css');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elena &amp; Sam — Demo Wedding Site | Webstar Weddings</title>
    <meta name="description" content="Demo coastal-weekend wedding website by Webstar Weddings. Not a real client wedding.">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="<?php echo webstar_h($css); ?>">
</head>
<body class="ww-demo ww-demo--coast">
    <p class="ww-demo__bar">Demo site · <a href="<?php echo webstar_h($back); ?>">Back to examples</a> · <a href="<?php echo webstar_h($home); ?>">Back to home</a> · Not a real wedding</p>
    <header class="ww-demo__hero">
        <h1 class="ww-demo__names">Elena <span class="ww-demo__amp">&amp;</span> Sam</h1>
        <p class="ww-demo__meta">August 21, 2027 · Harbor Light Inn</p>
    </header>
    <section class="ww-demo__section">
        <h2>Itinerary</h2>
        <p>Beach ceremony · Dinner on the lawn · Late-night dessert. Built for a relaxed coastal weekend.</p>
    </section>
    <section class="ww-demo__section">
        <h2>Stay</h2>
        <p>Room blocks and nearby inns listed here so guests can book without a dozen group texts.</p>
    </section>
    <section class="ww-demo__section">
        <h2>FAQ</h2>
        <p>Dress code, parking, kids policy—short answers guests actually read on their phones.</p>
    </section>
</body>
</html>

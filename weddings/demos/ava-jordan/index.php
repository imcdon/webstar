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
    <title>Ava &amp; Jordan — Demo Wedding Site | Webstar Weddings</title>
    <meta name="description" content="Demo garden-evening wedding website by Webstar Weddings. Not a real client wedding.">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="<?php echo webstar_h($css); ?>">
</head>
<body class="ww-demo ww-demo--garden">
    <p class="ww-demo__bar">Demo site · <a href="<?php echo webstar_h($back); ?>">Back to examples</a> · <a href="<?php echo webstar_h($home); ?>">Back to home</a> · Not a real wedding</p>
    <header class="ww-demo__hero">
        <h1 class="ww-demo__names">Ava <span class="ww-demo__amp">&amp;</span> Jordan</h1>
        <p class="ww-demo__meta">September 12, 2027 · Meadowbrook Gardens</p>
    </header>
    <section class="ww-demo__section">
        <h2>Our story</h2>
        <p>A sample narrative block for couples who want a short, warm welcome before guests dive into details.</p>
    </section>
    <section class="ww-demo__section">
        <h2>Schedule</h2>
        <p>Ceremony at 4:00 · Cocktail hour · Dinner &amp; dancing under the lights. Replace with your real timeline.</p>
    </section>
    <section class="ww-demo__section">
        <h2>Travel</h2>
        <p>Suggested lodging and parking notes live here—clear enough for out-of-town guests on their phones.</p>
    </section>
</body>
</html>

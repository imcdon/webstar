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
    <title>Maya &amp; Cole — Demo Wedding Site | Webstar Weddings</title>
    <meta name="description" content="Demo city-loft wedding website by Webstar Weddings. Not a real client wedding.">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" href="<?php echo webstar_h($css); ?>">
</head>
<body class="ww-demo ww-demo--city">
    <p class="ww-demo__bar">Demo site · <a href="<?php echo webstar_h($back); ?>">Back to examples</a> · <a href="<?php echo webstar_h($home); ?>">Back to home</a> · Not a real wedding</p>
    <header class="ww-demo__hero">
        <h1 class="ww-demo__names">Maya <span class="ww-demo__amp">&amp;</span> Cole</h1>
        <p class="ww-demo__meta">June 6, 2027 · The Foundry Loft</p>
    </header>
    <section class="ww-demo__section">
        <h2>Weekend</h2>
        <p>Welcome drinks Friday · Ceremony &amp; reception Saturday · Optional farewell brunch Sunday.</p>
    </section>
    <section class="ww-demo__section">
        <h2>Registry</h2>
        <p>Placeholder links for registries or a “your presence is enough” note—styled cleanly for city guests.</p>
    </section>
    <section class="ww-demo__section">
        <h2>RSVP</h2>
        <p>A clear call-to-action area for form or email RSVPs. Wire to the tool you already use.</p>
    </section>
</body>
</html>

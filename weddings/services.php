<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/library/helpers.php';
$pageTitle = 'Wedding Website Services — Webstar Weddings';
$pageDescription = 'Wedding website packages: digital invitations, RSVP-ready pages, galleries, and timelines. Built by Webstar for couples.';
$canonicalPath = 'weddings/services/';
$weddingsPage = 'services';
$showSeal = false;
include __DIR__ . '/includes/layout-start.php';
?>
<article class="ww-page">
    <h1 class="ww-page__title">Services</h1>

    <section class="ww-section">
        <h2>Who it’s for</h2>
        <p>Engaged couples planning a wedding—near or far—who want a polished online invite, schedule, travel notes, and a simple way for guests to respond.</p>
    </section>

    <section class="ww-section">
        <h2>What’s included</h2>
        <ul>
            <li>Custom design in warm, timeless styling tailored to your vibe</li>
            <li>Digital invitation feel with clear save-the-date messaging</li>
            <li>RSVP-ready contact or form paths (wired to tools you choose)</li>
            <li>Event details, timeline, travel / lodging, and registry links</li>
            <li>Photo gallery-ready layouts for engagement or venue shots</li>
            <li>Mobile-first pages that load quickly for guests on the go</li>
        </ul>
    </section>

    <section class="ww-section">
        <h2>How it works</h2>
        <p>Share your date, venues, colors, and any photos you love. We craft the structure and design, you review, then we launch.</p>
    </section>

    <div class="ww-page__cta">
        <div class="ww-home__actions" style="justify-content:flex-start;">
            <a class="ww-btn ww-btn--gold" href="<?php echo webstar_h(webstar_phone_href()); ?>">Call <?php echo webstar_h(webstar_phone_display()); ?></a>
            <a class="ww-btn ww-btn--ghost" href="mailto:weddings@webstarbusinessservices.com">Email weddings@</a>
            <a class="ww-btn ww-btn--ghost" href="<?php echo webstar_h(webstar_weddings_url('examples')); ?>">View examples</a>
        </div>
    </div>
</article>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/library/helpers.php';
$pageTitle = 'Webstar Weddings — Custom Wedding Websites & Digital Invitations';
$pageDescription = 'Elegant wedding websites and digital invitations for couples. Warm, personal, RSVP-ready sites by Webstar.';
$canonicalPath = 'weddings/';
$weddingsPage = 'home';
$showSeal = true;
$starUrl = webstar_url('assets/images/star-icon-gold.png');
include __DIR__ . '/includes/layout-start.php';
?>
<div class="ww-seal-gate" id="ww-seal-gate" role="dialog" aria-modal="true" aria-label="Open Webstar Weddings">
    <div class="ww-particles" aria-hidden="true"></div>
    <button type="button" class="ww-seal" id="ww-seal" aria-label="Tap the gold star to enter">
        <span class="ww-seal__ring" aria-hidden="true"></span>
        <img class="ww-seal__star" src="<?php echo webstar_h($starUrl); ?>" alt="" width="120" height="120">
    </button>
    <p class="ww-seal__hint">Tap to open</p>
    <button type="button" class="ww-seal-skip" id="ww-seal-skip">Skip</button>
</div>

<section class="ww-home" id="ww-home" aria-label="Webstar Weddings home">
    <h1 class="visually-hidden">Webstar Weddings</h1>
    <div class="ww-home__panels">
        <a class="ww-panel" href="<?php echo webstar_h(webstar_weddings_url('services')); ?>">
            <span class="ww-panel__label">View services</span>
        </a>
        <a class="ww-panel" href="<?php echo webstar_h(webstar_weddings_url('examples')); ?>">
            <span class="ww-panel__label">View examples</span>
        </a>
    </div>
</section>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

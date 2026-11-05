<?php
declare(strict_types=1);
require dirname(__DIR__) . '/library/portal-auth.php';
require dirname(__DIR__) . '/library/lead-map.php';

portal_require_admin();

$payload = lead_map_load_businesses();
$corridors = lead_map_enabled_corridors();
$staleCount = count($payload['stale']);
$leadCount = count($payload['leads']);
$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS;
$mapData = [
    'corridors' => $corridors,
    'leads' => $payload['leads'],
    'error' => $payload['error'],
];

$currentPage = 'portal';
$pageTitle = 'Lead map | Webstar';
$pageDescription = 'Internal outreach map of corridor businesses.';
$canonicalPath = 'portal/leads-map.php';
$skipCanonical = true;
$robotsMeta = 'noindex, nofollow';
$skipMobileCta = true;
$extraHead = <<<'HTML'
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin="">
HTML;

$jsUrl = webstar_h(webstar_url('portal/leads-map.js')) . '?v=' . (int) @filemtime(__DIR__ . '/leads-map.js');
$extraScripts = <<<HTML
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin=""></script>
<script src="{$jsUrl}" defer></script>
HTML;

include dirname(__DIR__) . '/library/layout-start.php';
?>
<section class="home-section home-section--night lead-map-page">
    <div class="home-section__inner lead-map-page__inner">
        <div class="lead-map-toolbar">
            <div>
                <h1 class="home-section__heading">Lead map</h1>
                <p class="lead-map-toolbar__meta">
                    <?php echo (int) $leadCount; ?> businesses on Dixie Hwy
                    <?php if ($staleCount > 0) : ?>
                        · <?php echo (int) $staleCount; ?> with stale coordinates (hidden)
                    <?php endif; ?>
                    · OSM © OpenStreetMap contributors
                </p>
            </div>
            <div class="lead-map-toolbar__actions">
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('portal/projects.php')); ?>">Projects</a>
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('portal/logout.php')); ?>">Log out</a>
            </div>
        </div>

        <?php if ($payload['error']) : ?>
            <p class="home-section__textbox" role="alert"><?php echo webstar_h($payload['error']); ?></p>
        <?php endif; ?>

        <div class="lead-map-shell">
            <aside class="lead-map-sidebar" aria-label="Filters and list">
                <fieldset class="lead-map-filters">
                    <legend>Website need</legend>
                    <label><input type="checkbox" name="need" value="high" checked> High (no site / F / D)</label>
                    <label><input type="checkbox" name="need" value="medium" checked> Medium (C)</label>
                    <label><input type="checkbox" name="need" value="unknown"> Unknown (blocked)</label>
                    <label><input type="checkbox" name="need" value="low"> Low (B / A)</label>
                </fieldset>
                <p class="lead-map-legend" aria-hidden="true">
                    <span class="lead-map-swatch lead-map-swatch--none"></span> none
                    <span class="lead-map-swatch lead-map-swatch--F"></span> F
                    <span class="lead-map-swatch lead-map-swatch--D"></span> D
                    <span class="lead-map-swatch lead-map-swatch--C"></span> C
                    <span class="lead-map-swatch lead-map-swatch--q"></span> ?
                    <span class="lead-map-swatch lead-map-swatch--B"></span> B
                    <span class="lead-map-swatch lead-map-swatch--A"></span> A
                </p>
                <p class="lead-map-sidebar__count" id="lead-map-count"></p>
                <ol class="lead-map-list" id="lead-map-list"></ol>
            </aside>
            <div id="lead-map" class="lead-map" role="application" aria-label="Corridor map"></div>
        </div>
    </div>
</section>
<script type="application/json" id="lead-map-data"><?php echo json_encode($mapData, $jsonFlags); ?></script>
<?php include dirname(__DIR__) . '/library/layout-end.php'; ?>

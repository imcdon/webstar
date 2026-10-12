<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live fishwithjarett.com).
 */
if (!function_exists('fwj_staging_should_gate') || !fwj_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'More interesting photos and videos will improve the feel of the website. We can also add a cost/trip breakdown section where people can see the rates and just kind of how a guided trip with you works. It doesn’t have to be detailed — just a couple points.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
    return;
}

// Standalone public-repo fallback (same UI as clients/shared/).
require __DIR__ . '/webstar-staging-banner-fallback.php';

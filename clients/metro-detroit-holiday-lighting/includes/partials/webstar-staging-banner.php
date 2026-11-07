<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live metrodetroitholidaylighting.com).
 */
if (!function_exists('mdhl_staging_should_gate') || !mdhl_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'Drop finished project photos into assets/img/gallery/ and push — the Gallery page updates automatically. Add real testimonials in includes/config.php when Steven has quotes ready.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

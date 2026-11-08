<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live metrodetroitholidaylighting.com).
 */
if (!function_exists('mdhl_staging_should_gate') || !mdhl_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'Spots where there are black squares we will need new photos, maybe photos of the lights being installed and the process. Then updated photos with the big camera that are composed a little better and more aesthetic. We\'ll convene and talk about the logo and throw some ideas around and see what fits.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

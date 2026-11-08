<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live metrodetroitholidaylighting.com).
 */
if (!function_exists('mdhl_staging_should_gate') || !mdhl_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'Spots with black squares need new photos — ideally lights being installed and the process, then updated shots with the big camera that are composed a little better and more aesthetic. Gallery photos can also be dropped into assets/img/gallery/ and pushed. Add real testimonials in includes/config.php when Steven has quotes ready. We\'ll convene and talk about the logo and throw some ideas around and see what fits.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

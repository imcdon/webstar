<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live superioriceadventures.com).
 */
if (!function_exists('sia_staging_should_gate') || !sia_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'More pictures is better, more content the better.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
    return;
}

require __DIR__ . '/webstar-staging-banner-fallback.php';

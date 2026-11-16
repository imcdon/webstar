<?php

declare(strict_types=1);

/**
 * Staging preview banner for Webstar-hosted client sites.
 */
if (!function_exists('glhs_staging_should_gate') || !glhs_staging_should_gate()) {
    return;
}

$webstar_staging_notes = 'Design draft v1. Confirm phone number, supply chair/before-after photos for gallery frames, and decide live domain (replace GlossGenius subdomain). Booking CTA already points at GlossGenius.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

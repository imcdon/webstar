<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live superioriceadventures.com).
 */
if (!function_exists('sia_staging_should_gate') || !sia_staging_should_gate()) {
    return;
}
?>
<div class="webstar-staging-banner" role="status">
    Preview — hosted by Webstar Business Services
</div>

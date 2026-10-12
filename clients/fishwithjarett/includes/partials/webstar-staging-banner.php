<?php
/**
 * Staging preview banner for Webstar-hosted client sites.
 * Shown only while the staging gate is active (not on live fishwithjarett.com).
 */
if (!function_exists('fwj_staging_should_gate') || !fwj_staging_should_gate()) {
    return;
}
?>
<div class="webstar-staging-banner" role="status">
    Preview — hosted by Webstar Business Services
</div>

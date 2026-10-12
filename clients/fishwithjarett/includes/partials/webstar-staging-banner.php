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
    <div class="webstar-staging-banner__bar">
        <span class="webstar-staging-banner__label">Preview — hosted by Webstar Business Services</span>
        <details class="webstar-staging-banner__notes">
            <summary>Notes</summary>
            <div class="webstar-staging-banner__notes-body">
                <p>More interesting photos and videos will improve the feel of the website. We can also add a cost/trip breakdown section where people can see the rates and just kind of how a guided trip with you works. It doesn’t have to be detailed — just a couple points.</p>
            </div>
        </details>
    </div>
</div>

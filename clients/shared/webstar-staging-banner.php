<?php
/**
 * Shared Webstar staging preview header (Path A).
 * Callers must set $webstar_staging_notes (plain text) before require.
 * Only include this file after the client staging gate has already passed.
 */
declare(strict_types=1);

$webstar_staging_notes = trim((string) ($webstar_staging_notes ?? ''));
?>
<style>
.webstar-staging-banner {
  background: #1a1a1a;
  color: #f5f5f5;
  font-size: 0.75rem;
  letter-spacing: 0.04em;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
  position: relative;
  z-index: 10000;
}
.webstar-staging-banner__bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.35rem 0.85rem;
  padding: 0.4rem 0.75rem;
}
.webstar-staging-banner__label { text-align: center; }
.webstar-staging-banner__notes { position: relative; }
.webstar-staging-banner__notes summary {
  cursor: pointer;
  list-style: none;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #d4c4a8;
  user-select: none;
}
.webstar-staging-banner__notes summary::-webkit-details-marker { display: none; }
.webstar-staging-banner__notes summary::after { content: " ▾"; font-size: 0.7em; }
.webstar-staging-banner__notes[open] summary::after { content: " ▴"; }
.webstar-staging-banner__notes-body {
  position: absolute;
  top: calc(100% + 0.35rem);
  right: 0;
  z-index: 60;
  width: min(22rem, calc(100vw - 1.5rem));
  padding: 0.75rem 0.85rem;
  background: #111;
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 0.25rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  text-align: left;
  letter-spacing: 0.01em;
  text-transform: none;
  font-size: 0.82rem;
  line-height: 1.45;
  color: #e8e8e8;
}
.webstar-staging-banner__notes-body p { margin: 0; }
@media (max-width: 520px) {
  .webstar-staging-banner__notes-body {
    right: auto;
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>
<div class="webstar-staging-banner" role="status">
    <div class="webstar-staging-banner__bar">
        <span class="webstar-staging-banner__label">Preview — hosted by Webstar Business Services</span>
        <details class="webstar-staging-banner__notes">
            <summary>Notes</summary>
            <div class="webstar-staging-banner__notes-body">
                <?php if ($webstar_staging_notes !== '') : ?>
                    <p><?php echo htmlspecialchars($webstar_staging_notes, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
                <?php else : ?>
                    <p>No preview notes yet.</p>
                <?php endif; ?>
            </div>
        </details>
    </div>
</div>

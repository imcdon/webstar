<?php

declare(strict_types=1);

$webstar_staging_notes = $webstar_staging_notes ?? 'Square sandbox shop — catalog from Ecwid scrape / seed. Demo checkout works without Square credentials.';

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

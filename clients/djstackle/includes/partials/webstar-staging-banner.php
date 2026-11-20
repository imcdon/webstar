<?php

declare(strict_types=1);

$webstar_staging_notes = $webstar_staging_notes ?? "Path A preview — production Square catalog. Checkout is real; use a small test order and refund in Square Dashboard.";

$shared = dirname(__DIR__, 3) . '/shared/webstar-staging-banner.php';
if (is_file($shared)) {
    require $shared;
}

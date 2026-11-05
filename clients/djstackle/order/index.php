<?php

declare(strict_types=1);

/**
 * Order status / fulfillment lives in Square. Redirect old /order/ links.
 */
require dirname(__DIR__) . '/includes/config.php';

header('Location: ' . url('shipping-returns/'), true, 301);
exit;

<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $shopCart->remove((string) ($_POST['variation_id'] ?? ''));
}
header('Location: ' . url('cart/'));
exit;

<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'toggle') {
        $added = $shopWishlist->toggle((string) ($_POST['product_id'] ?? ''));
        if ($shopAccount) {
            $shopWishlist->mergeToAccount($shopAccount['id']);
        }
        echo json_encode(['ok' => true, 'added' => $added]);
        exit;
    }
    echo json_encode(['ok' => false, 'error' => 'Unknown action']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

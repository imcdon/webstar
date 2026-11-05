<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

header('Content-Type: application/json; charset=UTF-8');

try {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'add') {
        $shopCart->add((string) ($_POST['variation_id'] ?? ''), (int) ($_POST['qty'] ?? 1));
        $detailed = $shopCart->detailed();
        $html = '<ul style="list-style:none;padding:0;margin:0">';
        foreach ($detailed['lines'] as $line) {
            $variant = ($line['variation_name'] ?? '') !== '' && !in_array($line['variation_name'], ['Default', 'Regular'], true)
                ? ' <span class="muted">(' . Helpers::e($line['variation_name']) . ')</span>'
                : '';
            $html .= '<li style="margin:.5rem 0"><strong>' . Helpers::e($line['product_name']) . '</strong>' . $variant . ' × '
                . (int) $line['qty'] . ' — ' . Helpers::e(Helpers::money($line['line_total_cents'])) . '</li>';
        }
        $html .= '</ul><p><strong>Total:</strong> ' . Helpers::e(Helpers::money($detailed['total_cents'])) . '</p>';
        echo json_encode(['ok' => true, 'count' => $shopCart->count(), 'drawer_html' => $html]);
        exit;
    }
    echo json_encode(['ok' => false, 'error' => 'Unknown action']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

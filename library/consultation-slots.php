<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';
require __DIR__ . '/calendar.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    exit;
}

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');

echo json_encode(webstar_calendar_public_slots(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

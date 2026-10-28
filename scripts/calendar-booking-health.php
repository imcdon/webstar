<?php

declare(strict_types=1);

/**
 * Booking diagnostics (no secrets). Web: ?key= must match booking_health_key in calendar-config.php.
 * CLI: C:\xampp\php\php.exe scripts/calendar-booking-health.php
 */

require dirname(__DIR__) . '/library/helpers.php';
require dirname(__DIR__) . '/library/calendar.php';

$configPath = dirname(__DIR__) . '/library/calendar-config.php';
$raw = webstar_calendar_config();
$healthKey = is_array($raw) ? trim((string) ($raw['booking_health_key'] ?? '')) : '';

if (PHP_SAPI !== 'cli') {
    $provided = (string) ($_GET['key'] ?? '');
    if ($healthKey === '' || !hash_equals($healthKey, $provided)) {
        http_response_code(404);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
}

$settings = webstar_calendar_settings();
$token = webstar_calendar_access_token(true);
$slots = webstar_calendar_public_slots();

$payload = [
    'config_exists' => is_file($configPath),
    'config_readable' => is_readable($configPath),
    'config_realpath' => is_file($configPath) ? (realpath($configPath) ?: $configPath) : null,
    'settings_ok' => $settings !== null,
    'has_refresh' => $settings !== null && $settings['refresh_token'] !== '',
    'curl' => function_exists('curl_init'),
    'token_ok' => $token !== null,
    'slots_ok' => !empty($slots['ok']),
    'reason' => $slots['reason'] ?? null,
    'day_count' => count($slots['days'] ?? []),
    'php_sapi' => PHP_SAPI,
    'host' => $_SERVER['HTTP_HOST'] ?? 'cli',
];

if (PHP_SAPI === 'cli') {
    foreach ($payload as $k => $v) {
        echo $k . '=' . (is_bool($v) ? ($v ? '1' : '0') : (string) $v) . PHP_EOL;
    }
    exit;
}

echo json_encode($payload, JSON_UNESCAPED_SLASHES);

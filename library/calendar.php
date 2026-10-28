<?php

declare(strict_types=1);

/**
 * Google Calendar free/busy and consultation events.
 * Requires library/calendar-config.php (see calendar-config.example.php).
 */

function webstar_calendar_config_path(): string
{
    return __DIR__ . '/calendar-config.php';
}

/** @return array<string, mixed>|null */
function webstar_calendar_config(): ?array
{
    static $config = false;
    if ($config !== false) {
        return $config;
    }
    $path = webstar_calendar_config_path();
    if (!is_file($path)) {
        $config = null;
        return null;
    }
    $loaded = require $path;
    $config = is_array($loaded) ? $loaded : null;

    return $config;
}

/**
 * Normalized booking settings. Null when the OAuth client is missing or still a placeholder.
 *
 * @return array<string, mixed>|null
 */
function webstar_calendar_settings(): ?array
{
    $raw = webstar_calendar_config();
    if ($raw === null) {
        return null;
    }

    $clientId = trim((string) ($raw['client_id'] ?? ''));
    $clientSecret = trim((string) ($raw['client_secret'] ?? ''));
    $placeholders = [
        '',
        'your-client-id.apps.googleusercontent.com',
        'your-client-secret',
    ];
    if (in_array($clientId, $placeholders, true) || in_array($clientSecret, $placeholders, true)) {
        return null;
    }

    $timezone = trim((string) ($raw['timezone'] ?? 'America/Detroit'));
    try {
        $tz = new DateTimeZone($timezone);
    } catch (Exception) {
        return null;
    }

    $slotMinutes = (int) ($raw['slot_minutes'] ?? 30);
    $leadHours = (int) ($raw['lead_hours'] ?? 4);
    $windowDays = (int) ($raw['window_days'] ?? 21);
    if ($slotMinutes < 15 || $slotMinutes > 120 || $leadHours < 0 || $leadHours > 72 || $windowDays < 1 || $windowDays > 60) {
        return null;
    }

    $dayStart = trim((string) ($raw['day_start'] ?? '09:00'));
    $dayEnd = trim((string) ($raw['day_end'] ?? '17:00'));
    if (!preg_match('/^\d{2}:\d{2}$/', $dayStart) || !preg_match('/^\d{2}:\d{2}$/', $dayEnd) || $dayStart >= $dayEnd) {
        return null;
    }

    $weekdays = $raw['weekdays'] ?? [1, 2, 3, 4, 5];
    if (!is_array($weekdays) || $weekdays === []) {
        return null;
    }
    $days = [];
    foreach ($weekdays as $day) {
        $day = (int) $day;
        if ($day >= 1 && $day <= 7) {
            $days[] = $day;
        }
    }
    if ($days === []) {
        return null;
    }

    $calendarId = trim((string) ($raw['calendar_id'] ?? 'primary'));
    if ($calendarId === '') {
        $calendarId = 'primary';
    }

    return [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => trim((string) ($raw['refresh_token'] ?? '')),
        'calendar_id' => $calendarId,
        'timezone' => $tz->getName(),
        'slot_minutes' => $slotMinutes,
        'lead_hours' => $leadHours,
        'window_days' => $windowDays,
        'weekdays' => array_values(array_unique($days)),
        'day_start' => $dayStart,
        'day_end' => $dayEnd,
    ];
}

function webstar_calendar_ready(): bool
{
    $settings = webstar_calendar_settings();

    return $settings !== null && $settings['refresh_token'] !== '';
}

/**
 * @param array<int, string> $headers
 * @return array{status: int, body: ?array, raw: string}
 */
function webstar_calendar_http(string $method, string $url, ?string $body, array $headers): array
{
    if (!function_exists('curl_init')) {
        return ['status' => 0, 'body' => null, 'raw' => ''];
    }
    $ch = curl_init($url);
    if ($ch === false) {
        return ['status' => 0, 'body' => null, 'raw' => ''];
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $raw = is_string($raw) ? $raw : '';
    $decoded = json_decode($raw, true);

    return [
        'status' => $status,
        'body' => is_array($decoded) ? $decoded : null,
        'raw' => $raw,
    ];
}

function webstar_calendar_access_token(bool $forceRefresh = false): ?string
{
    $settings = webstar_calendar_settings();
    if ($settings === null || $settings['refresh_token'] === '') {
        return null;
    }

    $cachePath = __DIR__ . '/.calendar-access.json';
    if (!$forceRefresh && is_file($cachePath)) {
        $cached = json_decode((string) file_get_contents($cachePath), true);
        if (is_array($cached) && isset($cached['access_token'], $cached['expires_at']) && (int) $cached['expires_at'] > time()) {
            return (string) $cached['access_token'];
        }
    }

    $response = webstar_calendar_http(
        'POST',
        'https://oauth2.googleapis.com/token',
        http_build_query([
            'client_id' => $settings['client_id'],
            'client_secret' => $settings['client_secret'],
            'refresh_token' => $settings['refresh_token'],
            'grant_type' => 'refresh_token',
        ]),
        ['Content-Type: application/x-www-form-urlencoded']
    );
    $token = trim((string) ($response['body']['access_token'] ?? ''));
    $expiresIn = (int) ($response['body']['expires_in'] ?? 0);
    if ($response['status'] !== 200 || $token === '' || $expiresIn < 60) {
        return null;
    }

    file_put_contents($cachePath, json_encode([
        'access_token' => $token,
        'expires_at' => time() + $expiresIn - 60,
    ]));

    return $token;
}

/**
 * Busy intervals only. Null when the API cannot be read.
 *
 * @return list<array{0: DateTimeImmutable, 1: DateTimeImmutable}>|null
 */
function webstar_calendar_busy(DateTimeImmutable $from, DateTimeImmutable $to): ?array
{
    $settings = webstar_calendar_settings();
    $token = webstar_calendar_access_token();
    if ($settings === null || $token === null) {
        return null;
    }

    $payload = json_encode([
        'timeMin' => $from->format('c'),
        'timeMax' => $to->format('c'),
        'items' => [['id' => $settings['calendar_id']]],
    ]);
    $response = webstar_calendar_http(
        'POST',
        'https://www.googleapis.com/calendar/v3/freeBusy',
        $payload === false ? '{}' : $payload,
        [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]
    );
    if ($response['status'] === 401) {
        $token = webstar_calendar_access_token(true);
        if ($token === null) {
            return null;
        }
        $response = webstar_calendar_http(
            'POST',
            'https://www.googleapis.com/calendar/v3/freeBusy',
            $payload === false ? '{}' : $payload,
            [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]
        );
    }
    if ($response['status'] !== 200 || !is_array($response['body'])) {
        return null;
    }

    $calendar = $response['body']['calendars'][$settings['calendar_id']] ?? null;
    if (!is_array($calendar) || !empty($calendar['errors'])) {
        return null;
    }

    $busy = [];
    foreach ($calendar['busy'] ?? [] as $block) {
        if (!is_array($block)) {
            continue;
        }
        try {
            $start = new DateTimeImmutable((string) ($block['start'] ?? ''));
            $end = new DateTimeImmutable((string) ($block['end'] ?? ''));
        } catch (Exception) {
            continue;
        }
        $busy[] = [$start, $end];
    }

    return $busy;
}

/**
 * @param list<array{0: DateTimeImmutable, 1: DateTimeImmutable}> $busy
 */
function webstar_calendar_overlaps(DateTimeImmutable $start, DateTimeImmutable $end, array $busy): bool
{
    foreach ($busy as [$blockStart, $blockEnd]) {
        if ($start < $blockEnd && $end > $blockStart) {
            return true;
        }
    }

    return false;
}

/**
 * Every slot in business hours from the lead time through the booking window.
 *
 * @return list<DateTimeImmutable>
 */
function webstar_calendar_slot_starts(array $settings): array
{
    $tz = new DateTimeZone((string) $settings['timezone']);
    $now = new DateTimeImmutable('now', $tz);
    $lead = $now->modify('+' . (int) $settings['lead_hours'] . ' hours');
    $windowEnd = $now->setTime(0, 0)->modify('+' . (int) $settings['window_days'] . ' days')->setTime(23, 59, 59);
    [$startHour, $startMinute] = array_map('intval', explode(':', (string) $settings['day_start']));
    [$endHour, $endMinute] = array_map('intval', explode(':', (string) $settings['day_end']));
    $minutes = (int) $settings['slot_minutes'];

    $slots = [];
    $day = $now->setTime(0, 0);
    $lastDay = $windowEnd->setTime(0, 0);
    while ($day <= $lastDay) {
        if (in_array((int) $day->format('N'), $settings['weekdays'], true)) {
            $slot = $day->setTime($startHour, $startMinute);
            $dayEnd = $day->setTime($endHour, $endMinute);
            while (true) {
                $slotEnd = $slot->modify('+' . $minutes . ' minutes');
                if ($slotEnd > $dayEnd) {
                    break;
                }
                if ($slot >= $lead && $slot <= $windowEnd) {
                    $slots[] = $slot;
                }
                $slot = $slotEnd;
            }
        }
        $day = $day->modify('+1 day');
    }

    return $slots;
}

/**
 * Public slot list. Times and open/taken only — never another event's details.
 *
 * @return array{ok: bool, reason?: string, timezone?: string, days?: list<array<string, mixed>>}
 */
function webstar_calendar_public_slots(): array
{
    $settings = webstar_calendar_settings();
    if ($settings === null || $settings['refresh_token'] === '') {
        return ['ok' => false, 'reason' => 'config'];
    }

    $starts = webstar_calendar_slot_starts($settings);
    if ($starts === []) {
        return [
            'ok' => true,
            'timezone' => (string) $settings['timezone'],
            'days' => [],
        ];
    }

    $minutes = (int) $settings['slot_minutes'];
    $last = $starts[array_key_last($starts)]->modify('+' . $minutes . ' minutes');
    $busy = webstar_calendar_busy($starts[0], $last);
    if ($busy === null) {
        return ['ok' => false, 'reason' => 'api'];
    }

    $days = [];
    foreach ($starts as $start) {
        $end = $start->modify('+' . $minutes . ' minutes');
        $date = $start->format('Y-m-d');
        if (!isset($days[$date])) {
            $days[$date] = [
                'date' => $date,
                'label' => $start->format('l, M j'),
                'slots' => [],
            ];
        }
        $days[$date]['slots'][] = [
            'start' => $start->format('c'),
            'label' => $start->format('g:i A'),
            'status' => webstar_calendar_overlaps($start, $end, $busy) ? 'taken' : 'open',
        ];
    }

    return [
        'ok' => true,
        'timezone' => (string) $settings['timezone'],
        'days' => array_values($days),
    ];
}

/**
 * Re-check one posted start time against the live grid.
 *
 * @return array{ok: true, status: string, start: DateTimeImmutable}|array{ok: false, reason: string}
 */
function webstar_calendar_resolve_slot(string $iso): array
{
    $settings = webstar_calendar_settings();
    if ($settings === null || $settings['refresh_token'] === '') {
        return ['ok' => false, 'reason' => 'config'];
    }

    try {
        $posted = new DateTimeImmutable($iso);
    } catch (Exception) {
        return ['ok' => false, 'reason' => 'validation'];
    }

    $starts = webstar_calendar_slot_starts($settings);
    $match = null;
    foreach ($starts as $start) {
        if ($start->getTimestamp() === $posted->getTimestamp()) {
            $match = $start;
            break;
        }
    }
    if ($match === null) {
        return ['ok' => false, 'reason' => 'validation'];
    }

    $end = $match->modify('+' . (int) $settings['slot_minutes'] . ' minutes');
    $busy = webstar_calendar_busy($match, $end);
    if ($busy === null) {
        return ['ok' => false, 'reason' => 'api'];
    }

    return [
        'ok' => true,
        'status' => webstar_calendar_overlaps($match, $end, $busy) ? 'taken' : 'open',
        'start' => $match,
    ];
}

/**
 * @return array{ok: true, meet: string}|array{ok: false, reason: string}
 */
function webstar_calendar_create_event(DateTimeImmutable $start, string $name, string $email, string $description): array
{
    $settings = webstar_calendar_settings();
    $token = webstar_calendar_access_token();
    if ($settings === null || $token === null) {
        return ['ok' => false, 'reason' => 'config'];
    }

    $end = $start->modify('+' . (int) $settings['slot_minutes'] . ' minutes');
    $summary = 'Webstar consultation — ' . $name;
    if (strlen($summary) > 200) {
        $summary = substr($summary, 0, 197) . '...';
    }

    $event = [
        'summary' => $summary,
        'description' => $description,
        'start' => [
            'dateTime' => $start->format('c'),
            'timeZone' => (string) $settings['timezone'],
        ],
        'end' => [
            'dateTime' => $end->format('c'),
            'timeZone' => (string) $settings['timezone'],
        ],
        'attendees' => [
            ['email' => $email],
        ],
        'conferenceData' => [
            'createRequest' => [
                'requestId' => bin2hex(random_bytes(16)),
                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
            ],
        ],
    ];

    $url = 'https://www.googleapis.com/calendar/v3/calendars/'
        . rawurlencode((string) $settings['calendar_id'])
        . '/events?sendUpdates=all&conferenceDataVersion=1';
    $response = webstar_calendar_insert($url, $event, $token);
    if ($response['status'] === 401) {
        $token = webstar_calendar_access_token(true);
        if ($token === null) {
            return ['ok' => false, 'reason' => 'config'];
        }
        $response = webstar_calendar_insert($url, $event, $token);
    }

    $message = strtolower((string) ($response['body']['error']['message'] ?? ''));
    if ($response['status'] === 400 && str_contains($message, 'conference')) {
        unset($event['conferenceData']);
        $url = 'https://www.googleapis.com/calendar/v3/calendars/'
            . rawurlencode((string) $settings['calendar_id'])
            . '/events?sendUpdates=all';
        $response = webstar_calendar_insert($url, $event, $token);
    }

    if ($response['status'] !== 200 || !is_array($response['body'])) {
        return ['ok' => false, 'reason' => 'send'];
    }

    $meet = trim((string) ($response['body']['hangoutLink'] ?? ''));

    return ['ok' => true, 'meet' => $meet];
}

/**
 * @param array<string, mixed> $event
 * @return array{status: int, body: ?array, raw: string}
 */
function webstar_calendar_insert(string $url, array $event, string $token): array
{
    $payload = json_encode($event);

    return webstar_calendar_http(
        'POST',
        $url,
        $payload === false ? '{}' : $payload,
        [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]
    );
}

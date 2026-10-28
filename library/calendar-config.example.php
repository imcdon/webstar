<?php

declare(strict_types=1);

/**
 * Copy to library/calendar-config.php (gitignored) and fill in.
 *
 * One-time Google setup:
 * 1. In Google Cloud, create a project and enable the Google Calendar API.
 * 2. OAuth consent screen: External, then Publish so the refresh token is not limited to 7 days.
 *    Scopes: calendar.events and calendar.freebusy (sensitive; the owner can still approve).
 * 3. Create an OAuth client ID of type Web application.
 *    Authorized redirect URI: http://127.0.0.1:8765/
 * 4. Paste the client id and secret below, then from the project root run:
 *    C:\xampp\php\php.exe scripts/google-calendar-auth.php
 *    Sign in as ian@webstarbusinessservices.com. The script saves the refresh token here.
 */
return [
    'client_id' => 'your-client-id.apps.googleusercontent.com',
    'client_secret' => 'your-client-secret',
    'refresh_token' => '',
    'calendar_id' => 'primary',
    'timezone' => 'America/Detroit',
    'slot_minutes' => 30,
    'lead_hours' => 4,
    'window_days' => 21,
    // 1 = Monday … 7 = Sunday
    'weekdays' => [1, 2, 3, 4, 5],
    'day_start' => '09:00',
    'day_end' => '17:00',
    // Optional: set to run scripts/calendar-booking-health.php?key=YOUR_KEY on the server (no secrets returned).
    'booking_health_key' => '',
];

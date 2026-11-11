<?php

declare(strict_types=1);

/**
 * Fallback staging users when portal-clients.php is not reachable.
 * Keep hashes in sync with library/portal-clients.php.
 * Do not document plaintext passwords here — see clients/SECURITY-CHECKLIST.md.
 */
return [
    'admin' => [
        'display_name' => 'Webstar Admin',
        'role' => 'admin',
        'password_hash' => '$2y$10$29Unokw6wL0urucGTzO68uwVjRv4b/QEz1dw3f0V8RCjreHIcXehO',
        'projects' => [],
    ],
    'djstackle' => [
        'display_name' => "DJ's Tackle",
        'role' => 'client',
        'password_hash' => '$2y$10$cUoIztGQgNta0lRVS8wwtOO.3OlhGfYt5UP4a7Z7MXGxkn/vYnhxy',
        'projects' => [
            [
                'slug' => 'djstackle',
                'title' => "DJ's Tackle website",
                'status' => 'In progress',
                'local_path' => 'clients/djstackle/',
                'staging_url' => 'https://webstarbusinessservices.com/clients/djstackle/',
            ],
        ],
    ],
];

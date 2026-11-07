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
    'mdhl' => [
        'display_name' => 'Metro Detroit Holiday Lighting',
        'role' => 'client',
        'password_hash' => '$2y$10$f8T4hMDOEbKBl5WHNKQbFOmVj7NJcGjF7inQ2uVn3djiSXPywDsRS',
        'projects' => [
            [
                'slug' => 'metro-detroit-holiday-lighting',
                'title' => 'Metro Detroit Holiday Lighting website',
                'status' => 'In progress',
                'local_path' => 'clients/metro-detroit-holiday-lighting/',
                'staging_url' => 'https://webstarbusinessservices.com/clients/metro-detroit-holiday-lighting/',
            ],
        ],
    ],
];

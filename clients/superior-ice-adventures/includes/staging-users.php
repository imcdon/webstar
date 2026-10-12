<?php

declare(strict_types=1);

/**
 * Fallback staging users when portal-clients.php is not reachable
 * (e.g. this folder deployed alone). Keep hashes in sync with
 * library/portal-clients.php on the Webstar site.
 * Do not document plaintext passwords here — see clients/SECURITY-CHECKLIST.md.
 */
return [
    'admin' => [
        'display_name' => 'Webstar Admin',
        'role' => 'admin',
        'password_hash' => '$2y$10$29Unokw6wL0urucGTzO68uwVjRv4b/QEz1dw3f0V8RCjreHIcXehO',
        'projects' => [],
    ],
    '1234' => [
        'display_name' => 'Superior Ice Adventures',
        'role' => 'client',
        'password_hash' => '$2y$10$amcsbH14b21g4bbBTAB.peRt6YQNP3Ft2o15gWKiq8SftVy077Zte',
        'projects' => [
            [
                'slug' => 'superior-ice-adventures',
                'title' => 'Superior Ice Adventures website',
                'status' => 'In progress',
                'local_path' => 'clients/superior-ice-adventures/',
                'staging_url' => 'https://webstarbusinessservices.com/clients/superior-ice-adventures/',
            ],
        ],
    ],
];

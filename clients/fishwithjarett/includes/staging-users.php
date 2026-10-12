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
    '123' => [
        'display_name' => 'Fish with Jarett',
        'role' => 'client',
        'password_hash' => '$2y$10$7UdOawJV4tMogfeq.yHZNOu6oqWzklwo/hX2WM3DmVLgkZkfdE.pK',
        'projects' => [
            [
                'slug' => 'fishwithjarett',
                'title' => 'Fish with Jarett website',
                'status' => 'In progress',
                'local_path' => 'clients/fishwithjarett/',
                'staging_url' => 'https://webstarbusinessservices.com/clients/fishwithjarett/',
            ],
        ],
    ],
];

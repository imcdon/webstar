<?php

declare(strict_types=1);

/**
 * Example client registry. Copy to portal-clients.php and set password hashes with:
 *   php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
 *
 * Accounts with role "admin" see every client project. Client accounts see only their own.
 *
 * @return array<string, array<string, mixed>>
 */
return [
    'webstar' => [
        'display_name' => 'Webstar Admin',
        'role' => 'admin',
        'password_hash' => '$2y$10$replaceWithRealHash...................................',
        'projects' => [],
    ],
    'superior' => [
        'display_name' => 'Superior Ice Adventures',
        'role' => 'client',
        'password_hash' => '$2y$10$replaceWithRealHash...................................',
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

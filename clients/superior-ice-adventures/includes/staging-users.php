<?php

declare(strict_types=1);

/**
 * Fallback staging users when portal-clients.php is not reachable
 * (e.g. this folder deployed alone). Keep hashes in sync with
 * library/portal-clients.php on the Webstar site.
 *
 * webstar / webstarAdmin2026
 * superior / preview123
 */
return [
    'webstar' => [
        'display_name' => 'Webstar Admin',
        'role' => 'admin',
        'password_hash' => '$2y$10$lfqZAJiiaVRuJjHYOG..wOkAS.hyNSdc1OYmVT2uX8iHj0xhpzUMG',
        'projects' => [],
    ],
    'superior' => [
        'display_name' => 'Superior Ice Adventures',
        'role' => 'client',
        'password_hash' => '$2y$10$X4fwtQr2e7rfJgSqDUFo5eCrCXAXcSf5E3ZGFx7yvjDcYlwQkdaSi',
        'projects' => [
            [
                'slug' => 'superior-ice-adventures',
                'title' => 'Superior Ice Adventures website',
                'status' => 'In progress',
                'local_path' => 'clients/superior-ice-adventures/',
                'staging_url' => 'https://superior-ice-adventures.webstarbusinessservices.com',
            ],
        ],
    ],
];

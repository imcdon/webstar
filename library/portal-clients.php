<?php

declare(strict_types=1);

/**
 * Client Portal registry.
 *
 * Universal admin: username "webstar" / password "webstarAdmin2026" (change after deploy).
 * Demo client: username "superior" / password "preview123" (change after handoff).
 *
 * @return array<string, array{
 *   display_name: string,
 *   password_hash: string,
 *   role?: 'admin'|'client',
 *   projects: list<array{
 *     slug: string,
 *     title: string,
 *     status: string,
 *     local_path: string,
 *     staging_url: string
 *   }>
 * }>
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
                'staging_url' => 'https://webstarbusinessservices.com/clients/superior-ice-adventures/',
            ],
        ],
    ],
];

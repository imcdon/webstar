<?php

declare(strict_types=1);

/**
 * Client Portal registry.
 * Users are identified by username keys; store only password_hash values here.
 * Keep plaintext passwords out of git (password manager). See clients/SECURITY-CHECKLIST.md.
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
    'gypsie' => [
        'display_name' => 'Gypsie Locs Hair Studio',
        'role' => 'client',
        'password_hash' => '$2y$10$Mnhc6BZMUNq5GJIxNFL1jeXLkmn9zlm4WeWY9.bSc1JbUev85dkz.',
        'projects' => [
            [
                'slug' => 'gypsie-locs-hair-studio',
                'title' => 'Gypsie Locs Hair Studio website',
                'status' => 'Design draft',
                'local_path' => 'clients/gypsie-locs-hair-studio/',
                'staging_url' => 'https://webstarbusinessservices.com/clients/gypsie-locs-hair-studio/',
            ],
        ],
    ],
];

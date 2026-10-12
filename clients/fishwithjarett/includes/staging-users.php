<?php

declare(strict_types=1);

/**
 * Fallback staging users when portal-clients.php is not reachable.
 * Keep hashes in sync with library/portal-clients.php.
 * Do not document plaintext passwords here — see clients/SECURITY-CHECKLIST.md.
 */
return [
    'webstar' => [
        'display_name' => 'Webstar Admin',
        'role' => 'admin',
        'password_hash' => '$2y$10$lfqZAJiiaVRuJjHYOG..wOkAS.hyNSdc1OYmVT2uX8iHj0xhpzUMG',
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

<?php

declare(strict_types=1);

/**
 * Portfolio entries for the public Examples page.
 * Add launching clients back when their production domain is live.
 * Thumbnails: 1280×800 in assets/images/examples/.
 */
return [
    [
        'title' => 'Chrome City Guide Service',
        'url' => 'https://www.chromecityguideservicellc.com',
        'status' => 'live',
        'thumbnail' => 'assets/images/examples/chrome-city-home.jpg',
        'package_slug' => 'business-website',
    ],
    [
        'title' => 'Earth Odyssey',
        'url' => 'https://earth-odyssey.com',
        'status' => 'testing',
        'thumbnail' => 'assets/images/examples/earth-odyssey-home.webp',
        'description' => 'Stream gauges and fishing reports for the water, a library of public-domain outdoor books, and photo galleries from the field. Still in testing.',
    ],
];

<?php
/*
 * config.php - Site-wide settings.
 */
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';

$site_name = 'Fish with Jarett';
$site_domain = 'fishwithjarett.com';
$site_url = 'https://fishwithjarett.com';
$site_tagline = 'Salmon and Trout Fishing on Lake Michigan Tributaries';
$site_description = 'Fish Wisconsin, Indiana, and Michigan waters with licensed guide Jarett Carlson. Salmon, steelhead, and trout on Lake Michigan tributaries. Licensed and insured.';
$company_name = 'Fish with Jarett';
$guide_name = 'Jarett Carlson';
$location_eyebrow = 'Wisconsin · Indiana · Michigan';

$phone = '(630) 396-0975';
$phone_href = 'tel:+16303960975';
$email = '';
$email_href = '';

$social = [
    'instagram' => 'https://www.instagram.com/jaretttt/',
    'facebook'  => 'https://www.facebook.com/jarett.carlson',
];

$trust_line = 'Licensed and insured';

$hero_image = 'assets/img/King-Salmon-Hero-Image.jpg';
$hero_image_alt = 'King salmon on Lake Michigan tributary waters';

$location_cards = [
    [
        'title' => 'Wisconsin',
        'rivers' => 'Lake Michigan tributaries',
        'tone' => 'wi',
    ],
    [
        'title' => 'Indiana',
        'rivers' => 'Trail Creek, Salt Creek, Little Calumet River',
        'tone' => 'in',
    ],
    [
        'title' => 'Michigan',
        'rivers' => 'St. Joseph River, Lake Michigan tributaries',
        'tone' => 'mi',
    ],
];

$species_cards = [
    [
        'title' => 'King Salmon',
        'season' => 'August–October',
        'tone' => 'salmon',
        'image' => 'assets/img/Jarett-Calson-King-Salmon.jpg',
        'alt' => 'Jarett Carlson with a king salmon',
    ],
    [
        'title' => 'Steelhead',
        'season' => 'October–July',
        'tone' => 'steelhead',
        'image' => 'assets/img/Jarett-Carlson-Steelhead.jpg',
        'alt' => 'Jarett Carlson with a steelhead',
    ],
    [
        'title' => 'Trout',
        'season' => 'Year-Around',
        'tone' => 'trout',
        'image' => 'assets/img/Jarett-Carlson-Brown-Trout.jpg',
        'alt' => 'Jarett Carlson with a brown trout',
    ],
];

$client_photos = [
    [
        'image' => 'assets/img/Client-Photo-001.jpg',
        'alt' => 'Client catch with Fish with Jarett',
        'layout' => 'standard',
    ],
    [
        'image' => 'assets/img/Client-Photo-002.jpg',
        'alt' => 'Client catch with Fish with Jarett',
        'layout' => 'standard',
    ],
    [
        'image' => 'assets/img/Client-Photo-003.jpg',
        'alt' => 'Client catch with Fish with Jarett',
        'layout' => 'standard',
    ],
    [
        'image' => 'assets/img/Client-Photo-Split-004.jpg',
        'alt' => 'Client catch with Fish with Jarett',
        'layout' => 'split',
    ],
    [
        'image' => 'assets/img/Client-Photo-Split-005.jpg',
        'alt' => 'Client catch with Fish with Jarett',
        'layout' => 'split',
    ],
];

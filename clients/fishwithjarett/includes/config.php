<?php
/*
 * config.php - Site-wide settings.
 */
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';

$site_name = 'Fish with Jarett';
$site_domain = 'fishwithjarett.com';
$site_url = 'https://fishwithjarett.com';
$site_tagline = 'Guided salmon & trout on Lake Michigan tributaries.';
$site_description = 'Fish Lake Michigan tributaries in Wisconsin, Indiana, and Michigan for salmon and trout with Jarett Carlson. Licensed and insured.';
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

$waters = [
    'Trail Creek',
    'Salt Creek',
    'Little Calumet River',
    'St. Joseph River',
    'Lake Michigan tributaries',
];

$species = ['Salmon', 'Trout', 'Steelhead'];
$states = ['Wisconsin', 'Indiana', 'Michigan'];

<?php

declare(strict_types=1);

/*
 * config.php — Site-wide settings for Gypsie Locs Hair Studio.
 */
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';

$site_name = 'Gypsie Locs Hair Studio';
$site_domain = 'gypsielocshairstudio.com';
$site_url = 'https://gypsielocshairstudio.com';
$site_tagline = 'Custom color and extensions in a private Waterford suite.';
$site_description = 'Gypsie Locs Hair Studio in Waterford, MI — custom color, Dream Catchers extensions, and precision cuts in a private Ultra Salon suite. Wella, Redken, and Rusk certified. Book online.';

$company_name = 'Gypsie Locs Hair Studio';
$stylist_name = 'Gypsie';
$years_experience = 20;

/* Confirm with client before go-live — Ultra Salon Suites listing */
$phone = '(248) 455-3970';
$phone_href = 'tel:+12484553970';
$email = '';
$email_href = '';

$booking_url = 'https://gypsielocshairstudio.glossgenius.com/';
$booking_services_url = 'https://gypsielocshairstudio.glossgenius.com/services';

$address = [
    'line1' => '5066 Highland Rd, Suite 12',
    'line2' => 'Ultra Salon Suites',
    'city' => 'Waterford',
    'region' => 'MI',
    'postal_code' => '48327',
    'country' => 'US',
];

$hours = [
    ['day' => 'Monday', 'hours' => '4 PM – 8 PM'],
    ['day' => 'Tuesday', 'hours' => '12 PM – 8 PM'],
    ['day' => 'Wednesday', 'hours' => '11 AM – 5:30 PM'],
    ['day' => 'Thursday', 'hours' => '12 PM – 5:30 PM'],
    ['day' => 'Friday', 'hours' => '11 AM – 5 PM'],
    ['day' => 'Saturday', 'hours' => 'Closed'],
    ['day' => 'Sunday', 'hours' => 'Closed'],
];

$nav_links = [
    'Home' => '',
    'Services' => 'services/',
    'About' => 'about/',
    'Book' => 'book/',
];

$credentials = [
    'Dream Catchers extension specialist',
    'Wella certified colorist',
    'Redken certified colorist',
    'Rusk certified colorist',
    (string) $years_experience . ' years behind the chair',
];

$specialties = [
    [
        'title' => 'Custom color',
        'body' => 'All-over color, retouches, bleach and tone, and correction planned around your hair’s history — not a one-formula chair.',
    ],
    [
        'title' => 'Dimensional work',
        'body' => 'Highlights, balayage, and gloss finishes that read natural in Michigan light, with pricing that starts from a real consult.',
    ],
    [
        'title' => 'Dream Catchers extensions',
        'body' => 'Beaded row, tape-in, and I-tip installs and lifts — length and density matched to your cut and color.',
    ],
    [
        'title' => 'Cuts & finish',
        'body' => 'Women’s and men’s cuts, blowouts, and upstyles that hold the color story you booked for.',
    ],
];

$service_groups = [
    [
        'title' => 'Color',
        'items' => [
            ['name' => 'Color retouch (new growth)', 'price' => 'From $55', 'note' => '60 min'],
            ['name' => 'All-over color', 'price' => 'From $96', 'note' => '90 min'],
            ['name' => 'Bleach and tone', 'price' => 'From $150', 'note' => 'Consultation recommended'],
            ['name' => 'Partial / full highlight', 'price' => 'From $70', 'note' => 'Quoted by density'],
            ['name' => 'Balayage', 'price' => 'From $150', 'note' => ''],
            ['name' => 'Gloss and shine', 'price' => 'From $25', 'note' => ''],
        ],
    ],
    [
        'title' => 'Extensions',
        'items' => [
            ['name' => 'Consultation', 'price' => 'Free', 'note' => '30 min'],
            ['name' => 'Beaded row extension lift', 'price' => 'From $150', 'note' => ''],
            ['name' => 'Tape-in / I-tip application', 'price' => 'Varies', 'note' => 'Quoted in chair'],
            ['name' => 'Tape-in removal', 'price' => 'From $80', 'note' => ''],
        ],
    ],
    [
        'title' => 'Cuts & treatments',
        'items' => [
            ['name' => 'Women’s cut', 'price' => 'From $45', 'note' => '45 min'],
            ['name' => 'Men’s clipper / scissor', 'price' => '$27–$35', 'note' => ''],
            ['name' => 'Blow out', 'price' => 'From $25', 'note' => ''],
            ['name' => 'Olaplex / protein treatment', 'price' => 'From $27', 'note' => ''],
            ['name' => 'Rusk anti-curl smoothing', 'price' => 'Varies', 'note' => 'Consultation required'],
        ],
    ],
];

$faq_items = [
    [
        'q' => 'Where is the studio?',
        'a' => 'Gypsie Locs is a private suite inside Ultra Salon Suites at 5066 Highland Rd, Suite 12, Waterford, MI — not a busy multi-chair floor.',
    ],
    [
        'q' => 'How do I book?',
        'a' => 'Book online through GlossGenius, or start with a free consultation if you need color correction or extensions.',
    ],
    [
        'q' => 'What is the cancellation policy?',
        'a' => 'A 50% fee applies for no-shows or cancellations without 48 hours’ notice.',
    ],
    [
        'q' => 'Do you specialize in locs?',
        'a' => 'The studio name honors Gypsie’s roots. Day-to-day work centers on custom color, Dream Catchers extensions, and precision cuts. Ask at consult if you need loc maintenance — the menu is built around color and extension craft.',
    ],
];

$policy_note = '50% cancellation fee for no-shows or cancellations without 48 hours’ notice.';

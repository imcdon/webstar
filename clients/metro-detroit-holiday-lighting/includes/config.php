<?php
/*
 * config.php - Site-wide settings for Metro Detroit Holiday Lighting.
 */
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';
require_once __DIR__ . '/cities.php';
require_once __DIR__ . '/gallery.php';
require_once __DIR__ . '/landing-pages.php';

$site_name = 'Metro Detroit Holiday Lighting';
$site_domain = 'metrodetroitholidaylighting.com';
$site_url = 'https://metrodetroitholidaylighting.com';
$site_tagline = 'Holiday magic, simplified.';
$site_description = 'Professional holiday lighting design, install, and take-down for Metro Detroit homes, businesses, and HOAs. Owner-operated by Steven with 20 years of experience. Call (248) 525-4715.';
$company_name = 'Metro Detroit Holiday Lighting';
$owner_name = 'Steven';
$years_experience = 20;

$phone = '(248) 525-4715';
$phone_href = 'tel:+12485254715';
$email = 'metrodetroitholidaylighting@gmail.com';
$email_href = 'mailto:metrodetroitholidaylighting@gmail.com';
$contact_form_to = $email;
$mail_from = '';

$trust_line = 'Professionally designed. Fully insured. Owner-operated.';

$service_nav = mdhl_service_nav();

$nav_links = [
    'Home' => '',
    'Services' => 'homeowners/',
    'Gallery' => 'gallery/',
    'Service Areas' => 'service-areas/',
    'Contact' => 'contact/',
];

$audiences = [
    [
        'title' => 'Homeowners',
        'body' => 'Custom holiday lighting that fits your home — rooftops, trees, walks, and accents — without you climbing a ladder or fighting tangled cords.',
        'image' => 'assets/img/gallery/residential-08-clark-white-tree.jpg',
        'image_alt' => 'Residential home with white holiday tree lighting',
        'url' => 'homeowners/',
    ],
    [
        'title' => 'Businesses',
        'body' => 'Storefronts, offices, and commercial buildings that look polished for the season and stay maintained from install through take-down.',
        'image' => 'assets/img/audiences/businesses.jpg',
        'image_alt' => 'Commercial storefront with professional holiday lighting',
        'url' => 'businesses/',
    ],
    [
        'title' => 'HOAs & property managers',
        'body' => 'Community entrances, clubhouses, and shared spaces with one clear plan, one crew, and one call for mid-season fixes.',
        'image' => 'assets/img/gallery/commercial-07-how-entrance.jpg',
        'image_alt' => 'Community entrance with professional holiday lighting',
        'url' => 'hoa-property-managers/',
    ],
];

foreach ($audiences as &$audience) {
    $audiencePath = dirname(__DIR__) . '/' . $audience['image'];
    $audience['has_image'] = is_file($audiencePath);
}
unset($audience);

$process_steps = [
    [
        'title' => 'Custom design',
        'body' => 'Send photos of the house, storefront, or community. Steven maps a lighting plan that fits the architecture and your budget.',
    ],
    [
        'title' => 'Install',
        'body' => 'Professional installation before the season hits. Clean lines, secure clips, and displays built to last through Michigan weather.',
    ],
    [
        'title' => 'Mid-season care',
        'body' => 'If a strand fails or a storm knocks something loose, call. Mid-season fixes keep the display looking right.',
    ],
    [
        'title' => 'Take-down',
        'body' => 'After the holidays, lights come down carefully so nothing is left behind on the property.',
    ],
    [
        'title' => 'Off-season storage',
        'body' => 'Labeled and stored for next year so you start the season ready instead of starting over.',
    ],
];

$faq_items = [
    [
        'q' => 'Do you serve my city in Metro Detroit?',
        'a' => 'Yes — we work across Oakland, Wayne, and Macomb County. Browse Service Areas or call (248) 525-4715 to confirm your neighborhood.',
    ],
    [
        'q' => 'How do I get a quote?',
        'a' => 'Text or email a few clear photos of the house, storefront, or community entrance to (248) 525-4715 or metrodetroitholidaylighting@gmail.com. Include your city and what you want lit. Steven will follow up with a plan.',
    ],
    [
        'q' => 'Are you insured?',
        'a' => 'Yes. Metro Detroit Holiday Lighting is fully insured for residential and commercial holiday lighting installs.',
    ],
    [
        'q' => 'Who does the work?',
        'a' => 'Steven owns and operates the company and does the work himself. You deal with the same person from design through take-down.',
    ],
    [
        'q' => 'Do you take the lights down and store them?',
        'a' => 'Yes. Install, mid-season care, take-down, and off-season storage are part of the full-season service.',
    ],
    [
        'q' => 'When should I book?',
        'a' => 'Earlier is better. Peak installs fill through fall so displays are ready before Thanksgiving and early December.',
    ],
];

/**
 * Real testimonials only. Leave empty until Steven supplies quotes — the section hides itself.
 *
 * @var list<array{quote:string,name:string,detail?:string}>
 */
$testimonials = [];

$gallery_images = mdhl_gallery_images();

$hero_image_file = dirname(__DIR__) . '/assets/img/hero-soaring-eagle.jpg';
$hero_image = is_file($hero_image_file) ? url('assets/img/hero-soaring-eagle.jpg') : '';
$hero_image_alt = 'Holiday lighting at Soaring Eagle by Metro Detroit Holiday Lighting';
if ($hero_image === '' && $gallery_images !== []) {
    $hero_image = $gallery_images[0]['url'];
    $hero_image_alt = $gallery_images[0]['alt'];
}

// About section photo — Steven hanging a wreath.
$owner_image_file = dirname(__DIR__) . '/assets/img/gallery/hanging-the-wreath.jpg';
$owner_image = is_file($owner_image_file) ? 'assets/img/gallery/hanging-the-wreath.jpg' : '';
$owner_image_alt = 'Steven hanging a holiday wreath';

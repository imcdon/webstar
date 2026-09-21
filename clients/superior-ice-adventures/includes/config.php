<?php
/*
 * config.php - Site-wide settings used across every page.
 */
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';

$site_name = 'Superior Ice Adventures';
$site_domain = 'superioriceadventures.com';
$site_url = 'https://superioriceadventures.com';
$site_tagline = 'Upper Peninsula and Lake Superior Guided Fishing and Ice Shanty Rentals in Michigan.';
$site_description = 'Upper Peninsula and Lake Superior guided fishing trips for Splake, Brook Trout, Burbot, Lake Trout, and Steelhead. Ice Shanty Rentals available with gear and fishing location.';
$company_name = 'Superior Ice Adventures';
$location_eyebrow = "Michigan's Upper Peninsula | Lake Superior";

$phone = '(248) 342-7414';
$phone_href = 'tel:+12483427414';
$email = 'derekwilder14@gmail.com';
$email_href = 'mailto:derekwilder14@gmail.com';
$contact_form_to = 'derekwilder14@gmail.com';
$mail_from = 'noreply@superioriceadventures.com';

$address = [
    'line1'      => '',
    'city'       => 'Marquette',
    'region'     => 'MI',
    'postal_code' => '49855',
    'country'    => 'US',
    'note'       => 'Service area only',
];

$hours = [
    ['day' => 'Every day', 'hours' => 'Open all day'],
];

$social = [
    'instagram' => '',
    'facebook'  => '',
];

$nav_services = [
    [
        'label' => 'Inland Splake Fishing',
        'href'  => '/inland-splake-fishing/',
        'slug'  => 'inland-splake-fishing',
    ],
    [
        'label' => 'Lake Superior Burbot',
        'href'  => '/lake-superior-burbot/',
        'slug'  => 'lake-superior-burbot',
    ],
    [
        'label' => 'Lake Superior Lake Trout',
        'href'  => '/lake-superior-lake-trout/',
        'slug'  => 'lake-superior-lake-trout',
    ],
    [
        'label' => 'Ice Shack Rentals',
        'href'  => '/ice-shack-rentals/',
        'slug'  => 'ice-shack-rentals',
    ],
];

$nav_links = [
    'Gallery'  => '/gallery/',
    'Articles' => '/articles/',
    'About'    => '/about/',
    'FAQ'      => '/faq/',
    'Contact'  => '/contact/',
];

/* ---------- Home hero carousel (Section 2) ---------- */
$hero_slides = [
    [
        'eyebrow'  => '',
        'title'    => 'Inland Splake and Brook Trout Fishing',
        'caption'  => 'Inland lake ice fishing for Brook Trout and Splake | 6 Hour Trip | All Equipment Provided',
        'image'    => '/assets/img/hero/inland-splake-fishing.webp',
        'href'     => '/inland-splake-fishing/',
        'image_position' => 'center 78%',
    ],
    [
        'eyebrow'  => 'Munising, MI',
        'title'    => 'Lake Superior Burbot Fishing',
        'caption'  => 'Lake Superior Ice Fishing for Burbot, "Poor Man\'s Lobster" | 6 Hour Trip | All Equipment Provided',
        'image'    => '/assets/img/hero/lake-superior-burbot.webp',
        'href'     => '/lake-superior-burbot/',
    ],
    [
        'eyebrow'  => 'Munising, MI',
        'title'    => 'Lake Superior Lake Trout Fishing',
        'caption'  => 'Lake Superior Ice Fishing for Lake Trout | 6 Hour Trip | All Equipment Provided',
        'image'    => '/assets/img/hero/lake-superior-lake-trout.webp',
        'href'     => '/lake-superior-lake-trout/',
    ],
    [
        'eyebrow'  => 'Munising, MI | Inland Lake Delivery Available',
        'title'    => 'Lake Superior & Inland Lake Ice Shanty Rentals',
        'caption'  => 'Temporary and Permanent Ice Shanty Rentals Available for Rent | Equipment Provided',
        'image'    => '/assets/img/hero/ice-shack-rentals.webp',
        'href'     => '/ice-shack-rentals/',
    ],
];

/* ---------- Home value props (Section 3) ---------- */
$value_props = [
    [
        'icon'  => 'map-pin',
        'title' => 'A Local, Experienced Guide',
        'copy'  => 'Our Guides are experienced in Upper Peninsula Fishing and pride themselves in preparation, safety, and making your adventure the best it can be.',
    ],
    [
        'icon'  => 'fish',
        'title' => 'Species and Seasons',
        'copy'  => 'Ice Fishing in Michigan\'s Upper Peninsula usually starts in late November/early December and finishes up around late March/early April. The following species are available throughout the ice fishing season: Burbot, Lake Trout, Splake, Brook Trout.',
    ],
    [
        'icon'  => 'compass',
        'title' => 'All Experience Levels | All Equipment Provided',
        'copy'  => 'All experience levels. All equipment provided.',
    ],
    [
        'icon'  => 'phone',
        'title' => 'Call to Book',
        'copy'  => 'Call (248) 342-7414 to book your trip.',
    ],
];

/* ---------- Home story section (Section 4) ---------- */
$home_story = [
    'eyebrow'   => 'Our Story',
    'headline' => 'Our Story',
    'paragraphs' => [
        'Winter In Michigan\'s Upper Peninsula is harsh and extreme. Feet of snow falls like a blanket over the pines and rolling hills. The lakes harden with feet of ice. While some may see it as a frozen wasteland, we\'re more concerned with what lies beneath. Fishing and adventure is our passion, we\'d love to introduce you to the wild, the fish we chase, and days and nice on the ice.',
    ],
    'guide_name' => 'Derek Wilder',
    'guide_role' => 'Owner, Guide',
    'images' => [
        '/assets/img/about/story-1.webp',
        '/assets/img/about/story-2.webp',
        '/assets/img/gallery/lake-trout-smile.webp',
    ],
];

/* ---------- Service pages (Section 5) ---------- */
$services = [
    'inland-splake-fishing' => [
        'slug'            => 'inland-splake-fishing',
        'nav_label'       => 'Inland Splake Fishing',
        'meta_line'       => 'Late November/Early December | 6 Hours | Groups of All Sizes | All Equipment Provided',
        'title'           => 'Inland Splake and Brook Trout Fishing',
        'summary'         => 'Deep in the woods lie small lakes with beautiful Splake and Brook Trout. Using a variety of techniques, we can target Brook Trout and Splake in hard-to-reach places.',
        'best_for'        => 'Best for Adults, Kids, Groups',
        'long_description'=> [],
        'highlights'      => [
            'Inland Lake Brook Trout are often larger than river dwelling Brookies.',
            'Splake are a man-made hybrid between a female Lake Trout and a male Brook Trout and rarely reproduce naturally.',
            'Splake often grow larger than Brook Trout.',
            'Splake and Brook Trout chase spoons and minnows throughout the winter months.',
        ],
        'whats_included'  => [
            'All Rods, Reels, Line, and Tackle',
            'Fish filleting',
            'Shanty with heater',
            'An experienced guide',
        ],
        'rates_note'      => '$200 per person',
        'hero_image'      => '/assets/img/services/inland-splake-fishing.webp',
        'thumb_image'     => '/assets/img/services/inland-splake-fishing.webp',
        'hero_position'   => 'center 78%',
        'card_blurb'      => 'Deep in the woods lie small lakes with beautiful Splake and Brook Trout. Using a variety of techniques, we can target Brook Trout and Splake in hard-to-reach places.',
    ],
    'lake-superior-burbot' => [
        'slug'            => 'lake-superior-burbot',
        'nav_label'       => 'Lake Superior Burbot',
        'meta_line'       => 'Late November/Early December | 6 Hours | Groups of All Sizes | All Equipment Provided',
        'title'           => 'Lake Superior Burbot Fishing',
        'summary'         => 'Munising Bay is home to migratory populations of Burbot that traverse the shallows at night. Burbot often go for cut bait on bottom rigs.',
        'best_for'        => 'Best for Adults, Kids, Groups',
        'long_description'=> [],
        'highlights'      => [
            'Burbot are a bottom dwelling fish.',
            'Often referred to as Poor Man\'s Lobster by locals.',
            'Often cooked like lobster and served with butter and lime.',
            'Burbot are migratory in the winter months and spawn in the late winter.',
        ],
        'whats_included'  => [
            'All Rods, Reels, Line, and Tackle',
            'Fish filleting',
            'Shanty with heater',
            'An experienced guide',
        ],
        'rates_note'      => '$200 per person',
        'hero_image'      => '/assets/img/services/lake-superior-burbot.webp',
        'thumb_image'     => '/assets/img/services/lake-superior-burbot.webp',
        'card_blurb'      => 'Munising Bay is home to migratory populations of Burbot that traverse the shallows at night. Burbot often go for cut bait on bottom rigs.',
    ],
    'lake-superior-lake-trout' => [
        'slug'            => 'lake-superior-lake-trout',
        'nav_label'       => 'Lake Superior Lake Trout',
        'meta_line'       => 'Late November/Early December | 6 Hours | Groups of All Sizes | All Equipment Provided',
        'title'           => 'Lake Superior Lake Trout Fishing',
        'summary'         => 'Munising Bay supports healthy populations of Lake Trout. Lake Trout occupy depths as deep as 200 feet and chase spoons and minnows.',
        'best_for'        => 'Best for Adults, Kids, Groups',
        'long_description'=> [],
        'highlights'      => [
            'Lake Trout are among the longest living fish species in Michigan, some individual fish living as long as 60 years.',
            'Lake Trout come in many sizes from 15 - 30 inches.',
            'Lake Trout is actually a char species, not a trout.',
        ],
        'whats_included'  => [
            'All Rods, Reels, Line, and Tackle',
            'Fish filleting',
            'Shanty with heater',
            'An experienced guide',
        ],
        'rates_note'      => '$200 per person',
        'hero_image'      => '/assets/img/services/lake-superior-lake-trout.webp',
        'thumb_image'     => '/assets/img/services/lake-superior-lake-trout.webp',
        'card_blurb'      => 'Munising Bay supports healthy populations of Lake Trout. Lake Trout occupy depths as deep as 200 feet and chase spoons and minnows.',
    ],
    'ice-shack-rentals' => [
        'slug'            => 'ice-shack-rentals',
        'nav_label'       => 'Ice Shack Rentals',
        'meta_line'       => 'Late November/Early December | Several Days Available | Groups of All Sizes | All Equipment Provided',
        'title'           => 'Lake Superior & Inland Lake Ice Shanty Rentals',
        'summary'         => 'Ice Shanties are available for rent on Lake Superior or Delivery to Inland Lakes in the Upper Peninsula.',
        'best_for'        => 'Best for Adults, Kids, Groups',
        'long_description'=> [],
        'highlights'      => [
            'Available for rent on Lake Superior as well as inland lakes in the Upper Peninsula.',
        ],
        'whats_included'  => [
            'Propane',
            'Seats',
            'Heater',
        ],
        'rates_note'      => '$200 per person',
        'hero_image'      => '/assets/img/services/ice-shack-rentals.webp',
        'thumb_image'     => '/assets/img/services/ice-shack-rentals.webp',
        'card_blurb'      => 'Ice Shanties are available for rent on Lake Superior or Delivery to Inland Lakes in the Upper Peninsula.',
    ],
];

/* ---------- About page (Section 6) ---------- */
$about = [
    'hero_eyebrow' => 'About Us',
    'hero_title'   => 'Our Story',
    'hero_intro'   => 'Lake Superior and the UP are where we fish, we would love to take you on an adventure!',
    'story_title'  => 'Our story',
    'story_paragraphs' => [
        'Winter In Michigan\'s Upper Peninsula is harsh and extreme. Feet of snow falls like a blanket over the pines and rolling hills. The lakes harden with feet of ice. While some may see it as a frozen wasteland, we\'re more concerned with what lies beneath. Fishing and adventure is our passion, we\'d love to introduce you to the wild, the fish we chase, and days and nice on the ice.',
    ],
    'guide' => [
        'name'  => 'Derek Wilder',
        'title' => 'Owner, Guide',
        'bio'   => [],
        'image' => '/assets/img/gallery/atlantic-shanty.webp',
    ],
    'images' => [
        '/assets/img/gallery/atlantic-shanty.webp',
        '/assets/img/about/story-1.webp',
        '/assets/img/gallery/lake-trout-smile.webp',
    ],
    'testimonials' => [
        [
            'quote' => 'Derek was patient and encouraging, teaching us the proper techniques for casting and reeling in. After just a few tries, we were actually catching salmon - me and both my kids! It was such an amazing feeling. Beyond fishing, Derek shared fascinating stories about the local history and clearly has a great reputation among other guides and fishermen in the area. He also pointed us toward some affordable places to get our salmon processed, and he was spot on - they did a great job. Oh, and I can\'t forget to mention his loyal companion, a brave dog named Finn, who came along for the adventure.',
            'name'  => '',
            'location' => '',
        ],
    ],
];

/* ---------- FAQ (Section 7) ---------- */
$faq_intro = 'Answers about guided ice fishing trips, what to bring, ice safety, and shanty rentals. Still need help? Call (248) 342-7414.';

$faq_categories = [
    [
        'name'  => 'Booking & Trips',
        'items' => [
            [
                'q' => 'How do I book a trip?',
                'a' => 'Call (248) 342-7414 to check availability and reserve your date. You can also send a message through our contact form.',
            ],
            [
                'q' => 'How long are guided trips?',
                'a' => 'Our guided ice fishing trips are typically 6 hours. Ice shanty rentals can be booked for several days.',
            ],
            [
                'q' => 'How much does a guided trip cost?',
                'a' => 'Guided trips are $200 per person. Call for current shanty rental rates and group options.',
            ],
            [
                'q' => 'What species can we target?',
                'a' => 'Depending on the trip, we fish for Splake, Brook Trout, Burbot, and Lake Trout. Steelhead opportunities may also be available in season.',
            ],
            [
                'q' => 'When is ice fishing season?',
                'a' => 'Ice fishing in Michigan\'s Upper Peninsula usually starts in late November or early December and runs through late March or early April, depending on ice conditions.',
            ],
            [
                'q' => 'Do you take kids and beginners?',
                'a' => 'Yes. Trips are open to adults, kids, and groups of all sizes and experience levels. All equipment is provided.',
            ],
        ],
    ],
    [
        'name'  => 'What to Bring',
        'items' => [
            [
                'q' => 'What gear do I need?',
                'a' => 'We provide rods, reels, line, tackle, a heated shanty, and an experienced guide. Bring warm winter clothing, boots, gloves, and a positive attitude.',
            ],
            [
                'q' => 'What should I wear?',
                'a' => 'Dress in warm layers for Upper Peninsula winter weather. Waterproof insulated boots, a heavy coat, hat, and gloves are strongly recommended.',
            ],
            [
                'q' => 'Do you fillet fish?',
                'a' => 'Yes. Fish filleting is included on guided trips.',
            ],
        ],
    ],
    [
        'name'  => 'Ice Safety',
        'items' => [
            [
                'q' => 'How do you decide if the ice is safe?',
                'a' => 'Your guide monitors ice conditions carefully and will only fish when conditions are appropriate. Safety and preparation come first on every trip.',
            ],
            [
                'q' => 'What if weather or ice conditions change?',
                'a' => 'Call ahead if conditions look questionable. We will help you reschedule when the ice or weather is not right for a safe trip.',
            ],
            [
                'q' => 'Where do you fish?',
                'a' => 'We fish inland lakes for Splake and Brook Trout, and Munising Bay / Lake Superior for Burbot and Lake Trout. Service area is based out of Marquette, MI.',
            ],
        ],
    ],
    [
        'name'  => 'Rentals',
        'items' => [
            [
                'q' => 'Do you rent ice shanties?',
                'a' => 'Yes. Temporary and permanent ice shanty rentals are available on Lake Superior, with delivery available to inland lakes in the Upper Peninsula.',
            ],
            [
                'q' => 'What comes with a shanty rental?',
                'a' => 'Rentals include propane, seats, and a heater. Call for full equipment details and availability.',
            ],
            [
                'q' => 'How do I reserve a shanty?',
                'a' => 'Call (248) 342-7414 to reserve dates and discuss Lake Superior or inland lake delivery options.',
            ],
        ],
    ],
];

/* ---------- Gallery (Section 8) ---------- */
$gallery_intro = 'Pictures of us, the beautiful places we guide, and our clients.';
$gallery_items = [
    ['src' => '/assets/img/gallery/atlantic.webp', 'alt' => 'Angler with Atlantic salmon on the ice'],
    ['src' => '/assets/img/gallery/atlantic-shanty.webp', 'alt' => 'Ice shanty on the lake'],
    ['src' => '/assets/img/gallery/burbot.webp', 'alt' => 'Burbot caught inside an ice shack'],
    ['src' => '/assets/img/gallery/burbot-client.webp', 'alt' => 'Angler with a Lake Superior burbot'],
    ['src' => '/assets/img/gallery/brook-trout.webp', 'alt' => 'Brook trout'],
    ['src' => '/assets/img/gallery/inland-splake-fishing.webp', 'alt' => 'Inland splake fishing'],
    ['src' => '/assets/img/gallery/lake-superior-lake-trout.webp', 'alt' => 'Lake Superior lake trout'],
    ['src' => '/assets/img/gallery/lake-trout-smile.webp', 'alt' => 'Angler with a lake trout'],
    ['src' => '/assets/img/gallery/ice-shack-rentals.webp', 'alt' => 'Ice shack rentals at night'],
    ['src' => '/assets/img/gallery/ice.webp', 'alt' => 'On the ice'],
];

/* ---------- Articles listing ---------- */
$articles_intro = 'Articles from our guides.';

/* ---------- Contact page (Section 9) ---------- */
$contact_intro = 'Please contact us with any questions you may have, we will reach out to you shortly. If you\'d like to book please call.';
$contact_service_options = [
    '' => '— Select a service —',
    'inland-splake-fishing' => 'Inland Splake Fishing',
    'lake-superior-burbot' => 'Lake Superior Burbot',
    'lake-superior-lake-trout' => 'Lake Superior Lake Trout',
    'ice-shack-rentals' => 'Ice Shack Rentals',
    'other' => 'Other / General inquiry',
];

/* ---------- Home final CTA ---------- */
$final_cta = [
    'eyebrow'  => 'Ready to fish?',
    'headline' => 'Go on an Adventure With Us',
    'copy'     => 'Call (248) 342-7414 to book your trip, or send us a message and we will reach out shortly.',
];

/* ---------- Articles fallback when DB empty ---------- */
$latest_articles_fallback = [];

function get_service(string $slug): ?array
{
    global $services;
    return $services[$slug] ?? null;
}

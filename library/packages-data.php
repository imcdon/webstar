<?php

declare(strict_types=1);

/**
 * Three website tiers. Every tier includes Google services (Business Profile,
 * Analytics 4, Search Console, basic call/form click tracking).
 * Monthly options (Care Plans, Website as a Service) live in plans-data.php.
 */
return [
    'simple-website' => [
        'slug' => 'simple-website',
        'nav_label' => 'Simple Website',
        'title' => 'Simple Website',
        'price' => '$350–$500',
        'price_note' => 'One-time project fee',
        'turnaround' => '24 hours',
        'meta_description' => 'Simple Website: responsive 3-page small business website with contact form, Google Business Profile, Analytics, and Search Console setup. One-time $350–$500. Typical turnaround 24 hours after complete intake.',
        'summary' => 'A fast, responsive three-page site plus the Google setup that gets you found — contact form, call and book buttons, and you own everything.',
        'includes' => [
            'Fully functional responsive website (3 pages)',
            'Contact form, call and book-now buttons',
            'Custom design and content structure',
            'Google Business Profile create or claim and full setup',
            'Google Analytics 4 and Search Console setup with call/form click tracking',
            'Owner access to every Google account — you keep the keys',
        ],
        'ideal_for' => 'Solo operators, guides, trades, and small teams ready to go live quickly.',
        'badge' => 'Most popular',
        'tier' => 1,
        'features' => [
            'pages' => '3',
            'contact_form' => true,
            'call_book_buttons' => true,
            'booking_integration' => false,
            'custom_design' => true,
            'google_business_profile' => true,
            'google_analytics' => true,
            'google_search_console' => true,
            'flyers' => '—',
            'business_card' => false,
            'letterhead' => false,
            'email_signature' => false,
            'custom_functionality' => false,
            'third_party_integrations' => '—',
        ],
        'process' => [
            'title' => 'How we work',
            'intro' => 'A clear path from your ideas to a live three-page website that shows up on Google.',
            'phases' => [
                [
                    'name' => 'Planning',
                    'summary' => 'We confirm your three pages, collect logo and photos, and set up the agreement and deposit.',
                    'steps' => [
                        'Lock your three-page sitemap',
                        'Collect logo, photos, and copy notes',
                        'Agreement and deposit to start',
                    ],
                ],
                [
                    'name' => 'Building',
                    'summary' => 'We design and build your site (including contact form and call/book buttons), then share a preview for feedback.',
                    'steps' => [
                        'Custom design for your brand',
                        'Contact form and call/book buttons',
                        'Preview and your feedback',
                    ],
                ],
                [
                    'name' => 'Launch',
                    'summary' => 'We put the site live on your hosting (or ours under a Care Plan), test everything, and hand off so you own your domain and files.',
                    'steps' => [
                        'Go live on agreed hosting',
                        'Test pages, form, and buttons',
                        '30-day post-launch fix window',
                    ],
                ],
                [
                    'name' => 'Google',
                    'summary' => 'We create or claim your Google Business Profile, connect Analytics and Search Console, and walk you through verification so you own the accounts.',
                    'steps' => [
                        'Business Profile setup and verification guidance',
                        'Analytics 4 and Search Console connected to your site',
                        'Plain-English first check-in on what the numbers mean',
                    ],
                ],
            ],
        ],
    ],
    'business-website' => [
        'slug' => 'business-website',
        'nav_label' => 'Business Website',
        'title' => 'Business Website',
        'price' => '$900–$1,800',
        'price_note' => 'One-time project fee',
        'turnaround' => '2–4 days',
        'meta_description' => '5–10 page responsive business website with booking integration, print and brand kit, Google Business Profile, Analytics, and Search Console. One-time $900–$1,800. Typical turnaround 2–4 days.',
        'summary' => 'A full business site with booking, matching print and brand materials, and complete Google setup — built for growing local businesses.',
        'includes' => [
            'Fully functional responsive website (5–10 pages)',
            'Contact form, call and book-now buttons',
            'Calendar view, booking flow, or one third-party integration',
            'Custom design and content structure',
            'Brand kit: 2 flyers, business card, letterhead, email signature',
            'Google Business Profile create or claim and full setup',
            'Google Analytics 4 and Search Console setup with call/form click tracking',
        ],
        'ideal_for' => 'Businesses that need a stronger web presence, booking, and coordinated brand materials.',
        'badge' => 'Most value',
        'tier' => 2,
        'features' => [
            'pages' => '5–10',
            'contact_form' => true,
            'call_book_buttons' => true,
            'booking_integration' => true,
            'custom_design' => true,
            'google_business_profile' => true,
            'google_analytics' => true,
            'google_search_console' => true,
            'flyers' => '2',
            'business_card' => true,
            'letterhead' => true,
            'email_signature' => true,
            'custom_functionality' => false,
            'third_party_integrations' => '1',
        ],
        'process' => [
            'title' => 'How we work',
            'intro' => 'Website, brand materials, and Google move together — then we help you get found after launch.',
            'phases' => [
                [
                    'name' => 'Planning',
                    'summary' => 'We lock your page list, booking tool, and brand pieces (flyers, card, letterhead, signature). Agreement and deposit start the work.',
                    'steps' => [
                        'Confirm pages and booking tool',
                        'Brief for print and email materials',
                        'Agreement and deposit',
                    ],
                ],
                [
                    'name' => 'Building',
                    'summary' => 'We build your 5–10 page site and matching print/email materials in parallel, with previews for your approval.',
                    'steps' => [
                        'Website pages, form, and booking path',
                        'Flyers, card, letterhead, signature',
                        'Previews for your approval',
                    ],
                ],
                [
                    'name' => 'Launch',
                    'summary' => 'Your website goes live with booking and contact working; you receive final brand files.',
                    'steps' => [
                        'Site live with booking and contact',
                        'Final brand file handoff',
                        '30-day post-launch fix window',
                    ],
                ],
                [
                    'name' => 'Google',
                    'summary' => 'We set up your Google Business Profile, Analytics 4, and Search Console, confirm you own the accounts, and do a first check-in once data arrives.',
                    'steps' => [
                        'Business Profile setup and verification guidance',
                        'Analytics and Search Console connected',
                        'First monitoring check-in',
                    ],
                ],
            ],
        ],
    ],
    'business-growth-website' => [
        'slug' => 'business-growth-website',
        'nav_label' => 'Business Growth',
        'title' => 'Business Growth Website',
        'price' => '$2,500+',
        'price_note' => 'Quoted from a written estimate',
        'turnaround' => '1 week or more',
        'meta_description' => 'Custom business growth website: unlimited pages, integrations, booking flows, marketing materials, and full Google setup. Quoted from $2,500. Timeline typically a week or more by scope.',
        'summary' => 'End-to-end web and marketing buildouts shaped around your goals, systems, and growth plans — with Google services built in.',
        'includes' => [
            'Unlimited pages and custom functionality',
            'Unlimited third-party integrations, booking flows, or calendars',
            'Unlimited custom marketing materials',
            'Google Business Profile, Analytics 4, and Search Console setup',
            'Measurement baseline and growth items from your estimate',
            'Entire package fit to your specifications and business needs',
        ],
        'ideal_for' => 'Established businesses with unique workflows, multiple locations, or custom tools.',
        'badge' => '',
        'tier' => 3,
        'features' => [
            'pages' => 'Unlimited',
            'contact_form' => true,
            'call_book_buttons' => true,
            'booking_integration' => true,
            'custom_design' => true,
            'google_business_profile' => true,
            'google_analytics' => true,
            'google_search_console' => true,
            'flyers' => 'Unlimited',
            'business_card' => true,
            'letterhead' => true,
            'email_signature' => true,
            'custom_functionality' => true,
            'third_party_integrations' => 'Unlimited',
        ],
        'process' => [
            'title' => 'How we work',
            'intro' => 'Built around a written plan for your business — nothing cookie-cutter.',
            'phases' => [
                [
                    'name' => 'Planning',
                    'summary' => 'We map your goals, pages, and tools in a written estimate and milestone schedule before build starts.',
                    'steps' => [
                        'Discovery and requirements',
                        'Written estimate and milestones',
                        'Clear ownership of each step',
                    ],
                ],
                [
                    'name' => 'Building',
                    'summary' => 'We build to that estimate (site features, integrations, and marketing as listed), with milestone reviews along the way.',
                    'steps' => [
                        'Build to the approved estimate',
                        'Integrations as listed',
                        'Milestone reviews and updates',
                    ],
                ],
                [
                    'name' => 'Launch',
                    'summary' => 'We go live with a clear checklist, training notes, and final handoff.',
                    'steps' => [
                        'Go-live checklist',
                        'Training and handoff notes',
                        '30-day post-launch fix window',
                    ],
                ],
                [
                    'name' => 'Google and growth',
                    'summary' => 'We set up Business Profile, Analytics, and Search Console, establish a measurement baseline, and follow the growth items in your estimate — extras need a simple change order.',
                    'steps' => [
                        'Google services and measurement baseline',
                        'Growth items from your estimate',
                        'Change orders for anything new',
                    ],
                ],
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

/**
 * SEO landing page definitions for Metro Detroit Holiday Lighting.
 *
 * @return array<string, array<string, mixed>>
 */
function mdhl_landing_pages(): array
{
    return [
        'homeowners' => [
            'nav_label' => 'Homeowners',
            'title' => 'Holiday Lighting Installation Metro Detroit | Homeowners',
            'description' => 'Professional Christmas lights installation for Metro Detroit homes. Residential holiday lighting design, install, and take-down by Steven. Call (248) 525-4715.',
            'h1' => 'Holiday lighting installation for Metro Detroit homeowners',
            'intro' => 'Looking for christmas light installation near me that does the job right? Metro Detroit Holiday Lighting provides professional christmas lights installation across Michigan homes — clean rooflines, trees, walks, and accents without you climbing a ladder.',
            'hero_image' => 'assets/img/gallery/residential-08-clark-white-tree.jpg',
            'hero_alt' => 'Residential home with white holiday tree lighting',
            'service_name' => 'Residential holiday lighting installation',
            'gallery_match' => ['/^residential-/i'],
            'related' => ['wreaths', 'tree-wraps', 'high-roof-lighting'],
            'sections' => [
                [
                    'h2' => 'Residential christmas light installation that fits your home',
                    'body' => 'Every house is different. Steven designs a custom outdoor christmas lights installation service plan from your photos — peaks, gutters, porches, and landscaping — so the display looks balanced from the street and holds up through Michigan weather.',
                    'bullets' => [
                        'Roofline christmas lights installation with secure clips',
                        'Trees, bushes, walks, and accent lighting',
                        'Full service holiday lighting from design through take-down',
                        'Christmas lights install and takedown with labeled off-season storage',
                    ],
                ],
                [
                    'h2' => 'Christmas light installers Metro Detroit homeowners trust',
                    'body' => 'Metro Detroit Holiday Lighting is owner-operated and fully insured. You work with Steven from the first quote through spring removal — the same christmas light installers Metro Detroit families call when they want a polished seasonal display without the hassle.',
                ],
                [
                    'h2' => 'Service across Oakland, Wayne, and Macomb County',
                    'body' => 'We install holiday lighting for homes throughout southeastern Michigan, including Birmingham, Troy, Rochester Hills, Sterling Heights, Livonia, Novi, Royal Oak, and neighboring communities. If you are searching for a holiday lighting company Oakland County or Wayne/Macomb neighborhoods trust, we likely cover your city.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'Who installs christmas lights on houses in Metro Detroit?',
                    'a' => 'Metro Detroit Holiday Lighting — Steven designs and installs residential christmas light displays across Oakland, Wayne, and Macomb County. Call (248) 525-4715 with photos of your home for a plan.',
                ],
                [
                    'q' => 'Do you offer christmas light installation in Troy, Rochester Hills, or Birmingham?',
                    'a' => 'Yes. Those cities and many nearby suburbs are part of our Metro Detroit service area. Browse Service Areas or text photos to confirm your neighborhood.',
                ],
                [
                    'q' => 'Is this seasonal lighting or permanent holiday lighting?',
                    'a' => 'We specialize in seasonal full-service holiday lighting: design, install, mid-season care, take-down, and storage. Ask Steven if you have questions about permanent vs seasonal holiday lighting options for your home.',
                ],
            ],
        ],

        'businesses' => [
            'nav_label' => 'Businesses',
            'title' => 'Commercial Christmas Light Installation Metro Detroit',
            'description' => 'Commercial holiday lighting for Metro Detroit storefronts, offices, and dealerships. Insured business christmas light installation by Steven. Call (248) 525-4715.',
            'h1' => 'Commercial holiday lighting for Metro Detroit businesses',
            'intro' => 'Stand out this season with commercial christmas light installation that looks sharp from the curb. Metro Detroit Holiday Lighting delivers commercial holiday lighting Metro Detroit businesses rely on — storefronts, offices, restaurants, and dealerships included.',
            'hero_image' => 'assets/img/audiences/businesses.jpg',
            'hero_alt' => 'Commercial storefront with professional holiday lighting',
            'service_name' => 'Commercial holiday lighting installation',
            'gallery_match' => ['/^commercial-/i'],
            'related' => ['wreaths', 'hoa-property-managers', 'tree-wraps'],
            'sections' => [
                [
                    'h2' => 'Business christmas light installation that stays maintained',
                    'body' => 'Your customers notice the entrance first. We plan storefront holiday lighting, office building christmas lights, and restaurant holiday lighting installation so displays look intentional after dark and stay maintained through January.',
                    'bullets' => [
                        'Storefront and canopy lighting',
                        'Dealership christmas lights and lot accents',
                        'Shopping center holiday lighting and entrance features',
                        'Mid-season fixes if a strand fails or weather hits hard',
                    ],
                ],
                [
                    'h2' => 'Insured commercial christmas light installation',
                    'body' => 'Commercial work needs clear communication and coverage. Metro Detroit Holiday Lighting is fully insured for commercial christmas light installation. Steven owns the company and does the work — professional holiday decorators for businesses Detroit-area managers can call directly.',
                ],
                [
                    'h2' => 'Commercial christmas light installers Oakland County and beyond',
                    'body' => 'We serve businesses across Oakland, Wayne, and Macomb County — from Troy and Novi office parks to Dearborn and Sterling Heights retail corridors. Send photos of the building and your preferred timing; we map an install window before the holiday rush.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'Do you install dealership christmas lights and shopping center displays?',
                    'a' => 'Yes. Dealership lots, shopping centers, and multi-tenant entrances are common commercial projects. Share site photos and access notes so Steven can plan safely.',
                ],
                [
                    'q' => 'Are you insured for commercial holiday lighting?',
                    'a' => 'Yes. We carry insurance for insured commercial christmas light installation on business properties across Metro Detroit.',
                ],
                [
                    'q' => 'How do businesses get a quote?',
                    'a' => 'Email or text clear photos of the storefront or building to (248) 525-4715 or metrodetroitholidaylighting@gmail.com. Include your city and any brand color preferences.',
                ],
            ],
        ],

        'hoa-property-managers' => [
            'nav_label' => 'HOAs & property managers',
            'title' => 'HOA Christmas Light Installation Metro Detroit',
            'description' => 'HOA christmas light installation and community entrance holiday lighting for Metro Detroit associations and property managers. One crew, one plan. Call (248) 525-4715.',
            'h1' => 'HOA and community entrance holiday lighting',
            'intro' => 'Associations and managers need one reliable partner for hoa christmas light installation. Metro Detroit Holiday Lighting handles community entrance holiday lighting, clubhouses, and shared spaces so residents see a polished season display without juggling multiple vendors.',
            'hero_image' => 'assets/img/gallery/commercial-07-how-entrance.jpg',
            'hero_alt' => 'Community entrance with professional holiday lighting',
            'service_name' => 'HOA and community holiday lighting',
            'gallery_match' => ['/entrance/i', '/wreath/i', '/commercial-07/i', '/commercial-09/i', '/commercial-03/i'],
            'related' => ['businesses', 'wreaths', 'tree-wraps'],
            'sections' => [
                [
                    'h2' => 'Property manager holiday lighting with one clear plan',
                    'body' => 'Boards and property managers get a single scope: subdivision entrance christmas lights, monument and gate accents, clubhouse holiday lighting, and common area christmas lighting — scheduled around community access and weather.',
                    'bullets' => [
                        'Entrance monument lighting for Christmas and the holiday season',
                        'Condo association holiday lighting for shared buildings and lobbies',
                        'Multi property holiday lighting contracts when you manage several communities',
                        'One call for mid-season repairs across the display',
                    ],
                ],
                [
                    'h2' => 'Holiday lighting for HOA communities Metro Detroit trusts',
                    'body' => 'From Oakland County subdivisions to Wayne and Macomb associations, Steven designs displays that look consistent at the entrance and scale cleanly for take-down. Residents see the same high standard every year without board members on ladders.',
                ],
                [
                    'h2' => 'One vendor for multiple properties',
                    'body' => 'If you oversee more than one community, ask about coordinating install windows and storage. One vendor holiday lighting for multiple properties keeps invoicing, access, and mid-season service straightforward.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'Can you light entrance monuments for Christmas in Michigan?',
                    'a' => 'Yes. Entrance monument lighting Christmas Michigan communities expect is a core part of our HOA work — columns, signs, and gate features included.',
                ],
                [
                    'q' => 'Do you work with property managers and board presidents?',
                    'a' => 'Yes. We coordinate with property manager holiday lighting requests, board approvals, and site access rules so install day stays organized.',
                ],
                [
                    'q' => 'What photos should we send for an HOA quote?',
                    'a' => 'Entrance, clubhouse, and any common areas you want lit. Note your city and preferred switch-on date. Text or email (248) 525-4715.',
                ],
            ],
        ],

        'wreaths' => [
            'nav_label' => 'Wreaths',
            'title' => 'Christmas Wreath Installation Service Metro Detroit',
            'description' => 'Christmas wreath installation service and holiday wreath hanging for Metro Detroit homes and businesses. Lighted wreaths and garland by Steven. Call (248) 525-4715.',
            'h1' => 'Christmas wreath installation service in Metro Detroit',
            'intro' => 'Need a christmas wreath installation service that looks finished from the street? Metro Detroit Holiday Lighting offers holiday wreath hanging service and lighted wreath installation for doors, porches, and building entrances across southeastern Michigan.',
            'hero_image' => 'assets/img/gallery/hanging-the-wreath.jpg',
            'hero_alt' => 'Professional holiday wreath hanging by Metro Detroit Holiday Lighting',
            'service_name' => 'Holiday wreath and garland installation',
            'gallery_match' => ['/wreath/i', '/garland/i'],
            'gallery_files' => [
                'commercial-04-daytime-garland-wreath.jpg',
                'residential-11-daytime-wreath-garland-planters.jpg',
                'residential-12-daytime-wreaths.jpg',
                'residential-15-rood-height-install.jpg',
            ],
            'related' => ['homeowners', 'businesses', 'hoa-property-managers'],
            'sections' => [
                [
                    'h2' => 'Front door wreath installation and porch accents',
                    'body' => 'From a single front door wreath installation to matching pairs on a two-story facade, Steven sizes and mounts wreaths so they hang level and stay secure through wind and snow.',
                    'bullets' => [
                        'Lighted wreath installation with clean power routing',
                        'Holiday garland installation for front porch rails and columns',
                        'Custom holiday wreaths Metro Detroit homes and businesses can coordinate with roof and tree lighting',
                    ],
                ],
                [
                    'h2' => 'Commercial wreath and garland for entrances',
                    'body' => 'Offices and storefronts often need commercial wreath and garland that match brand colors and building scale. We hang building entrance wreath and garland displays that photograph well for daytime and glow after dark.',
                ],
                [
                    'h2' => 'Professional wreath hanging near you',
                    'body' => 'Serving Oakland, Wayne, and Macomb County. Whether you need professional wreath hanging near me for a residential porch or to install a wreath on a second story entrance, we bring the gear and leave the facade clean.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'Can you install a wreath on a second-story entrance?',
                    'a' => 'Yes. Second-story and tall entrance wreaths are routine when paired with proper mounting. Mention height when you send photos.',
                ],
                [
                    'q' => 'Do wreaths come with lights?',
                    'a' => 'We can install lighted wreaths or hang your preferred style. Ask about lighted wreath installation when you request a quote.',
                ],
                [
                    'q' => 'Can wreaths be part of a full lighting package?',
                    'a' => 'Absolutely. Many homeowners and businesses combine wreaths with rooflines, garland, and trees for a complete holiday look.',
                ],
            ],
        ],

        'tree-wraps' => [
            'nav_label' => 'Tree wraps',
            'title' => 'Branch Wrap Tree Lights Metro Detroit | Tree Wrapping',
            'description' => 'Branch wrap tree lights and outdoor tree light wrapping in Metro Detroit. Professional christmas tree wrapping for landscape trees. Call (248) 525-4715.',
            'h1' => 'Branch wrap tree lights for Metro Detroit landscapes',
            'intro' => 'Transform trunks and canopies with branch wrap tree lights installed the professional way. Metro Detroit Holiday Lighting specializes in christmas tree wrapping service and outdoor tree light wrapping that looks dense, even, and built for Michigan winters.',
            'hero_image' => 'assets/img/gallery/residential-03-birchwood-multicolor-tree.jpg',
            'hero_alt' => 'Outdoor tree wrapped with professional holiday lights',
            'service_name' => 'Branch wrap tree lighting',
            'gallery_match' => ['/tree/i'],
            'related' => ['homeowners', 'businesses', 'wreaths'],
            'sections' => [
                [
                    'h2' => 'Branch wrap tree lights for landscape trees',
                    'body' => 'Branch wrapping outdoor trees is more than tossing strands into the canopy. Steven wraps limbs and trunks so light coverage stays consistent from the ground up — ideal for front-yard specimens and commercial landscaping.',
                    'bullets' => [
                        'Branch wrap tree lights on deciduous and evergreen trees',
                        'Trunk wrap tree lights for strong vertical glow',
                        'Large tree christmas lights installation with secure routing',
                        'Landscape tree holiday lighting that pairs with rooflines and walks',
                    ],
                ],
                [
                    'h2' => 'Professional tree lighting installation',
                    'body' => 'Whether you want warm white, cool white, or color, we wrap trees with christmas lights sized for the canopy. Professional tree lighting installation means clean power plans, tidy connections, and displays that still look sharp after a snowstorm.',
                ],
                [
                    'h2' => 'Branch wrap tree lights Metro Detroit and Oakland County',
                    'body' => 'Homeowners and businesses across southeastern Michigan book tree wrap christmas lights Oakland County and neighboring counties trust. From single statement trees to rows of lot trees, ask Steven how to decorate large trees with professional installers on your timeline.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'How much does it cost to wrap outdoor trees with lights?',
                    'a' => 'Pricing depends on tree height, branch density, and how many trees you want wrapped. Send photos and approximate heights to (248) 525-4715 for a clear estimate.',
                ],
                [
                    'q' => 'Do you wrap trunks only or full branch wraps?',
                    'a' => 'Both. Many clients choose trunk wrap tree lights for a clean look; others prefer full branch wrap tree lights for a richer canopy glow.',
                ],
                [
                    'q' => 'Can tree wraps be combined with roof lighting?',
                    'a' => 'Yes. Tree wraps often complete a residential or commercial package alongside rooflines, wreaths, and walk lighting.',
                ],
            ],
        ],

        'high-roof-lighting' => [
            'nav_label' => 'High roofs',
            'title' => 'High Roof Christmas Light Installation Metro Detroit',
            'description' => 'High roof christmas light installation and steep roof holiday lighting in Metro Detroit. Insured tall roofline christmas lights by Steven. Call (248) 525-4715.',
            'h1' => 'High roof christmas light installation in Metro Detroit',
            'intro' => 'Steep pitches and tall peaks are not DIY projects. Metro Detroit Holiday Lighting handles high roof christmas light installation and steep roof holiday lighting with the right equipment — so your tall roofline christmas lights look sharp without putting your household on a ladder.',
            'hero_image' => 'assets/img/gallery/residential-15-rood-height-install.jpg',
            'hero_alt' => 'High roof holiday lighting installation in Metro Detroit',
            'service_name' => 'High and steep roof holiday lighting',
            'gallery_match' => ['/roof/i', '/rood/i', '/height/i'],
            'related' => ['homeowners', 'tree-wraps', 'wreaths'],
            'sections' => [
                [
                    'h2' => 'Two story house christmas lights installation',
                    'body' => 'Two story house christmas lights installation needs secure clips, careful peak work, and clean lines along high gutters. We plan each run so christmas lights on high gutters stay tight through wind and ice.',
                    'bullets' => [
                        'Peak roof holiday lighting on steep gables',
                        'Tall fascia and gutter runs',
                        'Insured high roof light installers Michigan homeowners can trust',
                        'Take-down handled the same careful way in the spring',
                    ],
                ],
                [
                    'h2' => 'Skip dangerous roof christmas lights DIY',
                    'body' => 'If a roof feels too high or too steep, hire a professional. Dangerous roof christmas lights are a leading winter injury risk. Steven brings the experience for two story roofline lighting Metro Detroit homes need — without you climbing.',
                ],
                [
                    'h2' => 'Christmas light installation without the ladder hassle',
                    'body' => 'Looking for christmas light installation without ladder service at your house? Send elevation photos and note any tricky peaks. We quote high-roof work across Oakland, Wayne, and Macomb County and schedule installs before peak season fills.',
                ],
            ],
            'faq' => [
                [
                    'q' => 'Do you install lights on steep or tall roofs?',
                    'a' => 'Yes. High roof and steep roof holiday lighting is a core service. Share photos so Steven can assess access and pitch.',
                ],
                [
                    'q' => 'Are you insured for high-roof work?',
                    'a' => 'Yes. We are insured high roof light installers serving Michigan homes and buildings in Metro Detroit.',
                ],
                [
                    'q' => 'Can high-roof lighting include wreaths or trees?',
                    'a' => 'Yes. Many packages combine tall rooflines with wreaths, garland, and tree wraps for a complete display.',
                ],
            ],
        ],
    ];
}

/**
 * @return array<string, mixed>|null
 */
function mdhl_landing_by_slug(string $slug): ?array
{
    $pages = mdhl_landing_pages();
    return $pages[$slug] ?? null;
}

/**
 * Nav items for Services dropdown: label => path relative to site root.
 *
 * @return array<string, string>
 */
function mdhl_service_nav(): array
{
    $out = [];
    foreach (mdhl_landing_pages() as $slug => $page) {
        $out[$page['nav_label']] = $slug . '/';
    }
    return $out;
}

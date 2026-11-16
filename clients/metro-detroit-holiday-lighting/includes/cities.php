<?php

declare(strict_types=1);

/**
 * Priority Metro Detroit cities for service-area landing pages.
 * Keep this list focused (high-intent search markets), not every township.
 *
 * Optional keys for richer city pages:
 * - intro: unique 2–3 sentence lead (priority SEO cities)
 * - gallery_match: PCRE patterns for a small photo strip on the city page
 *
 * @return list<array{slug:string,name:string,county:string,nearby:list<string>,intro?:string,gallery_match?:list<string>}>
 */
function mdhl_cities(): array
{
    return [
        // Oakland County — priority intros (competitive density)
        [
            'slug' => 'birmingham',
            'name' => 'Birmingham',
            'county' => 'Oakland',
            'nearby' => ['bloomfield-hills', 'troy', 'royal-oak', 'southfield'],
            'intro' => 'Birmingham homeowners and storefronts want clean rooflines and tasteful accents that fit the village streetscape — not a tangled DIY project in December. Steven designs and installs full-season holiday lighting for Birmingham homes, businesses, and HOA entrances, then returns for mid-season fixes, take-down, and storage.',
            'gallery_match' => ['/^residential-/i', '/wreath/i'],
        ],
        [
            'slug' => 'bloomfield-hills',
            'name' => 'Bloomfield Hills',
            'county' => 'Oakland',
            'nearby' => ['birmingham', 'west-bloomfield', 'troy', 'pontiac'],
            'intro' => 'Bloomfield Hills properties often mean taller peaks, longer gutters, and landscaping that deserves careful tree wraps. Metro Detroit Holiday Lighting plans displays around the architecture — owner-operated and fully insured — so estates and family homes look finished from the curb without anyone on a ladder.',
            'gallery_match' => ['/^residential-/i', '/roof/i', '/rood/i'],
        ],
        [
            'slug' => 'troy',
            'name' => 'Troy',
            'county' => 'Oakland',
            'nearby' => ['birmingham', 'rochester-hills', 'sterling-heights', 'royal-oak'],
            'intro' => 'Troy is one of Metro Detroit’s busiest holiday-lighting corridors — homes, offices, and retail all competing for attention after dark. Steven maps a custom plan from your photos, installs commercial-grade lighting, and stays available if a strand fails mid-season.',
            'gallery_match' => ['/^residential-/i', '/^commercial-/i'],
        ],
        [
            'slug' => 'rochester-hills',
            'name' => 'Rochester Hills',
            'county' => 'Oakland',
            'nearby' => ['rochester', 'troy', 'auburn-hills', 'lake-orion'],
            'intro' => 'Rochester Hills neighborhoods fill early once fall booking starts. Whether you need a classic warm-white roofline or trees and walk accents, Metro Detroit Holiday Lighting handles design, install, take-down, and labeled storage so next season starts ready.',
            'gallery_match' => ['/^residential-/i', '/tree/i'],
        ],
        [
            'slug' => 'rochester',
            'name' => 'Rochester',
            'county' => 'Oakland',
            'nearby' => ['rochester-hills', 'auburn-hills', 'lake-orion', 'troy'],
            'intro' => 'Downtown Rochester and nearby streets shine for the holidays — your home or storefront can match that energy without the ladder work. Steven installs and maintains seasonal displays across Rochester, then takes everything down after the New Year.',
            'gallery_match' => ['/^residential-/i', '/wreath/i'],
        ],
        [
            'slug' => 'royal-oak',
            'name' => 'Royal Oak',
            'county' => 'Oakland',
            'nearby' => ['birmingham', 'ferndale', 'troy', 'southfield'],
            'intro' => 'Royal Oak homes and Main Street businesses need displays that look sharp from the sidewalk and hold up through Michigan weather. Owner-operated holiday lighting from Steven covers rooflines, wreaths, trees, and storefront accents with mid-season support included.',
            'gallery_match' => ['/^residential-/i', '/^commercial-/i', '/wreath/i'],
        ],
        [
            'slug' => 'ferndale',
            'name' => 'Ferndale',
            'county' => 'Oakland',
            'nearby' => ['royal-oak', 'southfield', 'detroit', 'warren'],
        ],
        [
            'slug' => 'novi',
            'name' => 'Novi',
            'county' => 'Oakland',
            'nearby' => ['northville', 'farmington-hills', 'livonia', 'plymouth'],
            'intro' => 'Novi’s mix of newer builds, office parks, and HOA entrances calls for one clear lighting plan — not three vendors. Metro Detroit Holiday Lighting designs residential, commercial, and community displays for Novi and nearby west-side suburbs, with storage after take-down.',
            'gallery_match' => ['/^residential-/i', '/entrance/i', '/^commercial-/i'],
        ],
        [
            'slug' => 'farmington-hills',
            'name' => 'Farmington Hills',
            'county' => 'Oakland',
            'nearby' => ['livonia', 'novi', 'west-bloomfield', 'southfield'],
        ],
        [
            'slug' => 'west-bloomfield',
            'name' => 'West Bloomfield',
            'county' => 'Oakland',
            'nearby' => ['bloomfield-hills', 'farmington-hills', 'waterford', 'pontiac'],
        ],
        [
            'slug' => 'waterford',
            'name' => 'Waterford',
            'county' => 'Oakland',
            'nearby' => ['pontiac', 'clarkston', 'west-bloomfield', 'auburn-hills'],
        ],
        [
            'slug' => 'clarkston',
            'name' => 'Clarkston',
            'county' => 'Oakland',
            'nearby' => ['waterford', 'lake-orion', 'auburn-hills', 'rochester'],
        ],
        [
            'slug' => 'auburn-hills',
            'name' => 'Auburn Hills',
            'county' => 'Oakland',
            'nearby' => ['rochester-hills', 'pontiac', 'lake-orion', 'troy'],
        ],
        [
            'slug' => 'lake-orion',
            'name' => 'Lake Orion',
            'county' => 'Oakland',
            'nearby' => ['rochester', 'auburn-hills', 'clarkston', 'rochester-hills'],
        ],
        [
            'slug' => 'southfield',
            'name' => 'Southfield',
            'county' => 'Oakland',
            'nearby' => ['farmington-hills', 'birmingham', 'detroit', 'royal-oak'],
        ],
        [
            'slug' => 'pontiac',
            'name' => 'Pontiac',
            'county' => 'Oakland',
            'nearby' => ['auburn-hills', 'waterford', 'bloomfield-hills', 'lake-orion'],
        ],
        // Wayne County
        [
            'slug' => 'livonia',
            'name' => 'Livonia',
            'county' => 'Wayne',
            'nearby' => ['farmington-hills', 'plymouth', 'westland', 'northville'],
            'intro' => 'Livonia families and businesses book holiday lighting when they want a polished curb appeal without weekends on the roof. Steven provides design, install, mid-season care, and take-down across Livonia — text photos for a custom quote.',
            'gallery_match' => ['/^residential-/i', '/tree/i'],
        ],
        [
            'slug' => 'plymouth',
            'name' => 'Plymouth',
            'county' => 'Wayne',
            'nearby' => ['canton', 'northville', 'livonia', 'novi'],
        ],
        [
            'slug' => 'canton',
            'name' => 'Canton',
            'county' => 'Wayne',
            'nearby' => ['plymouth', 'westland', 'northville', 'livonia'],
        ],
        [
            'slug' => 'northville',
            'name' => 'Northville',
            'county' => 'Wayne',
            'nearby' => ['novi', 'plymouth', 'livonia', 'farmington-hills'],
            'intro' => 'Northville’s historic downtown energy and nearby subdivisions reward lighting that looks intentional, not temporary. Metro Detroit Holiday Lighting installs full-season displays for Northville homes, storefronts, and community entrances — owner-operated from first photo to spring storage.',
            'gallery_match' => ['/^residential-/i', '/wreath/i', '/entrance/i'],
        ],
        [
            'slug' => 'grosse-pointe',
            'name' => 'Grosse Pointe',
            'county' => 'Wayne',
            'nearby' => ['detroit', 'st-clair-shores', 'warren', 'ferndale'],
        ],
        [
            'slug' => 'dearborn',
            'name' => 'Dearborn',
            'county' => 'Wayne',
            'nearby' => ['detroit', 'westland', 'livonia', 'southfield'],
        ],
        [
            'slug' => 'westland',
            'name' => 'Westland',
            'county' => 'Wayne',
            'nearby' => ['livonia', 'canton', 'dearborn', 'plymouth'],
        ],
        [
            'slug' => 'detroit',
            'name' => 'Detroit',
            'county' => 'Wayne',
            'nearby' => ['grosse-pointe', 'dearborn', 'ferndale', 'warren'],
        ],
        // Macomb County
        [
            'slug' => 'sterling-heights',
            'name' => 'Sterling Heights',
            'county' => 'Macomb',
            'nearby' => ['warren', 'troy', 'clinton-township', 'shelby-township'],
            'intro' => 'Sterling Heights homes and commercial corridors need reliable seasonal lighting that survives wind, ice, and busy parking lots. Steven designs and installs residential and business displays across Sterling Heights, with mid-season fixes and labeled off-season storage.',
            'gallery_match' => ['/^residential-/i', '/^commercial-/i'],
        ],
        [
            'slug' => 'warren',
            'name' => 'Warren',
            'county' => 'Macomb',
            'nearby' => ['sterling-heights', 'detroit', 'st-clair-shores', 'ferndale'],
        ],
        [
            'slug' => 'clinton-township',
            'name' => 'Clinton Township',
            'county' => 'Macomb',
            'nearby' => ['sterling-heights', 'macomb', 'shelby-township', 'st-clair-shores'],
        ],
        [
            'slug' => 'shelby-township',
            'name' => 'Shelby Township',
            'county' => 'Macomb',
            'nearby' => ['sterling-heights', 'rochester-hills', 'clinton-township', 'macomb'],
        ],
        [
            'slug' => 'st-clair-shores',
            'name' => 'St. Clair Shores',
            'county' => 'Macomb',
            'nearby' => ['grosse-pointe', 'warren', 'clinton-township', 'detroit'],
        ],
        [
            'slug' => 'macomb',
            'name' => 'Macomb Township',
            'county' => 'Macomb',
            'nearby' => ['clinton-township', 'shelby-township', 'sterling-heights', 'st-clair-shores'],
        ],
    ];
}

/**
 * @return array{slug:string,name:string,county:string,nearby:list<string>,intro?:string,gallery_match?:list<string>}|null
 */
function mdhl_city_by_slug(string $slug): ?array
{
    $slug = strtolower(trim($slug));
    foreach (mdhl_cities() as $city) {
        if ($city['slug'] === $slug) {
            return $city;
        }
    }

    return null;
}

/**
 * @return array<string, list<array{slug:string,name:string,county:string,nearby:list<string>,intro?:string,gallery_match?:list<string>}>>
 */
function mdhl_cities_by_county(): array
{
    $grouped = [];
    foreach (mdhl_cities() as $city) {
        $grouped[$city['county']][] = $city;
    }

    return $grouped;
}

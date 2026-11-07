<?php

declare(strict_types=1);

/**
 * Priority Metro Detroit cities for service-area landing pages.
 * Keep this list focused (high-intent search markets), not every township.
 *
 * @return list<array{slug:string,name:string,county:string,nearby:list<string>}>
 */
function mdhl_cities(): array
{
    return [
        // Oakland County
        ['slug' => 'birmingham', 'name' => 'Birmingham', 'county' => 'Oakland', 'nearby' => ['bloomfield-hills', 'troy', 'royal-oak', 'southfield']],
        ['slug' => 'bloomfield-hills', 'name' => 'Bloomfield Hills', 'county' => 'Oakland', 'nearby' => ['birmingham', 'west-bloomfield', 'troy', 'pontiac']],
        ['slug' => 'troy', 'name' => 'Troy', 'county' => 'Oakland', 'nearby' => ['birmingham', 'rochester-hills', 'sterling-heights', 'royal-oak']],
        ['slug' => 'rochester-hills', 'name' => 'Rochester Hills', 'county' => 'Oakland', 'nearby' => ['rochester', 'troy', 'auburn-hills', 'lake-orion']],
        ['slug' => 'rochester', 'name' => 'Rochester', 'county' => 'Oakland', 'nearby' => ['rochester-hills', 'auburn-hills', 'lake-orion', 'troy']],
        ['slug' => 'royal-oak', 'name' => 'Royal Oak', 'county' => 'Oakland', 'nearby' => ['birmingham', 'ferndale', 'troy', 'southfield']],
        ['slug' => 'ferndale', 'name' => 'Ferndale', 'county' => 'Oakland', 'nearby' => ['royal-oak', 'southfield', 'detroit', 'warren']],
        ['slug' => 'novi', 'name' => 'Novi', 'county' => 'Oakland', 'nearby' => ['northville', 'farmington-hills', 'livonia', 'plymouth']],
        ['slug' => 'farmington-hills', 'name' => 'Farmington Hills', 'county' => 'Oakland', 'nearby' => ['livonia', 'novi', 'west-bloomfield', 'southfield']],
        ['slug' => 'west-bloomfield', 'name' => 'West Bloomfield', 'county' => 'Oakland', 'nearby' => ['bloomfield-hills', 'farmington-hills', 'waterford', 'pontiac']],
        ['slug' => 'waterford', 'name' => 'Waterford', 'county' => 'Oakland', 'nearby' => ['pontiac', 'clarkston', 'west-bloomfield', 'auburn-hills']],
        ['slug' => 'clarkston', 'name' => 'Clarkston', 'county' => 'Oakland', 'nearby' => ['waterford', 'lake-orion', 'auburn-hills', 'rochester']],
        ['slug' => 'auburn-hills', 'name' => 'Auburn Hills', 'county' => 'Oakland', 'nearby' => ['rochester-hills', 'pontiac', 'lake-orion', 'troy']],
        ['slug' => 'lake-orion', 'name' => 'Lake Orion', 'county' => 'Oakland', 'nearby' => ['rochester', 'auburn-hills', 'clarkston', 'rochester-hills']],
        ['slug' => 'southfield', 'name' => 'Southfield', 'county' => 'Oakland', 'nearby' => ['farmington-hills', 'birmingham', 'detroit', 'royal-oak']],
        ['slug' => 'pontiac', 'name' => 'Pontiac', 'county' => 'Oakland', 'nearby' => ['auburn-hills', 'waterford', 'bloomfield-hills', 'lake-orion']],
        // Wayne County
        ['slug' => 'livonia', 'name' => 'Livonia', 'county' => 'Wayne', 'nearby' => ['farmington-hills', 'plymouth', 'westland', 'northville']],
        ['slug' => 'plymouth', 'name' => 'Plymouth', 'county' => 'Wayne', 'nearby' => ['canton', 'northville', 'livonia', 'novi']],
        ['slug' => 'canton', 'name' => 'Canton', 'county' => 'Wayne', 'nearby' => ['plymouth', 'westland', 'northville', 'livonia']],
        ['slug' => 'northville', 'name' => 'Northville', 'county' => 'Wayne', 'nearby' => ['novi', 'plymouth', 'livonia', 'farmington-hills']],
        ['slug' => 'grosse-pointe', 'name' => 'Grosse Pointe', 'county' => 'Wayne', 'nearby' => ['detroit', 'st-clair-shores', 'warren', 'ferndale']],
        ['slug' => 'dearborn', 'name' => 'Dearborn', 'county' => 'Wayne', 'nearby' => ['detroit', 'westland', 'livonia', 'southfield']],
        ['slug' => 'westland', 'name' => 'Westland', 'county' => 'Wayne', 'nearby' => ['livonia', 'canton', 'dearborn', 'plymouth']],
        ['slug' => 'detroit', 'name' => 'Detroit', 'county' => 'Wayne', 'nearby' => ['grosse-pointe', 'dearborn', 'ferndale', 'warren']],
        // Macomb County
        ['slug' => 'sterling-heights', 'name' => 'Sterling Heights', 'county' => 'Macomb', 'nearby' => ['warren', 'troy', 'clinton-township', 'shelby-township']],
        ['slug' => 'warren', 'name' => 'Warren', 'county' => 'Macomb', 'nearby' => ['sterling-heights', 'detroit', 'st-clair-shores', 'ferndale']],
        ['slug' => 'clinton-township', 'name' => 'Clinton Township', 'county' => 'Macomb', 'nearby' => ['sterling-heights', 'macomb', 'shelby-township', 'st-clair-shores']],
        ['slug' => 'shelby-township', 'name' => 'Shelby Township', 'county' => 'Macomb', 'nearby' => ['sterling-heights', 'rochester-hills', 'clinton-township', 'macomb']],
        ['slug' => 'st-clair-shores', 'name' => 'St. Clair Shores', 'county' => 'Macomb', 'nearby' => ['grosse-pointe', 'warren', 'clinton-township', 'detroit']],
        ['slug' => 'macomb', 'name' => 'Macomb Township', 'county' => 'Macomb', 'nearby' => ['clinton-township', 'shelby-township', 'sterling-heights', 'st-clair-shores']],
    ];
}

/**
 * @return array{slug:string,name:string,county:string,nearby:list<string>}|null
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
 * @return array<string, list<array{slug:string,name:string,county:string,nearby:list<string>}>>
 */
function mdhl_cities_by_county(): array
{
    $grouped = [];
    foreach (mdhl_cities() as $city) {
        $grouped[$city['county']][] = $city;
    }

    return $grouped;
}

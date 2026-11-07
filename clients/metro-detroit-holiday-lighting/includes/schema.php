<?php

declare(strict_types=1);

function mdhl_print_json_ld(array $data): void
{
    echo '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
        . '</script>' . "\n";
}

function mdhl_schema_local_business(): array
{
    global $site_name, $site_url, $phone, $email, $site_description, $company_name;

    $cities = array_map(static fn ($c) => $c['name'] . ', MI', mdhl_cities());

    return [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => rtrim((string) $site_url, '/') . '/#business',
        'name' => $company_name,
        'description' => $site_description,
        'url' => rtrim((string) $site_url, '/') . '/',
        'telephone' => $phone,
        'email' => $email,
        'image' => absolute_url('assets/img/og-default.svg'),
        'priceRange' => '$$',
        'areaServed' => [
            ['@type' => 'AdministrativeArea', 'name' => 'Oakland County, Michigan'],
            ['@type' => 'AdministrativeArea', 'name' => 'Wayne County, Michigan'],
            ['@type' => 'AdministrativeArea', 'name' => 'Macomb County, Michigan'],
            ...array_map(static fn ($name) => ['@type' => 'City', 'name' => $name], $cities),
        ],
        'knowsAbout' => [
            'Holiday lighting installation',
            'Christmas light design',
            'Commercial holiday lighting',
            'HOA holiday lighting',
        ],
    ];
}

/**
 * @param list<array{q:string,a:string}> $items
 */
function mdhl_schema_faq(array $items): array
{
    $entities = [];
    foreach ($items as $item) {
        $entities[] = [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['a'],
            ],
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $entities,
    ];
}

/**
 * @param list<array{name:string,url:string}> $crumbs
 */
function mdhl_schema_breadcrumbs(array $crumbs): array
{
    $items = [];
    foreach ($crumbs as $i => $crumb) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ];
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

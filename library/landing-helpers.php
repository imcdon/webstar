<?php

declare(strict_types=1);

/**
 * Build JSON-LD graphs for an SEO landing page.
 *
 * @return list<array<string, mixed>>
 */
function webstar_landing_json_ld(array $page, string $landingUrl, string $h1): array
{
    $config = webstar_config();
    $type = (string) ($page['type'] ?? 'region');
    $hub = webstar_seo_hub_for_type($type);

    $breadcrumbs = [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => webstar_production_site_url(''),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $hub['label'],
                'item' => webstar_production_site_url($hub['path']),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $h1,
                'item' => $landingUrl,
            ],
        ],
    ];

    $service = [
        '@type' => 'Service',
        'name' => $h1,
        'description' => (string) ($page['meta_description'] ?? ''),
        'url' => $landingUrl,
        'provider' => [
            '@type' => 'Organization',
            'name' => (string) ($config['legal_name'] ?? $config['business_name']),
            'url' => webstar_production_site_url(''),
        ],
        'areaServed' => (string) ($page['area_served'] ?? $config['service_area']),
    ];

    $package = webstar_package_by_slug((string) ($page['package_slug'] ?? ''));
    if ($package !== null) {
        $service['offers'] = [
            '@type' => 'Offer',
            'url' => webstar_production_site_url('packages/' . rawurlencode((string) $package['slug']) . '/'),
            'description' => (string) $package['price'],
        ];
    }

    $graphs = [$breadcrumbs, $service];

    $faqs = is_array($page['faq'] ?? null) ? $page['faq'] : [];
    $faqEntities = [];
    foreach ($faqs as $faq) {
        $q = trim((string) ($faq['q'] ?? ''));
        $a = trim((string) ($faq['a'] ?? ''));
        if ($q === '' || $a === '') {
            continue;
        }
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $q,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
        ];
    }
    if ($faqEntities !== []) {
        $graphs[] = ['@type' => 'FAQPage', 'mainEntity' => $faqEntities];
    }

    return $graphs;
}

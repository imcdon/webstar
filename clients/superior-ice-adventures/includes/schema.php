<?php
/*
 * schema.php - JSON-LD builders for LocalBusiness, Service, FAQ, Article, Breadcrumbs.
 */

if (!function_exists('sia_schema_local_business')) {
    function sia_schema_local_business(): array
    {
        global $site_name, $site_url, $site_description, $phone, $email, $address, $social, $nav_services;

        $same_as = array_values(array_filter([
            $social['instagram'] ?? '',
            $social['facebook'] ?? '',
        ]));

        $data = [
            '@context' => 'https://schema.org',
            '@type' => ['LocalBusiness', 'SportsActivityLocation'],
            '@id' => rtrim($site_url, '/') . '/#business',
            'name' => $site_name,
            'description' => $site_description,
            'url' => rtrim($site_url, '/') . '/',
            'telephone' => $phone,
            'email' => $email,
            'image' => absolute_url('assets/img/hero/inland-splake-fishing.webp'),
            'priceRange' => '$$',
            'areaServed' => [
                '@type' => 'Place',
                'name' => 'Marquette County and Lake Superior, Michigan',
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $address['city'] ?? 'Marquette',
                'addressRegion' => $address['region'] ?? 'MI',
                'postalCode' => $address['postal_code'] ?? '49855',
                'addressCountry' => $address['country'] ?? 'US',
            ],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Guided fishing and ice shanty rentals',
                'itemListElement' => array_map(static function ($svc) use ($site_url) {
                    return [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => $svc['label'],
                            'url' => rtrim($site_url, '/') . rtrim($svc['href'], '/') . '/',
                        ],
                    ];
                }, $nav_services),
            ],
        ];

        if ($same_as) {
            $data['sameAs'] = $same_as;
        }

        return $data;
    }

    function sia_schema_breadcrumbs(array $crumbs): array
    {
        $items = [];
        foreach (array_values($crumbs) as $i => $crumb) {
            $entry = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'],
            ];
            if (!empty($crumb['url'])) {
                $entry['item'] = $crumb['url'];
            }
            $items[] = $entry;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    function sia_schema_service(array $service): array
    {
        global $site_name, $site_url, $phone;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service['title'],
            'description' => $service['summary'] ?? ($service['card_blurb'] ?? ''),
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => $site_name,
                '@id' => rtrim($site_url, '/') . '/#business',
                'telephone' => $phone,
            ],
            'areaServed' => [
                '@type' => 'Place',
                'name' => 'Upper Peninsula and Lake Superior, Michigan',
            ],
            'url' => absolute_url($service['slug'] . '/'),
            'image' => absolute_url(ltrim($service['hero_image'] ?? 'assets/img/hero/inland-splake-fishing.webp', '/')),
        ];
    }

    function sia_schema_faq(array $faq_categories): array
    {
        $entities = [];
        foreach ($faq_categories as $cat) {
            foreach ($cat['items'] as $item) {
                $entities[] = [
                    '@type' => 'Question',
                    'name' => $item['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['a'],
                    ],
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    function sia_schema_article(array $article): array
    {
        global $site_name, $site_url;

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['blurb'] ?? '',
            'mainEntityOfPage' => absolute_url('articles/' . ($article['slug'] ?? '') . '/'),
            'author' => [
                '@type' => 'Organization',
                'name' => $site_name,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $site_name,
                'url' => rtrim($site_url, '/') . '/',
            ],
        ];

        if (!empty($article['published_at'])) {
            $data['datePublished'] = date('c', strtotime($article['published_at']));
        }
        if (!empty($article['updated_at'])) {
            $data['dateModified'] = date('c', strtotime($article['updated_at']));
        } elseif (!empty($article['published_at'])) {
            $data['dateModified'] = $data['datePublished'];
        }
        if (!empty($article['thumbnail'])) {
            $data['image'] = absolute_url(ltrim($article['thumbnail'], '/'));
        }

        return $data;
    }

    function sia_print_json_ld(array $graph): void
    {
        echo '<script type="application/ld+json">' .
            json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
            '</script>' . "\n";
    }
}

<?php

declare(strict_types=1);

function glhs_print_json_ld(array $data): void
{
    echo '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}

function glhs_schema_local_business(): array
{
    global $site_name, $site_description, $phone, $booking_url, $address, $hours, $site_url;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'HairSalon',
        'name' => $site_name,
        'description' => $site_description,
        'url' => $site_url,
        'telephone' => $phone,
        'image' => absolute_url('assets/img/og-default.svg'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => trim(($address['line1'] ?? '') . ' ' . ($address['line2'] ?? '')),
            'addressLocality' => $address['city'] ?? 'Waterford',
            'addressRegion' => $address['region'] ?? 'MI',
            'postalCode' => $address['postal_code'] ?? '',
            'addressCountry' => $address['country'] ?? 'US',
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Waterford'],
            ['@type' => 'City', 'name' => 'Clarkston'],
            ['@type' => 'City', 'name' => 'White Lake'],
            ['@type' => 'AdministrativeArea', 'name' => 'Oakland County'],
        ],
        'potentialAction' => [
            '@type' => 'ReserveAction',
            'target' => $booking_url,
            'name' => 'Book appointment',
        ],
    ];

    $opening = [];
    $dayMap = [
        'Monday' => 'Monday',
        'Tuesday' => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday' => 'Thursday',
        'Friday' => 'Friday',
        'Saturday' => 'Saturday',
        'Sunday' => 'Sunday',
    ];
    foreach ($hours as $row) {
        $day = $row['day'] ?? '';
        $hrs = $row['hours'] ?? '';
        if ($day === '' || $hrs === '' || strcasecmp($hrs, 'Closed') === 0) {
            continue;
        }
        if (preg_match('/(\d{1,2})\s*(AM|PM)\s*[–-]\s*(\d{1,2}(?::\d{2})?)\s*(AM|PM)/i', $hrs, $m)) {
            $open = glhs_to_24h($m[1], $m[2]);
            $close = glhs_to_24h($m[3], $m[4]);
            $opening[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $dayMap[$day] ?? $day,
                'opens' => $open,
                'closes' => $close,
            ];
        }
    }
    if ($opening !== []) {
        $schema['openingHoursSpecification'] = $opening;
    }

    return $schema;
}

function glhs_to_24h(string $time, string $ampm): string
{
    $ampm = strtoupper($ampm);
    if (str_contains($time, ':')) {
        [$h, $min] = array_map('intval', explode(':', $time, 2));
    } else {
        $h = (int) $time;
        $min = 0;
    }
    if ($ampm === 'PM' && $h < 12) {
        $h += 12;
    }
    if ($ampm === 'AM' && $h === 12) {
        $h = 0;
    }

    return sprintf('%02d:%02d', $h, $min);
}

/**
 * @param list<array{q:string,a:string}> $items
 */
function glhs_schema_faq(array $items): array
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

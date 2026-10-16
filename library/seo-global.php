<?php

declare(strict_types=1);

/**
 * Site-wide JSON-LD nodes (Organization + WebSite) merged in layout-start.php.
 *
 * @return list<array<string, mixed>>
 */
function webstar_global_json_ld(): array
{
    $config = webstar_config();
    $siteUrl = webstar_production_site_url('');
    $org = [
        '@type' => 'Organization',
        'name' => (string) ($config['legal_name'] ?? $config['business_name']),
        'alternateName' => (string) $config['business_name'],
        'url' => $siteUrl,
        'logo' => webstar_production_site_url('assets/images/star-icon-gold.png'),
        'email' => (string) $config['email'],
        'areaServed' => (string) $config['service_area'],
    ];
    $phone = webstar_phone_e164();
    if ($phone !== '') {
        $org['telephone'] = $phone;
    }
    $sameAs = array_values(array_filter(array_map('strval', $config['sameAs'] ?? [])));
    if ($sameAs !== []) {
        $org['sameAs'] = $sameAs;
    }

    $website = [
        '@type' => 'WebSite',
        'name' => (string) $config['business_name'],
        'url' => $siteUrl,
        'publisher' => ['@id' => $siteUrl . '#organization'],
    ];
    $org['@id'] = $siteUrl . '#organization';

    return [$org, $website];
}

/**
 * Canonical production origin (www + https). Local requests keep webstar_absolute_url behavior elsewhere.
 */
function webstar_production_site_url(string $path = ''): string
{
    $config = webstar_config();
    if (webstar_is_local_host()) {
        return webstar_absolute_url($path);
    }

    $base = 'https://www.' . (string) $config['domain'];

    return rtrim($base, '/') . webstar_url($path);
}

/**
 * Merge global graph nodes with page-level schema (JSON string or array).
 */
function webstar_json_ld_script(mixed $pageLd = null): string
{
    $graphs = webstar_global_json_ld();

    if ($pageLd !== null && $pageLd !== '') {
        if (is_string($pageLd)) {
            $decoded = json_decode($pageLd, true);
        } else {
            $decoded = $pageLd;
        }
        if (is_array($decoded)) {
            if (array_is_list($decoded)) {
                foreach ($decoded as $node) {
                    if (is_array($node)) {
                        unset($node['@context']);
                        $graphs[] = $node;
                    }
                }
            } else {
                unset($decoded['@context']);
                $graphs[] = $decoded;
            }
        }
    }

    return json_encode(
        ['@context' => 'https://schema.org', '@graph' => $graphs],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
}

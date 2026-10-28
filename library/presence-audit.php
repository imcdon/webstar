<?php

declare(strict_types=1);

/**
 * Short check of a guest’s website / social / Maps link for consultation follow-up.
 */

/**
 * @return array{status: string, url: string, title: string, https: bool, finding: string}
 */
function webstar_presence_audit(string $rawUrl, bool $fetch = true): array
{
    $empty = [
        'status' => 'none',
        'url' => '',
        'title' => '',
        'https' => false,
        'finding' => 'You did not include a website, Facebook, or Google Maps link with the booking.',
    ];

    $rawUrl = trim($rawUrl);
    if ($rawUrl === '') {
        return $empty;
    }

    if (!preg_match('#^https?://#i', $rawUrl)) {
        $rawUrl = 'https://' . $rawUrl;
    }

    $parts = parse_url($rawUrl);
    if (!is_array($parts) || empty($parts['host']) || !isset($parts['scheme'])) {
        return [
            'status' => 'invalid',
            'url' => $rawUrl,
            'title' => '',
            'https' => false,
            'finding' => 'The link you sent could not be read as a web address.',
        ];
    }

    $scheme = strtolower((string) $parts['scheme']);
    if ($scheme !== 'http' && $scheme !== 'https') {
        return [
            'status' => 'invalid',
            'url' => $rawUrl,
            'title' => '',
            'https' => false,
            'finding' => 'The link you sent could not be read as a web address.',
        ];
    }

    $host = strtolower((string) $parts['host']);
    $host = preg_replace('/^www\./', '', $host) ?? $host;
    $url = $scheme . '://' . (string) $parts['host']
        . (isset($parts['port']) ? ':' . $parts['port'] : '')
        . (string) ($parts['path'] ?? '')
        . (isset($parts['query']) ? '?' . $parts['query'] : '');

    if (webstar_presence_host_is_private($host)) {
        return [
            'status' => 'blocked',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent could not be opened safely.',
        ];
    }

    if (webstar_presence_host_matches($host, [
        'facebook.com', 'fb.com', 'fb.me', 'm.facebook.com',
        'instagram.com', 'tiktok.com', 'x.com', 'twitter.com',
        'linkedin.com', 'linktr.ee', 'beacons.ai', 'bio.link',
    ])) {
        return [
            'status' => 'social',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent is a social or link-in-bio page, not a website of your own.',
        ];
    }

    $isMaps = webstar_presence_host_matches($host, [
        'maps.google.com', 'goo.gl', 'maps.app.goo.gl',
    ]) || (
        (webstar_presence_host_matches($host, ['google.com']) || str_contains($host, 'google.'))
        && (str_contains(strtolower($url), '/maps') || str_starts_with($host, 'maps.'))
    );
    if ($isMaps) {
        return [
            'status' => 'maps',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent is a Google Maps listing, not a website.',
        ];
    }

    if (webstar_presence_host_matches($host, [
        'yelp.com', 'yellowpages.com', 'bbb.org', 'angi.com', 'homeadvisor.com',
    ])) {
        return [
            'status' => 'directory',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent is a directory page, not a website of your own.',
        ];
    }

    if (webstar_presence_is_builder($host)) {
        return [
            'status' => 'builder',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent is a site on a website builder.',
        ];
    }

    if (!$fetch) {
        return [
            'status' => 'site',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'The link you sent looks like a website of your own.',
        ];
    }

    $fetched = webstar_presence_fetch($url);
    if (!$fetched['ok']) {
        return [
            'status' => 'unreachable',
            'url' => $url,
            'title' => '',
            'https' => $scheme === 'https',
            'finding' => 'We could not open the website link you sent.',
        ];
    }

    $https = str_starts_with(strtolower($fetched['final_url']), 'https://');
    $titleBit = $fetched['title'] !== '' ? ' Page title: ' . $fetched['title'] . '.' : '';
    $secureBit = $https ? ' It loads over HTTPS.' : ' It loaded without HTTPS.';

    return [
        'status' => 'site',
        'url' => $url,
        'title' => $fetched['title'],
        'https' => $https,
        'finding' => 'The website link you sent loaded.' . $secureBit . $titleBit,
    ];
}

/**
 * @param list<string> $needles
 */
function webstar_presence_host_matches(string $host, array $needles): bool
{
    foreach ($needles as $needle) {
        if ($host === $needle || str_ends_with($host, '.' . $needle)) {
            return true;
        }
    }

    return false;
}

function webstar_presence_is_builder(string $host): bool
{
    if (webstar_presence_host_matches($host, [
        'wixsite.com', 'wix.com',
        'squarespace.com', 'sqsp.com',
        'godaddysites.com', 'godaddy.com',
        'square.site',
        'carrd.co',
        'weebly.com',
        'webflow.io',
        'myshopify.com',
        'sites.google.com',
        'blogspot.com', 'blogger.com',
    ])) {
        return true;
    }

    return $host === 'sites.google.com' || str_ends_with($host, '.sites.google.com');
}

function webstar_presence_host_is_private(string $host): bool
{
    if ($host === 'localhost' || $host === '0.0.0.0' || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
        return true;
    }

    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    $ips = @gethostbynamel($host);
    if (!is_array($ips) || $ips === []) {
        return false;
    }
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }
    }

    return false;
}

/**
 * @return array{ok: bool, final_url: string, title: string}
 */
function webstar_presence_fetch(string $url): array
{
    $fail = ['ok' => false, 'final_url' => $url, 'title' => ''];
    if (!function_exists('curl_init')) {
        return $fail;
    }

    $ch = curl_init($url);
    if ($ch === false) {
        return $fail;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_USERAGENT => 'WebstarPresenceCheck/1.0',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $finalUrl = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if (!is_string($body) || $status < 200 || $status >= 400) {
        return $fail;
    }

    $finalParts = parse_url($finalUrl);
    $finalHost = strtolower((string) ($finalParts['host'] ?? ''));
    if ($finalHost === '' || webstar_presence_host_is_private($finalHost)) {
        return $fail;
    }

    $snippet = substr($body, 0, 200000);
    $title = '';
    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $snippet, $match) === 1) {
        $title = trim(html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if (strlen($title) > 120) {
            $title = substr($title, 0, 117) . '...';
        }
    }

    return ['ok' => true, 'final_url' => $finalUrl, 'title' => $title];
}

/**
 * Plain-text sentence for the guest email.
 *
 * @param array{status: string, url: string, title: string, https: bool, finding: string} $audit
 */
function webstar_presence_finding_text(array $audit): string
{
    return (string) ($audit['finding'] ?? '');
}

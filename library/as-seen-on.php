<?php

declare(strict_types=1);

/**
 * As Seen On platform marks (inline SVG wordmarks).
 * URLs come from site-config; empty URL = mark only, no link.
 *
 * @return list<array{id: string, label: string, url_key: string, svg: string}>
 */
function webstar_as_seen_on_platforms(): array
{
    return [
        [
            'id' => 'yelp',
            'label' => 'Yelp',
            'url_key' => 'yelp_url',
            'svg' => <<<'SVG'
<svg class="as-seen-on__logo-svg" viewBox="0 0 120 36" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Yelp" focusable="false">
  <path fill="#FF1A1A" d="M18.2 8.1c.5-1.1 1.7-1.6 2.8-1.1l4.2 1.9c1.1.5 1.6 1.8 1.1 2.9l-2.4 5.3c-.4.9-1.4 1.3-2.3 1l-4.6-1.6c-1.2-.4-1.8-1.7-1.3-2.9l2.5-5.5z"/>
  <path fill="#FF1A1A" d="M9.4 16.6c-1.1-.4-1.6-1.7-1.1-2.8l2-4.4c.5-1.1 1.8-1.6 2.9-1.1l5.1 2.4c.9.4 1.3 1.4 1 2.3l-1.8 4.8c-.4 1.1-1.6 1.7-2.8 1.3l-5.3-2.5z"/>
  <path fill="#FF1A1A" d="M14.1 27.8c-.6 1.1-1.9 1.5-3 1l-4.3-2.1c-1.1-.5-1.5-1.8-1-2.9l2.6-5.2c.4-.9 1.5-1.3 2.4-1l4.5 1.8c1.2.5 1.7 1.8 1.2 2.9l-2.4 5.5z"/>
  <path fill="#FF1A1A" d="M27.6 22.4c1.2.2 2-1 1.8-2.1l-.9-4.8c-.2-1.2-1.3-1.9-2.4-1.7l-5.5 1.1c-.9.2-1.5 1.1-1.3 2l1.1 5.1c.2 1.2 1.4 1.9 2.5 1.7l4.7-1.3z"/>
  <path fill="#FF1A1A" d="M22.8 31.2c.9.8 2.3.5 2.9-.5l2.8-4.1c.6-1 .3-2.3-.7-2.9l-4.7-2.7c-.9-.5-2-.1-2.5.8l-2.4 4.6c-.6 1.1.1 2.4 1.2 2.9l3.4 1.9z"/>
  <text x="40" y="24" fill="#FF1A1A" font-family="Arial Black, Arial, Helvetica, sans-serif" font-size="18" font-weight="800" letter-spacing="-0.5">yelp</text>
</svg>
SVG,
        ],
        [
            'id' => 'google-maps',
            'label' => 'Google Maps',
            'url_key' => 'google_maps_url',
            'svg' => <<<'SVG'
<svg class="as-seen-on__logo-svg" viewBox="0 0 168 36" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Google Maps" focusable="false">
  <path fill="#EA4335" d="M14 3c-6.1 0-11 4.9-11 11 0 8.2 11 19 11 19s11-10.8 11-19c0-6.1-4.9-11-11-11zm0 15.1a4.1 4.1 0 1 1 0-8.2 4.1 4.1 0 0 1 0 8.2z"/>
  <text x="32" y="16" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700">
    <tspan fill="#4285F4">G</tspan><tspan fill="#EA4335">o</tspan><tspan fill="#FBBC05">o</tspan><tspan fill="#4285F4">g</tspan><tspan fill="#34A853">l</tspan><tspan fill="#EA4335">e</tspan>
  </text>
  <text x="84" y="16" fill="#5F6368" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="500">Maps</text>
  <text x="32" y="30" fill="#9AA0A6" font-family="Arial, Helvetica, sans-serif" font-size="8" letter-spacing="0.4">GOOGLE MAPS</text>
</svg>
SVG,
        ],
        [
            'id' => 'linkedin',
            'label' => 'LinkedIn',
            'url_key' => 'linkedin_url',
            'svg' => <<<'SVG'
<svg class="as-seen-on__logo-svg" viewBox="0 0 140 36" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="LinkedIn" focusable="false">
  <rect x="2" y="4" width="28" height="28" rx="4" fill="#0A66C2"/>
  <path fill="#fff" d="M8.2 14.2h4.3v13.1H8.2V14.2zm2.1-6.8c1.4 0 2.5 1.1 2.5 2.5S11.7 12.4 10.3 12.4 7.8 11.3 7.8 9.9s1.1-2.5 2.5-2.5zM15.6 14.2h4.1v1.8h.1c.6-1.1 2-2.2 4.1-2.2 4.4 0 5.2 2.9 5.2 6.6v7h-4.3v-6.2c0-1.5 0-3.4-2.1-3.4s-2.4 1.6-2.4 3.3v6.3h-4.3V14.2z"/>
  <text x="38" y="24" fill="#0A66C2" font-family="Arial, Helvetica, sans-serif" font-size="16" font-weight="700">LinkedIn</text>
</svg>
SVG,
        ],
        [
            'id' => 'facebook',
            'label' => 'Facebook',
            'url_key' => 'facebook_url',
            'svg' => <<<'SVG'
<svg class="as-seen-on__logo-svg" viewBox="0 0 150 36" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Facebook" focusable="false">
  <circle cx="16" cy="18" r="14" fill="#1877F2"/>
  <path fill="#fff" d="M18.8 18.8h3.1l.5-3.5h-3.6v-1.9c0-1 .5-2 2.1-2h1.6V8.4s-1.5-.3-2.9-.3c-2.9 0-4.8 1.8-4.8 5v2.2H11v3.5h3.3v8.7h4.5v-8.7z"/>
  <text x="36" y="24" fill="#1877F2" font-family="Arial, Helvetica, sans-serif" font-size="16" font-weight="700">Facebook</text>
</svg>
SVG,
        ],
        [
            'id' => 'instagram',
            'label' => 'Instagram',
            'url_key' => 'instagram_url',
            'svg' => <<<'SVG'
<svg class="as-seen-on__logo-svg" viewBox="0 0 156 36" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Instagram" focusable="false">
  <defs>
    <linearGradient id="webstar-ig-grad" x1="0%" y1="100%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#F58529"/>
      <stop offset="45%" stop-color="#DD2A7B"/>
      <stop offset="100%" stop-color="#515BD4"/>
    </linearGradient>
  </defs>
  <rect x="2" y="4" width="28" height="28" rx="7" fill="url(#webstar-ig-grad)"/>
  <rect x="9" y="11" width="14" height="14" rx="4" fill="none" stroke="#fff" stroke-width="2"/>
  <circle cx="16" cy="18" r="3.5" fill="none" stroke="#fff" stroke-width="2"/>
  <circle cx="21.5" cy="12.5" r="1.3" fill="#fff"/>
  <text x="38" y="24" fill="#E1306C" font-family="Arial, Helvetica, sans-serif" font-size="16" font-weight="700">Instagram</text>
</svg>
SVG,
        ],
    ];
}

/**
 * Resolve a profile URL from site-config (empty string when unset).
 */
function webstar_profile_url(string $urlKey): string
{
    $config = webstar_config();

    return trim((string) ($config[$urlKey] ?? ''));
}

/**
 * Best URL for writing a Google review (direct write link, then Maps listing).
 */
function webstar_google_review_destination(): string
{
    $config = webstar_config();
    $direct = trim((string) ($config['google_review_url'] ?? ''));
    if ($direct !== '') {
        return $direct;
    }

    return trim((string) ($config['google_maps_url'] ?? ''));
}

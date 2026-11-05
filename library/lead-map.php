<?php

declare(strict_types=1);

/**
 * Load corridor geometry + retained businesses for the admin map.
 * Mirrors lead-gen/scripts/lib/score.mjs rankWebsite / pain summary.
 */

const LEAD_MAP_COORDS_MAX_AGE_DAYS = 30;

const LEAD_MAP_SITE_FLAG_KEYS = [
    'no_website',
    'weak_url',
    'builder_subdomain',
    'redirects_to_social',
    'parked_domain',
    'site_error',
    'no_https',
    'powered_by_builder',
    'wp_default_theme',
    'placeholder_copy',
    'generic_title',
    'thin_one_page',
    'no_meta_description',
    'stale_copyright',
    'stale_last_modified',
    'free_email_on_site',
    'no_domain_email',
    'psi_perf_low',
    'psi_seo_low',
    'mobile_a11y_low',
];

function lead_map_root(): string
{
    return dirname(__DIR__) . '/lead-gen';
}

function lead_map_scoring(): array
{
    static $scoring = null;
    if ($scoring === null) {
        $path = lead_map_root() . '/config/scoring.json';
        $raw = is_file($path) ? file_get_contents($path) : false;
        $scoring = is_string($raw) ? (json_decode($raw, true) ?: []) : [];
    }

    return $scoring;
}

function lead_map_geography(): array
{
    static $geo = null;
    if ($geo === null) {
        $path = lead_map_root() . '/config/geography.json';
        $raw = is_file($path) ? file_get_contents($path) : false;
        $geo = is_string($raw) ? (json_decode($raw, true) ?: ['cells' => []]) : ['cells' => []];
    }

    return $geo;
}

/**
 * @return list<array<string, mixed>>
 */
function lead_map_enabled_corridors(): array
{
    $out = [];
    foreach (lead_map_geography()['cells'] ?? [] as $cell) {
        if (!is_array($cell) || (($cell['enabled'] ?? true) === false)) {
            continue;
        }
        $line = $cell['polyline'] ?? null;
        if (!is_array($line) || count($line) < 2) {
            continue;
        }
        $buffer = (float) ($cell['bufferMeters'] ?? 400);
        $out[] = [
            'id' => (string) ($cell['id'] ?? ''),
            'label' => (string) ($cell['label'] ?? ''),
            'streetHint' => (string) ($cell['streetHint'] ?? ''),
            'bufferMeters' => $buffer,
            'polyline' => $line,
            'bufferPolygon' => lead_map_buffer_polygon($line, $buffer),
        ];
    }

    return $out;
}

/**
 * Offset a lat/lng polyline by $bufferMeters on both sides → closed ring.
 *
 * @param list<array{0:float,1:float}> $line
 * @return list<array{0:float,1:float}>
 */
function lead_map_buffer_polygon(array $line, float $bufferMeters): array
{
    $n = count($line);
    if ($n < 2 || $bufferMeters <= 0) {
        return $line;
    }
    $refLat = (float) $line[(int) floor($n / 2)][0];
    $k = cos($refLat * M_PI / 180);
    $r = 6371000.0;
    $toXy = static function (array $p) use ($k, $r): array {
        return [$p[1] * M_PI / 180 * $r * $k, $p[0] * M_PI / 180 * $r];
    };
    $toLl = static function (array $xy) use ($k, $r): array {
        return [
            round($xy[1] / (M_PI / 180 * $r), 5),
            round($xy[0] / (M_PI / 180 * $r * $k), 5),
        ];
    };

    $left = [];
    $right = [];
    for ($i = 0; $i < $n; $i++) {
        $prev = $toXy($line[max(0, $i - 1)]);
        $cur = $toXy($line[$i]);
        $next = $toXy($line[min($n - 1, $i + 1)]);
        $dx = $next[0] - $prev[0];
        $dy = $next[1] - $prev[1];
        $len = hypot($dx, $dy);
        if ($len < 1e-6) {
            $nx = 0.0;
            $ny = 1.0;
        } else {
            $nx = -$dy / $len;
            $ny = $dx / $len;
        }
        $left[] = $toLl([$cur[0] + $nx * $bufferMeters, $cur[1] + $ny * $bufferMeters]);
        $right[] = $toLl([$cur[0] - $nx * $bufferMeters, $cur[1] - $ny * $bufferMeters]);
    }

    return array_merge($left, array_reverse($right));
}

function lead_map_parse_flags(?string $raw): array
{
    if ($raw === null || $raw === '') {
        return [];
    }
    $flags = json_decode($raw, true);

    return is_array($flags) ? $flags : [];
}

/**
 * @param array<string, mixed> $flags
 * @param array<string, mixed> $scoring
 * @return array{website_status:string,website_grade:string,website_need:string,website_score:int}
 */
function lead_map_rank_website(array $flags, array $scoring): array
{
    if (!empty($flags['no_website'])) {
        return ['website_status' => 'no_website', 'website_grade' => 'none', 'website_need' => 'high', 'website_score' => 0];
    }
    if (!empty($flags['weak_url']) || !empty($flags['redirects_to_social'])) {
        return ['website_status' => 'social_only', 'website_grade' => 'F', 'website_need' => 'high', 'website_score' => 10];
    }
    if (!empty($flags['parked_domain']) || !empty($flags['site_error'])) {
        return ['website_status' => 'broken_or_parked', 'website_grade' => 'F', 'website_need' => 'high', 'website_score' => 5];
    }
    if (!empty($flags['fetch_blocked'])) {
        return ['website_status' => 'could_not_fetch', 'website_grade' => '?', 'website_need' => 'unknown', 'website_score' => 50];
    }

    $weights = $scoring['weights'] ?? [];
    $penalty = 0;
    foreach (LEAD_MAP_SITE_FLAG_KEYS as $key) {
        if (!empty($flags[$key]) && isset($weights[$key])) {
            $penalty += (int) $weights[$key];
        }
    }
    $score = max(0, min(100, 100 - $penalty));
    $grade = 'A';
    if ($score < 35) {
        $grade = 'F';
    } elseif ($score < 50) {
        $grade = 'D';
    } elseif ($score < 65) {
        $grade = 'C';
    } elseif ($score < 80) {
        $grade = 'B';
    }
    $need = ($grade === 'F' || $grade === 'D') ? 'high' : ($grade === 'C' ? 'medium' : 'low');

    return [
        'website_status' => ($grade === 'A' || $grade === 'B') ? 'has_site' : 'weak_site',
        'website_grade' => $grade,
        'website_need' => $need,
        'website_score' => $score,
    ];
}

/**
 * @param array<string, mixed> $flags
 * @param array<string, mixed> $row
 */
function lead_map_pain_summary(array $flags, array $row, array $scoring): string
{
    $parts = [];
    foreach (($scoring['painLabels'] ?? []) as $key => $label) {
        if (!empty($flags[$key])) {
            $parts[] = (string) $label;
        }
    }
    if (isset($row['psi_performance']) && $row['psi_performance'] !== null && !empty($flags['psi_perf_low'])) {
        $parts[] = 'Mobile perf ' . (string) round((float) $row['psi_performance']);
    }

    return implode(' · ', array_slice($parts, 0, 4));
}

function lead_map_coords_fresh(?string $fetchedAt): bool
{
    if ($fetchedAt === null || $fetchedAt === '') {
        return false;
    }
    $ts = strtotime($fetchedAt);

    return $ts !== false && (time() - $ts) <= LEAD_MAP_COORDS_MAX_AGE_DAYS * 86400;
}

/**
 * @return array{leads: list<array<string, mixed>>, stale: list<array<string, mixed>>, error: ?string}
 */
function lead_map_load_businesses(): array
{
    $dbPath = lead_map_root() . '/data/leads.sqlite';
    if (!is_file($dbPath)) {
        return ['leads' => [], 'stale' => [], 'error' => 'No lead database yet. Run npm run lead-gen:corridors first.'];
    }
    try {
        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        return ['leads' => [], 'stale' => [], 'error' => 'Could not open lead database.'];
    }

    $ids = array_values(array_filter(array_map(static fn ($c) => $c['id'], lead_map_enabled_corridors())));
    if ($ids === []) {
        return ['leads' => [], 'stale' => [], 'error' => 'No enabled corridor in geography.json.'];
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT place_id, display_name, website_url, google_maps_uri, primary_type, geo_cell,
                lat, lng, location_fetched_at, distance_to_road_m, road_position_m,
                audit_flags, lead_score
         FROM leads
         WHERE exclude_reason IS NULL AND in_corridor = 1 AND geo_cell IN ($placeholders)"
    );
    $stmt->execute($ids);

    $scoring = lead_map_scoring();
    $leads = [];
    $stale = [];
    foreach ($stmt as $row) {
        $lat = isset($row['lat']) ? (float) $row['lat'] : null;
        $lng = isset($row['lng']) ? (float) $row['lng'] : null;
        $flags = lead_map_parse_flags($row['audit_flags'] ?? null);
        $site = lead_map_rank_website($flags, $scoring);
        $item = [
            'id' => (string) $row['place_id'],
            'name' => (string) ($row['display_name'] ?? ''),
            'lat' => $lat,
            'lng' => $lng,
            'cell' => (string) ($row['geo_cell'] ?? ''),
            'type' => (string) ($row['primary_type'] ?? ''),
            'website' => (string) ($row['website_url'] ?? ''),
            'maps' => (string) ($row['google_maps_uri'] ?? ''),
            'distance' => $row['distance_to_road_m'] !== null ? (int) $row['distance_to_road_m'] : null,
            'position' => $row['road_position_m'] !== null ? (int) $row['road_position_m'] : null,
            'pain' => lead_map_pain_summary($flags, $row, $scoring),
            'leadScore' => (int) ($row['lead_score'] ?? 0),
            'grade' => $site['website_grade'],
            'need' => $site['website_need'],
            'status' => $site['website_status'],
            'siteScore' => $site['website_score'],
        ];
        $fresh = $lat !== null && $lng !== null && lead_map_coords_fresh($row['location_fetched_at'] ?? null);
        if ($fresh) {
            $leads[] = $item;
        } else {
            $stale[] = $item;
        }
    }

    $needOrder = ['high' => 0, 'medium' => 1, 'unknown' => 2, 'low' => 3];
    $gradeOrder = ['none' => 0, 'F' => 1, 'D' => 2, 'C' => 3, '?' => 4, 'B' => 5, 'A' => 6];
    usort($leads, static function (array $a, array $b) use ($needOrder, $gradeOrder): int {
        $na = $needOrder[$a['need']] ?? 9;
        $nb = $needOrder[$b['need']] ?? 9;
        if ($na !== $nb) {
            return $na - $nb;
        }
        $ga = $gradeOrder[$a['grade']] ?? 9;
        $gb = $gradeOrder[$b['grade']] ?? 9;
        if ($ga !== $gb) {
            return $ga - $gb;
        }

        return ($a['position'] ?? PHP_INT_MAX) <=> ($b['position'] ?? PHP_INT_MAX);
    });

    return ['leads' => $leads, 'stale' => $stale, 'error' => null];
}

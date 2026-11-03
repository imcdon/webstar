<?php
/**
 * Quick local checks for SEO/speed markup (bypasses HTTP Basic by CLI include).
 * Usage: php scripts/verify-seo-local.php
 */
$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', dirname(__DIR__, 3));
$_SERVER['HTTP_HOST'] = 'superioriceadventures.com';
$_SERVER['SERVER_NAME'] = 'superioriceadventures.com';
$_SERVER['HTTPS'] = 'on';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';

ob_start();
include dirname(__DIR__) . '/index.php';
$html = ob_get_clean();

require_once dirname(__DIR__) . '/includes/schema.php';

$faqLd = json_encode(sia_schema_faq($faq_categories ?? []), JSON_UNESCAPED_SLASHES);
$svc = get_service('inland-splake-fishing');
$svcLd = $svc ? json_encode(sia_schema_service($svc), JSON_UNESCAPED_SLASHES) : '';
$crumbLd = json_encode(sia_schema_breadcrumbs([
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => 'Test', 'url' => absolute_url('about/')],
]), JSON_UNESCAPED_SLASHES);
$articleLd = json_encode(sia_schema_article([
    'title' => 'Sample',
    'blurb' => 'Blurb',
    'slug' => 'sample',
    'published_at' => '2026-01-01 12:00:00',
    'updated_at' => '2026-01-02 12:00:00',
    'thumbnail' => '/assets/img/hero/inland-splake-fishing.webp',
]), JSON_UNESCAPED_SLASHES);

$checks = [
    'JSON-LD LocalBusiness' => str_contains($html, '"LocalBusiness"'),
    'Single H1' => preg_match_all('/<h1\b/i', $html) === 1,
    'No Google Fonts CDN' => !str_contains($html, 'fonts.googleapis.com'),
    'Self-hosted fonts.css' => str_contains($html, 'assets/fonts/fonts.css'),
    'LCP fetchpriority img' => str_contains($html, 'fetchpriority="high"'),
    'Hero image preload' => str_contains($html, 'rel="preload"') && str_contains($html, 'as="image"'),
    'Lazy later slides' => str_contains($html, 'data-src='),
    'FAQ schema builder' => str_contains($faqLd, '"FAQPage"'),
    'Service schema builder' => str_contains($svcLd, '"Service"'),
    'Breadcrumb schema builder' => str_contains($crumbLd, '"BreadcrumbList"'),
    'Article schema builder' => str_contains($articleLd, '"Article"') && str_contains($articleLd, 'datePublished'),
];

$fail = 0;
foreach ($checks as $label => $ok) {
    echo ($ok ? '[OK]  ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$ok) {
        $fail++;
    }
}
exit($fail > 0 ? 1 : 0);

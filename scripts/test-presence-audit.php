<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/library/presence-audit.php';

$cases = [
    'https://facebook.com/shop' => 'social',
    'https://maps.app.goo.gl/abc' => 'maps',
    'https://mysite.wixsite.com/home' => 'builder',
    'https://example.com' => 'site',
    '' => 'none',
];

$failed = 0;
foreach ($cases as $url => $want) {
    $result = webstar_presence_audit($url, false);
    $ok = ($result['status'] ?? '') === $want;
    $label = $url === '' ? '(empty)' : $url;
    echo ($ok ? 'OK' : 'FAIL') . " {$label} => {$result['status']}\n";
    if (!$ok) {
        $failed++;
    }
}

exit($failed === 0 ? 0 : 1);

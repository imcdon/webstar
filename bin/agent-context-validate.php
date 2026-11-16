<?php

declare(strict_types=1);

/**
 * Compare live article slugs in articles-data.php to docs/agent/CORE.md list.
 * Exit 0 on match; 1 on mismatch or unreadable CORE.
 */
$root = dirname(__DIR__);
$corePath = $root . '/docs/agent/CORE.md';
$dataPath = $root . '/library/articles-data.php';

if (!is_readable($corePath) || !is_readable($dataPath)) {
    fwrite(STDERR, "Missing CORE.md or articles-data.php\n");
    exit(1);
}

/** @var list<array<string, mixed>> $articles */
$articles = require $dataPath;
$liveSlugs = [];
foreach ($articles as $row) {
    $slug = (string) ($row['slug'] ?? '');
    if ($slug !== '') {
        $liveSlugs[] = $slug;
    }
}
sort($liveSlugs);

$core = file_get_contents($corePath);
if ($core === false) {
    fwrite(STDERR, "Could not read CORE.md\n");
    exit(1);
}

preg_match_all('/^\d+\.\s+`([a-z0-9-]+)`/m', $core, $matches);
$coreSlugs = $matches[1] ?? [];
sort($coreSlugs);

$missingInCore = array_diff($liveSlugs, $coreSlugs);
$staleInCore = array_diff($coreSlugs, $liveSlugs);

if ($missingInCore === [] && $staleInCore === []) {
    echo 'OK: ' . count($liveSlugs) . " article slugs match CORE.md\n";
    exit(0);
}

if ($missingInCore !== []) {
    fwrite(STDERR, "In articles-data.php but not CORE.md:\n  - " . implode("\n  - ", $missingInCore) . "\n");
}
if ($staleInCore !== []) {
    fwrite(STDERR, "In CORE.md but not articles-data.php:\n  - " . implode("\n  - ", $staleInCore) . "\n");
}
exit(1);
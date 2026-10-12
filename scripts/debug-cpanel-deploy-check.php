<?php
declare(strict_types=1);

/**
 * Local/GitHub-side checks for cPanel deploy blockers.
 * Writes NDJSON to debug-c3e4be.log (session c3e4be).
 */

$logPath = dirname(__DIR__) . '/debug-c3e4be.log';
$sessionId = 'c3e4be';

$write = static function (string $hypothesisId, string $location, string $message, array $data) use ($logPath, $sessionId): void {
    $line = json_encode([
        'sessionId' => $sessionId,
        'id' => 'log_' . uniqid('', true),
        'timestamp' => (int) round(microtime(true) * 1000),
        'location' => $location,
        'message' => $message,
        'data' => $data,
        'runId' => 'cpanel-deploy-check',
        'hypothesisId' => $hypothesisId,
    ], JSON_UNESCAPED_SLASHES) . "\n";
    file_put_contents($logPath, $line, FILE_APPEND);
};

$root = dirname(__DIR__);
chdir($root);

// #region agent log
$status = trim((string) shell_exec('git status --porcelain 2>&1'));
$write('A', 'debug-cpanel-deploy-check.php:local-status', 'Local working tree porcelain', [
    'clean' => $status === '',
    'porcelain' => $status === '' ? '(empty)' : $status,
]);
// #endregion

// #region agent log
$ymlPath = $root . '/.cpanel.yml';
$ymlExists = is_file($ymlPath);
$ymlTracked = trim((string) shell_exec('git ls-files --error-unmatch .cpanel.yml 2>&1'));
$ymlRaw = $ymlExists ? (string) file_get_contents($ymlPath) : '';
$hasDeployment = str_contains($ymlRaw, 'deployment:');
$hasTasks = str_contains($ymlRaw, 'tasks:');
$hasDocStart = str_starts_with(ltrim($ymlRaw), '---');
$write('C', 'debug-cpanel-deploy-check.php:yml-local', 'Local .cpanel.yml validity shape', [
    'exists' => $ymlExists,
    'trackedOk' => str_contains($ymlTracked, '.cpanel.yml') && !str_contains(strtolower($ymlTracked), 'error'),
    'hasDocStart' => $hasDocStart,
    'hasDeployment' => $hasDeployment,
    'hasTasks' => $hasTasks,
    'bytes' => strlen($ymlRaw),
]);
// #endregion

// #region agent log
$remoteHead = trim((string) shell_exec('git rev-parse origin/main 2>&1'));
$localHead = trim((string) shell_exec('git rev-parse HEAD 2>&1'));
$remoteYml = trim((string) shell_exec('git cat-file -e origin/main:.cpanel.yml 2>&1; echo EXIT:$?'));
$write('B', 'debug-cpanel-deploy-check.php:remote', 'origin/main vs HEAD and remote .cpanel.yml blob', [
    'localHead' => $localHead,
    'remoteHead' => $remoteHead,
    'headsMatch' => $localHead === $remoteHead,
    'remoteYmlProbe' => $remoteYml,
]);
// #endregion

// #region agent log
$ignoredMail = trim((string) shell_exec('git check-ignore -v library/mail-config.php 2>&1'));
$mailInRepo = is_file($root . '/library/mail-config.php');
$write('A', 'debug-cpanel-deploy-check.php:mail-config', 'mail-config presence can dirty cPanel clone if copied into clone path', [
    'localMailConfigExists' => $mailInRepo,
    'checkIgnore' => $ignoredMail === '' ? '(not ignored / missing)' : $ignoredMail,
    'note' => 'If mail-config.php sits inside the cPanel Git clone (not only document root), git status is dirty and Deploy is disabled.',
]);
// #endregion

echo "Wrote checks to debug-c3e4be.log\n";

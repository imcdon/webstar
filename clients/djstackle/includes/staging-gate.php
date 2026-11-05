<?php

declare(strict_types=1);

/**
 * HTTP Basic Auth gate for Webstar staging previews.
 * Active on *.webstarbusinessservices.com only — not on localhost or djstackle.com.
 */

function djt_staging_is_local_dev_host(string $host): bool
{
    if ($host === '') {
        return false;
    }
    if ($host === 'localhost' || str_starts_with($host, 'localhost:')) {
        return true;
    }
    if (str_starts_with($host, '127.0.0.1')) {
        return true;
    }
    if (str_ends_with($host, '.local') || str_contains($host, '.local:')) {
        return true;
    }

    return false;
}

function djt_staging_should_gate(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));

    if ($host !== '' && str_contains($host, 'djstackle.com')) {
        return false;
    }

    if (djt_staging_is_local_dev_host($host)) {
        return false;
    }

    return str_contains($host, 'webstarbusinessservices.com');
}

/**
 * @return array<string, array<string, mixed>>
 */
function djt_staging_clients(): array
{
    static $clients = null;
    if ($clients !== null) {
        return $clients;
    }

    $candidates = [
        __DIR__ . '/staging-users.php',
        dirname(__DIR__, 3) . '/library/portal-clients.php',
    ];

    foreach ($candidates as $path) {
        if (is_file($path)) {
            $data = require $path;
            if (is_array($data)) {
                $clients = $data;
                return $clients;
            }
        }
    }

    $clients = [];
    return $clients;
}

/**
 * @param array<string, array<string, mixed>> $entry
 */
function djt_staging_user_allowed(string $username, array $entry): bool
{
    if (($entry['role'] ?? 'client') === 'admin') {
        return true;
    }

    $projects = $entry['projects'] ?? [];
    if (!is_array($projects)) {
        return false;
    }

    foreach ($projects as $project) {
        if (!is_array($project)) {
            continue;
        }
        if (($project['slug'] ?? '') === 'djstackle') {
            return true;
        }
    }

    return false;
}

function djt_staging_credentials(): array
{
    $user = (string) ($_SERVER['PHP_AUTH_USER'] ?? '');
    $pass = (string) ($_SERVER['PHP_AUTH_PW'] ?? '');
    if ($user !== '') {
        return [$user, $pass];
    }

    $header = (string) (
        $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? ''
    );
    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (strcasecmp((string) $name, 'Authorization') === 0) {
                    $header = (string) $value;
                    break;
                }
            }
        }
    }

    if (preg_match('/^\s*Basic\s+(\S+)/i', $header, $match) === 1) {
        $decoded = base64_decode($match[1], true);
        if (is_string($decoded) && str_contains($decoded, ':')) {
            return explode(':', $decoded, 2);
        }
    }

    return ['', ''];
}

function djt_staging_require_auth(): void
{
    if (!djt_staging_should_gate()) {
        return;
    }

    [$rawUser, $password] = djt_staging_credentials();
    $username = strtolower(trim($rawUser));
    $clients = djt_staging_clients();

    if ($username !== '' && isset($clients[$username])) {
        $entry = $clients[$username];
        $hash = (string) ($entry['password_hash'] ?? '');
        if (
            $hash !== ''
            && password_verify($password, $hash)
            && djt_staging_user_allowed($username, $entry)
        ) {
            return;
        }
    }

    header('WWW-Authenticate: Basic realm="DJ\'s Tackle — Staging (Webstar)", charset="UTF-8"');
    http_response_code(401);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Authentication required.\nUse your Webstar Client Portal username and password.";
    exit;
}

djt_staging_require_auth();

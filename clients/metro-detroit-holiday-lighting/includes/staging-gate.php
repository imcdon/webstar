<?php

declare(strict_types=1);

/**
 * HTTP Basic Auth gate for Webstar staging previews.
 * Active on *.webstarbusinessservices.com — not on localhost or metrodetroitholidaylighting.com.
 */

function mdhl_staging_is_local_dev_host(string $host): bool
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

function mdhl_staging_should_gate(): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));

    if ($host !== '' && str_contains($host, 'metrodetroitholidaylighting.com')) {
        return false;
    }

    if (mdhl_staging_is_local_dev_host($host)) {
        return false;
    }

    return str_contains($host, 'webstarbusinessservices.com');
}

/**
 * @return array<string, array<string, mixed>>
 */
function mdhl_staging_clients(): array
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
function mdhl_staging_user_allowed(string $username, array $entry): bool
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
        if (($project['slug'] ?? '') === 'metro-detroit-holiday-lighting') {
            return true;
        }
    }

    return false;
}

function mdhl_staging_credentials(): array
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
            [$user, $pass] = explode(':', $decoded, 2);

            return [$user, $pass];
        }
    }

    return ['', ''];
}

function mdhl_staging_require_auth(): void
{
    if (!mdhl_staging_should_gate()) {
        return;
    }

    [$rawUser, $password] = mdhl_staging_credentials();
    $username = strtolower(trim($rawUser));
    $clients = mdhl_staging_clients();

    if ($username !== '' && isset($clients[$username])) {
        $entry = $clients[$username];
        $hash = (string) ($entry['password_hash'] ?? '');
        if (
            $hash !== ''
            && password_verify($password, $hash)
            && mdhl_staging_user_allowed($username, $entry)
        ) {
            return;
        }
    }

    header('WWW-Authenticate: Basic realm="Metro Detroit Holiday Lighting — Staging (Webstar)", charset="UTF-8"');
    http_response_code(401);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Authentication required.\nUse your Webstar Client Portal username and password.";
    exit;
}

mdhl_staging_require_auth();

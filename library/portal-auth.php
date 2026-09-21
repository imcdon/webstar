<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

/**
 * @return array<string, array<string, mixed>>
 */
function portal_clients(): array
{
    static $clients = null;
    if ($clients === null) {
        $path = __DIR__ . '/portal-clients.php';
        $clients = is_file($path) ? require $path : [];
    }

    return $clients;
}

function portal_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function portal_attempt_login(string $username, string $password): bool
{
    portal_start_session();
    $username = strtolower(trim($username));
    $clients = portal_clients();
    if ($username === '' || !isset($clients[$username])) {
        return false;
    }
    $hash = (string) ($clients[$username]['password_hash'] ?? '');
    if ($hash === '' || !password_verify($password, $hash)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['portal_user'] = $username;

    return true;
}

function portal_logout(): void
{
    portal_start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function portal_current_username(): ?string
{
    portal_start_session();
    $user = $_SESSION['portal_user'] ?? null;

    return is_string($user) && $user !== '' ? $user : null;
}

/**
 * @return array<string, mixed>|null
 */
function portal_current_client(): ?array
{
    $user = portal_current_username();
    if ($user === null) {
        return null;
    }
    $clients = portal_clients();

    return $clients[$user] ?? null;
}

function portal_is_admin(): bool
{
    $client = portal_current_client();

    return $client !== null && (($client['role'] ?? 'client') === 'admin');
}

/**
 * @return list<array<string, mixed>>
 */
function portal_client_projects(): array
{
    $client = portal_current_client();
    if ($client === null) {
        return [];
    }

    if (portal_is_admin()) {
        $all = [];
        foreach (portal_clients() as $username => $entry) {
            if (($entry['role'] ?? 'client') === 'admin') {
                continue;
            }
            $projects = $entry['projects'] ?? [];
            if (!is_array($projects)) {
                continue;
            }
            foreach ($projects as $project) {
                if (!is_array($project)) {
                    continue;
                }
                $project['client_username'] = $username;
                $project['client_name'] = (string) ($entry['display_name'] ?? $username);
                $all[] = $project;
            }
        }

        return $all;
    }

    $projects = $client['projects'] ?? [];
    if (!is_array($projects)) {
        return [];
    }

    $displayName = (string) ($client['display_name'] ?? '');
    $username = portal_current_username() ?? '';
    $out = [];
    foreach ($projects as $project) {
        if (!is_array($project)) {
            continue;
        }
        $project['client_username'] = $username;
        $project['client_name'] = $displayName;
        $out[] = $project;
    }

    return $out;
}

function portal_require_login(): void
{
    if (portal_current_username() === null) {
        header('Location: ' . webstar_url('portal/login.php'), true, 303);
        exit;
    }
}

function portal_project_preview_url(array $project): string
{
    // #region agent log
    $__dbg = static function (string $hypothesisId, string $message, array $data = []): void {
        $line = json_encode([
            'sessionId' => 'c3e4be',
            'runId' => 'pre-fix',
            'hypothesisId' => $hypothesisId,
            'location' => 'library/portal-auth.php:portal_project_preview_url',
            'message' => $message,
            'data' => $data,
            'timestamp' => (int) round(microtime(true) * 1000),
        ], JSON_UNESCAPED_SLASHES);
        if (is_string($line)) {
            @file_put_contents(dirname(__DIR__) . '/debug-c3e4be.log', $line . "\n", FILE_APPEND);
        }
    };
    // #endregion

    if (webstar_is_local_host()) {
        $url = webstar_url((string) ($project['local_path'] ?? ''));
        // #region agent log
        $__dbg('C', 'preview_url_local', ['host' => (string) ($_SERVER['HTTP_HOST'] ?? ''), 'url' => $url, 'slug' => (string) ($project['slug'] ?? '')]);
        // #endregion
        return $url;
    }

    $staging = trim((string) ($project['staging_url'] ?? ''));
    if ($staging !== '') {
        // #region agent log
        $__dbg('C', 'preview_url_staging', ['host' => (string) ($_SERVER['HTTP_HOST'] ?? ''), 'url' => $staging, 'slug' => (string) ($project['slug'] ?? '')]);
        // #endregion
        return $staging;
    }

    $url = webstar_url((string) ($project['local_path'] ?? ''));
    // #region agent log
    $__dbg('C', 'preview_url_fallback_local_path', ['url' => $url]);
    // #endregion
    return $url;
}

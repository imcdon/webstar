<?php

declare(strict_types=1);

if (!function_exists('url')) {
    $doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $project_root = str_replace('\\', '/', dirname(__DIR__));
    $base_path = '';

    if ($doc_root !== '' && str_starts_with($project_root, $doc_root)) {
        $relative = substr($project_root, strlen($doc_root));
        $base_path = '/' . trim($relative, '/');
        if ($base_path === '/') {
            $base_path = '';
        }
    }

    function url(string $path = ''): string
    {
        global $base_path;
        $path = ltrim($path, '/');
        if ($path === '') {
            return $base_path === '' ? '/' : $base_path . '/';
        }
        return $base_path . '/' . $path;
    }

    function absolute_url(string $path = ''): string
    {
        global $site_url;
        $origin = rtrim($site_url ?? 'https://djstackle.com', '/');
        $path = ltrim($path, '/');
        if ($path === '') {
            return $origin . '/';
        }
        // Keep query strings / .php files as-is; add trailing slash for clean directories.
        if (str_contains($path, '?') || str_contains($path, '.php') || str_contains($path, '.')) {
            return $origin . '/' . $path;
        }
        return $origin . '/' . rtrim($path, '/') . '/';
    }

    function current_path(): string
    {
        global $base_path;
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        if ($base_path !== '' && str_starts_with($path, $base_path)) {
            $path = substr($path, strlen($base_path)) ?: '/';
        }
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
        return $path;
    }
}

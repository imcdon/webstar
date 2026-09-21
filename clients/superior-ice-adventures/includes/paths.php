<?php
/*
 * paths.php - Base path detection + URL helpers.
 * Local XAMPP may live under /superior-ice-adventures/; production is the domain root.
 */
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

    /** Absolute URL on the live domain (https://superioriceadventures.com/...). */
    function absolute_url(string $path = ''): string
    {
        global $site_url;
        $origin = rtrim($site_url ?? 'https://superioriceadventures.com', '/');
        $path = trim($path, '/');
        if ($path === '') {
            return $origin . '/';
        }
        return $origin . '/' . $path . (str_ends_with($path, '.php') || str_contains($path, '.') ? '' : '/');
    }

    /** Current page path for canonical tags (path + query, relative to site root). */
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

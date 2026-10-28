<?php

declare(strict_types=1);

/**
 * One-time localhost sign-in. Saves a refresh token into library/calendar-config.php.
 * Not for the public site: a browser request gets a 404.
 *
 *   C:\xampp\php\php.exe scripts/google-calendar-auth.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$configPath = dirname(__DIR__) . '/library/calendar-config.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "Copy library/calendar-config.example.php to library/calendar-config.php and add the OAuth client id and secret first.\n");
    exit(1);
}

/** @var mixed $config */
$config = require $configPath;
if (!is_array($config)) {
    fwrite(STDERR, "calendar-config.php must return an array.\n");
    exit(1);
}

$clientId = trim((string) ($config['client_id'] ?? ''));
$clientSecret = trim((string) ($config['client_secret'] ?? ''));
if ($clientId === '' || $clientSecret === '' || str_contains($clientId, 'your-client-id') || $clientSecret === 'your-client-secret') {
    fwrite(STDERR, "Put the Google OAuth client id and secret in library/calendar-config.php first.\n");
    exit(1);
}

$redirectUri = 'http://127.0.0.1:8765/';
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => implode(' ', [
        'https://www.googleapis.com/auth/calendar.events',
        'https://www.googleapis.com/auth/calendar.freebusy',
    ]),
    'access_type' => 'offline',
    'prompt' => 'consent',
    'login_hint' => 'ian@webstarbusinessservices.com',
]);

$errno = 0;
$errstr = '';
$server = stream_socket_server('tcp://127.0.0.1:8765', $errno, $errstr);
if ($server === false) {
    fwrite(STDERR, "Could not listen on 127.0.0.1:8765 ({$errstr}). Close whatever is using that port and run this again.\n");
    exit(1);
}

echo "Opening Google sign-in. Approve access as ian@webstarbusinessservices.com.\n";
echo "If the browser does not open, visit:\n{$authUrl}\n\n";
if (PHP_OS_FAMILY === 'Windows') {
    pclose(popen('cmd /c start "" ' . escapeshellarg($authUrl), 'r'));
}

$conn = stream_socket_accept($server, 180);
if ($conn === false) {
    fwrite(STDERR, "Timed out waiting for Google to redirect back.\n");
    exit(1);
}

$request = '';
while (!str_contains($request, "\r\n\r\n") && strlen($request) < 8192) {
    $chunk = fread($conn, 1024);
    if ($chunk === false || $chunk === '') {
        break;
    }
    $request .= $chunk;
}

$code = '';
$error = '';
if (preg_match('/^GET\s+\/\?(\S+)/', $request, $match) === 1) {
    parse_str($match[1], $query);
    $code = trim((string) ($query['code'] ?? ''));
    $error = trim((string) ($query['error'] ?? ''));
}

$html = '<!DOCTYPE html><html><body><p>Webstar calendar sign-in is finished. You can close this tab.</p></body></html>';
if ($code === '') {
    $html = '<!DOCTYPE html><html><body><p>Sign-in did not return a code. Close this tab and check the terminal.</p></body></html>';
}
$response = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Length: " . strlen($html) . "\r\nConnection: close\r\n\r\n" . $html;
fwrite($conn, $response);
fclose($conn);
fclose($server);

if ($error !== '' || $code === '') {
    fwrite(STDERR, $error !== '' ? "Google returned: {$error}\n" : "No authorization code in the redirect.\n");
    exit(1);
}

$tokenRequest = http_build_query([
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code',
]);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $tokenRequest,
        'timeout' => 20,
        'ignore_errors' => true,
    ],
]);
$raw = file_get_contents('https://oauth2.googleapis.com/token', false, $context);
$decoded = is_string($raw) ? json_decode($raw, true) : null;
$refresh = is_array($decoded) ? trim((string) ($decoded['refresh_token'] ?? '')) : '';
if ($refresh === '') {
    $message = is_array($decoded) ? (string) ($decoded['error_description'] ?? $decoded['error'] ?? 'no refresh token') : 'no refresh token';
    fwrite(STDERR, "Token exchange failed: {$message}\n");
    exit(1);
}

$contents = (string) file_get_contents($configPath);
$updated = preg_replace(
    "/'refresh_token'\\s*=>\\s*(?:'(?:\\\\'|[^'])*'|\"(?:\\\\\"|[^\"])*\")/",
    "'refresh_token' => " . var_export($refresh, true),
    $contents,
    1,
    $count
);
if (!is_string($updated) || $count !== 1) {
    fwrite(STDERR, "Signed in, but calendar-config.php has no refresh_token line to update.\n");
    exit(1);
}
file_put_contents($configPath, $updated);
echo "Refresh token saved in library/calendar-config.php. The contact page can book consultations now.\n";

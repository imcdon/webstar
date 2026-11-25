<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . webstar_url('intake.php'), true, 303);
    exit;
}

$redirect = trim((string) ($_POST['redirect'] ?? ''));
if ($redirect === '' || str_contains($redirect, '://') || str_starts_with($redirect, '//')) {
    $redirect = webstar_url('intake.php');
}

function intake_redirect(string $target, string $status, ?string $reason = null, ?string $fields = null): void
{
    $params = ['intake_status' => $status];
    if ($reason) {
        $params['intake_reason'] = $reason;
    }
    if ($fields) {
        $params['intake_fields'] = $fields;
    }
    $sep = str_contains($target, '?') ? '&' : '?';
    header('Location: ' . $target . $sep . http_build_query($params) . '#intake', true, 303);
    exit;
}

if (!empty(trim((string) ($_POST['company_url'] ?? '')))) {
    intake_redirect($redirect, 'error', 'generic');
}

$phonePattern = '/^[\d\s().+\-]{7,30}$/';

$businessName = trim((string) ($_POST['business_name'] ?? ''));
$businessEmail = trim((string) ($_POST['business_email'] ?? ''));
$businessPhone = trim((string) ($_POST['business_phone'] ?? ''));
$contactPhone = trim((string) ($_POST['contact_phone'] ?? ''));
$businessAddress = trim((string) ($_POST['business_address'] ?? ''));
$mailingAddress = trim((string) ($_POST['mailing_address'] ?? ''));
$sellServices = trim((string) ($_POST['sell_services'] ?? ''));
$currentSystems = trim((string) ($_POST['current_systems'] ?? ''));
$openToChange = trim((string) ($_POST['open_to_change'] ?? ''));
$goals = trim((string) ($_POST['goals'] ?? ''));

$servicesRaw = $_POST['services'] ?? [];
$services = [];
if (is_array($servicesRaw)) {
    foreach ($servicesRaw as $item) {
        $item = trim((string) $item);
        if ($item !== '') {
            $services[] = $item;
        }
    }
}
$services = array_values(array_unique($services));

$tiersRaw = $_POST['website_tiers'] ?? [];
$websiteTiers = [];
if (is_array($tiersRaw)) {
    foreach ($tiersRaw as $item) {
        $item = trim((string) $item);
        if ($item !== '') {
            $websiteTiers[] = $item;
        }
    }
}
$websiteTiers = array_values(array_unique($websiteTiers));

$allowedServices = [
    'website',
    'google-business-profile',
    'apple-maps',
    'chatgpt-business-profile',
    'yelp',
    'photography',
    'social-media',
    'ai-agents',
    'business-email',
    'google-analytics',
];
$allowedTiers = [
    'simple-website',
    'business-website',
    'business-growth-website',
    'hosting',
];

$services = array_values(array_intersect($services, $allowedServices));
$websiteTiers = array_values(array_intersect($websiteTiers, $allowedTiers));

$serviceLabels = [
    'website' => 'Website',
    'google-business-profile' => 'Google Business Profile (Google Maps)',
    'apple-maps' => 'Apple Maps',
    'chatgpt-business-profile' => 'ChatGPT Business Profile',
    'yelp' => 'Yelp',
    'photography' => 'Photography',
    'social-media' => 'Social Media',
    'ai-agents' => 'AI Agents',
    'business-email' => 'Business Email Connection',
    'google-analytics' => 'Google Analytics',
];
$tierLabels = [
    'simple-website' => 'Simple Website',
    'business-website' => 'Business Website',
    'business-growth-website' => 'Business Growth Website',
    'hosting' => 'Hosting',
];

$missing = [];
if ($businessName === '' || strlen($businessName) > 160) {
    $missing[] = 'business_name';
}
if ($businessEmail === '' || !filter_var($businessEmail, FILTER_VALIDATE_EMAIL) || strlen($businessEmail) > 254) {
    $missing[] = 'business_email';
}
if ($businessPhone === '' || !preg_match($phonePattern, $businessPhone)) {
    $missing[] = 'business_phone';
}
if ($contactPhone === '' || !preg_match($phonePattern, $contactPhone)) {
    $missing[] = 'contact_phone';
}
if ($businessAddress === '' || strlen($businessAddress) > 1000) {
    $missing[] = 'business_address';
}
if ($mailingAddress === '' || strlen($mailingAddress) > 1000) {
    $missing[] = 'mailing_address';
}
if (strlen($sellServices) > 4000) {
    $missing[] = 'sell_services';
}
if (strlen($currentSystems) > 2000) {
    $missing[] = 'current_systems';
}
if ($openToChange !== '' && $openToChange !== 'yes' && $openToChange !== 'no') {
    $missing[] = 'open_to_change';
}
if (strlen($goals) > 4000) {
    $missing[] = 'goals';
}
if ($services === [] && $websiteTiers === []) {
    $missing[] = 'services';
}
if (in_array('website', $services, true) && $websiteTiers === []) {
    // Website checked without a tier is still allowed; tiers alone imply website interest
}

if ($missing !== []) {
    intake_redirect($redirect, 'error', 'validation', implode(',', array_unique($missing)));
}

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoload)) {
    intake_redirect($redirect, 'error', 'config');
}
require $autoload;

$configPath = __DIR__ . '/mail-config.php';
if (!is_file($configPath)) {
    intake_redirect($redirect, 'error', 'config');
}
$config = require $configPath;
foreach (['smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'mail_from', 'mail_from_name', 'mail_to'] as $key) {
    if (!isset($config[$key]) || trim((string) $config[$key]) === '') {
        intake_redirect($redirect, 'error', 'config');
    }
}
$placeholders = ['smtp.example.com', 'your-smtp-user@example.com', 'your-app-password', 'your-email@domain.com'];
foreach (['smtp_host', 'smtp_user', 'smtp_pass', 'mail_to'] as $key) {
    if (in_array(trim((string) $config[$key]), $placeholders, true)) {
        intake_redirect($redirect, 'error', 'config');
    }
}

$selectedServiceLines = [];
foreach ($services as $slug) {
    $selectedServiceLines[] = '- ' . ($serviceLabels[$slug] ?? $slug);
}
foreach ($websiteTiers as $slug) {
    $selectedServiceLines[] = '  · ' . ($tierLabels[$slug] ?? $slug);
}
if ($websiteTiers !== [] && !in_array('website', $services, true)) {
    array_unshift($selectedServiceLines, '- Website (tier selected)');
}

$body = implode("\n", [
    'New project intake',
    '----------------------------------------',
    'Business name: ' . $businessName,
    'Business email: ' . $businessEmail,
    'Business phone: ' . $businessPhone,
    'Contact phone: ' . $contactPhone,
    '',
    'Business address:',
    $businessAddress,
    '',
    'Mailing address:',
    $mailingAddress,
    '',
    'What they sell / services they provide:',
    $sellServices !== '' ? $sellServices : '(not provided)',
    '',
    'Current systems:',
    $currentSystems !== '' ? $currentSystems : '(not provided)',
    '',
    'Open to changing booking/payment/inventory software: ' . ($openToChange === 'yes' ? 'Yes' : ($openToChange === 'no' ? 'No' : '(not provided)')),
    '',
    'Goals:',
    $goals !== '' ? $goals : '(not provided)',
    '',
    'Services requested:',
    $selectedServiceLines !== [] ? implode("\n", $selectedServiceLines) : '(none)',
    '',
    'Submitted: ' . (new DateTimeImmutable('now'))->format('Y-m-d H:i:s T'),
    'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
]);

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = (string) $config['smtp_host'];
    $mail->Port = (int) $config['smtp_port'];
    $mail->SMTPAuth = true;
    $mail->Username = (string) $config['smtp_user'];
    $mail->Password = (string) $config['smtp_pass'];
    $secure = strtolower((string) $config['smtp_secure']);
    if ($secure === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($secure === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;
    }
    $mail->setFrom((string) $config['mail_from'], (string) $config['mail_from_name']);
    $mail->addAddress((string) $config['mail_to']);
    $mail->addReplyTo($businessEmail, $businessName);
    $mail->Subject = 'Project intake — ' . $businessName;
    $mail->Body = $body;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->send();
    intake_redirect($redirect, 'success');
} catch (MailerException $e) {
    intake_redirect($redirect, 'error', 'send');
}

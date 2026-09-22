<?php
declare(strict_types=1);
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . webstar_url('packages.php'), true, 303);
    exit;
}

$redirect = trim((string) ($_POST['redirect'] ?? ''));
if ($redirect === '' || str_contains($redirect, '://') || str_starts_with($redirect, '//')) {
    $redirect = webstar_url('packages.php');
}

function intake_redirect(string $target, string $status, ?string $reason = null, ?string $fields = null, string $packageSlug = ''): void
{
    $params = ['intake_status' => $status];
    if ($reason) { $params['intake_reason'] = $reason; }
    if ($fields) { $params['intake_fields'] = $fields; }
    $sep = str_contains($target, '?') ? '&' : '?';
    $hash = '#intake';
    header('Location: ' . $target . $sep . http_build_query($params) . $hash, true, 303);
    exit;
}

$packageSlug = trim((string) ($_POST['package_slug'] ?? ''));
$package = webstar_package_by_slug($packageSlug);
if ($package === null) {
    intake_redirect($redirect, 'error', 'generic', null, $packageSlug);
}

if (!empty(trim((string) ($_POST['website'] ?? '')))) {
    intake_redirect($redirect, 'error', 'generic', null, $packageSlug);
}

$company = trim((string) ($_POST['company_name'] ?? ''));
$contactName = trim((string) ($_POST['contact_name'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$logoUrl = trim((string) ($_POST['logo_url'] ?? ''));
$summary = trim((string) ($_POST['business_summary'] ?? ''));
$desired = trim((string) ($_POST['desired_services'] ?? ''));

$missing = [];
if ($company === '' || strlen($company) > 160) { $missing[] = 'company_name'; }
if ($contactName === '' || strlen($contactName) > 120) { $missing[] = 'contact_name'; }
if ($phone === '' || !preg_match('/^[\d\s().+\-]{7,30}$/', $phone)) { $missing[] = 'phone'; }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $missing[] = 'email'; }
if ($summary === '' || strlen($summary) > 4000) { $missing[] = 'business_summary'; }
if ($desired === '' || strlen($desired) > 2000) { $missing[] = 'desired_services'; }
if ($logoUrl !== '' && !filter_var($logoUrl, FILTER_VALIDATE_URL)) { $missing[] = 'logo'; }

$upload = $_FILES['logo_file'] ?? null;
$hasUpload = is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
if ($hasUpload) {
    if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) { $missing[] = 'logo'; }
    elseif (($upload['size'] ?? 0) > 5 * 1024 * 1024) { $missing[] = 'logo'; }
}

if ($missing !== []) {
    intake_redirect($redirect, 'error', 'validation', implode(',', array_unique($missing)), $packageSlug);
}

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoload)) { intake_redirect($redirect, 'error', 'config', null, $packageSlug); }
require $autoload;

$configPath = __DIR__ . '/mail-config.php';
if (!is_file($configPath)) { intake_redirect($redirect, 'error', 'config', null, $packageSlug); }
$config = require $configPath;
foreach (['smtp_host','smtp_port','smtp_secure','smtp_user','smtp_pass','mail_from','mail_from_name','mail_to'] as $key) {
    if (!isset($config[$key]) || trim((string) $config[$key]) === '') { intake_redirect($redirect, 'error', 'config', null, $packageSlug); }
}
$placeholders = ['smtp.example.com', 'your-smtp-user@example.com', 'your-app-password', 'your-email@domain.com'];
foreach (['smtp_host','smtp_user','smtp_pass','mail_to'] as $key) {
    if (in_array(trim((string) $config[$key]), $placeholders, true)) { intake_redirect($redirect, 'error', 'config', null, $packageSlug); }
}

$body = implode("\n", [
    'New package intake — ' . ($package['title'] ?? $packageSlug),
    '----------------------------------------',
    'Package: ' . ($package['title'] ?? $packageSlug),
    'Company: ' . $company,
    'Contact: ' . $contactName,
    'Phone: ' . $phone,
    'Email: ' . $email,
    'Logo URL: ' . ($logoUrl !== '' ? $logoUrl : '(none)'),
    '',
    'Business summary:',
    $summary,
    '',
    'Desired services:',
    $desired,
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
    if ($secure === 'ssl') { $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; }
    elseif ($secure === 'tls') { $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; }
    else { $mail->SMTPSecure = ''; $mail->SMTPAutoTLS = false; }
    $mail->setFrom((string) $config['mail_from'], (string) $config['mail_from_name']);
    $mail->addAddress((string) $config['mail_to']);
    $mail->addReplyTo($email, $contactName);
    if ($hasUpload && is_uploaded_file((string) $upload['tmp_name'])) {
        $mail->addAttachment((string) $upload['tmp_name'], (string) ($upload['name'] ?? 'logo-upload'));
    }
    $mail->Subject = 'Package intake — ' . $company . ' (' . ($package['title'] ?? $packageSlug) . ')';
    $mail->Body = $body;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->send();
    intake_redirect($redirect, 'success', null, null, $packageSlug);
} catch (MailerException $e) {
    intake_redirect($redirect, 'error', 'send', null, $packageSlug);
}

<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/helpers.php';
require __DIR__ . '/calendar.php';
require __DIR__ . '/presence-audit.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . webstar_url('contact.php') . '#book', true, 303);
    exit;
}

function book_redirect(string $status, ?string $reason = null, ?string $fields = null): void
{
    $params = ['book_status' => $status];
    if ($reason) {
        $params['book_reason'] = $reason;
    }
    if ($fields) {
        $params['book_fields'] = $fields;
    }
    header('Location: ' . webstar_url('contact.php') . '?' . http_build_query($params) . '#book', true, 303);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!empty(trim((string) ($_POST['website'] ?? '')))) {
    book_redirect('error', 'generic');
}

$postedToken = (string) ($_POST['book_token'] ?? '');
$sessionToken = (string) ($_SESSION['book_token'] ?? '');
if ($postedToken === '' || $sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    book_redirect('error', 'generic');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$company = trim((string) ($_POST['company'] ?? ''));
$topic = trim((string) ($_POST['topic'] ?? ''));
$presenceUrl = trim((string) ($_POST['presence_url'] ?? ''));
$slot = trim((string) ($_POST['slot'] ?? ''));

$missing = [];
if ($name === '' || strlen($name) > 120) {
    $missing[] = 'name';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    $missing[] = 'email';
}
if ($phone === '' || !preg_match('/^[\d\s().+\-]{7,30}$/', $phone)) {
    $missing[] = 'phone';
}
if ($company !== '' && strlen($company) > 160) {
    $missing[] = 'company';
}
if ($topic !== '' && strlen($topic) > 2000) {
    $missing[] = 'topic';
}
if ($presenceUrl !== '' && strlen($presenceUrl) > 500) {
    $missing[] = 'presence_url';
}
if ($slot === '') {
    $missing[] = 'slot';
}

$upload = $_FILES['logo_file'] ?? null;
$hasUpload = is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
if ($hasUpload) {
    $uploadError = (int) ($upload['error'] ?? UPLOAD_ERR_OK);
    $uploadSize = (int) ($upload['size'] ?? 0);
    $uploadName = strtolower((string) ($upload['name'] ?? ''));
    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'];
    $ext = pathinfo($uploadName, PATHINFO_EXTENSION);
    if ($uploadError !== UPLOAD_ERR_OK || $uploadSize > 5 * 1024 * 1024 || !in_array($ext, $allowed, true)) {
        $missing[] = 'logo';
    }
}

if ($missing !== []) {
    book_redirect('error', 'validation', implode(',', array_unique($missing)));
}

$lock = fopen(sys_get_temp_dir() . '/webstar-consultation.lock', 'c');
if ($lock === false) {
    book_redirect('error', 'send');
}
flock($lock, LOCK_EX);

$resolved = webstar_calendar_resolve_slot($slot);
if (!$resolved['ok']) {
    flock($lock, LOCK_UN);
    fclose($lock);
    $reason = $resolved['reason'] === 'config' ? 'config' : ($resolved['reason'] === 'validation' ? 'validation' : 'send');
    book_redirect('error', $reason, $reason === 'validation' ? 'slot' : null);
}
if ($resolved['status'] !== 'open') {
    flock($lock, LOCK_UN);
    fclose($lock);
    book_redirect('error', 'taken');
}

/** @var DateTimeImmutable $start */
$start = $resolved['start'];
$description = implode("\n", array_filter([
    'Free 1-on-1 consultation',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . $phone,
    $company !== '' ? 'Company: ' . $company : '',
    $presenceUrl !== '' ? 'Online presence: ' . $presenceUrl : '',
    $topic !== '' ? "What they want to talk about:\n" . $topic : '',
], static fn (string $line): bool => $line !== ''));

$created = webstar_calendar_create_event($start, $name, $email, $description);
flock($lock, LOCK_UN);
fclose($lock);

if (!$created['ok']) {
    book_redirect('error', $created['reason'] === 'config' ? 'config' : 'send');
}

$logoMailed = true;
if ($hasUpload) {
    $logoMailed = book_email_logo($name, $email, $phone, $company, $topic, $start, $upload);
}

$audit = webstar_presence_audit($presenceUrl);
$summaryMailed = book_email_presence_summary($name, $email, $start, $audit);

$_SESSION['book_token'] = bin2hex(random_bytes(16));

$successReason = null;
if (!$logoMailed && !$summaryMailed) {
    $successReason = 'logo_summary';
} elseif (!$logoMailed) {
    $successReason = 'logo';
} elseif (!$summaryMailed) {
    $successReason = 'summary';
}
book_redirect('success', $successReason);

/**
 * @param array<string, mixed> $upload
 */
function book_email_logo(string $name, string $email, string $phone, string $company, string $topic, DateTimeImmutable $start, array $upload): bool
{
    $mailConfig = book_mail_config();
    if ($mailConfig === null) {
        return false;
    }

    $body = implode("\n", [
        'Consultation logo upload',
        '------------------------',
        'When: ' . $start->format('l, M j, Y g:i A T'),
        'Name: ' . $name,
        'Email: ' . $email,
        'Phone: ' . $phone,
        'Company: ' . ($company !== '' ? $company : '(none)'),
        '',
        'What they want to talk about:',
        $topic !== '' ? $topic : '(none)',
    ]);

    $mail = new PHPMailer(true);
    try {
        book_configure_mailer($mail, $mailConfig);
        $mail->addAddress((string) $mailConfig['mail_to']);
        $mail->addReplyTo($email, $name);
        if (is_uploaded_file((string) $upload['tmp_name'])) {
            $mail->addAttachment((string) $upload['tmp_name'], (string) ($upload['name'] ?? 'logo-upload'));
        }
        $mail->Subject = 'Consultation logo — ' . $name;
        $mail->Body = $body;
        $mail->send();
    } catch (MailerException) {
        return false;
    }

    return true;
}

/**
 * @param array{status: string, url: string, title: string, https: bool, finding: string} $audit
 */
function book_email_presence_summary(string $name, string $email, DateTimeImmutable $start, array $audit): bool
{
    $mailConfig = book_mail_config();
    if ($mailConfig === null) {
        return false;
    }

    $when = $start->format('l, M j, Y g:i A T');
    $finding = webstar_presence_finding_text($audit);
    $linkLine = $audit['url'] !== '' ? "Link you sent: {$audit['url']}\n" : '';

    $body = implode("\n", [
        'Hi ' . $name . ',',
        '',
        'Thanks for booking a free 1-on-1 with Webstar. You’re set for ' . $when . '.',
        'A calendar invite with the Google Meet link is on its way from Google.',
        '',
        'Quick look at the online presence you shared:',
        $linkLine . $finding,
        '',
        'We’ll talk through what would help most on the call.',
        '',
        '— Ian',
        'Webstar Business Services',
        '248-564-3663',
        'ian@webstarbusinessservices.com',
    ]);

    $mail = new PHPMailer(true);
    try {
        book_configure_mailer($mail, $mailConfig);
        $mail->addAddress($email, $name);
        $mail->addCC((string) $mailConfig['mail_to']);
        $mail->addReplyTo((string) $mailConfig['mail_to'], (string) $mailConfig['mail_from_name']);
        $mail->Subject = 'Your Webstar 1-on-1 — quick look before the call';
        $mail->Body = $body;
        $mail->send();
    } catch (MailerException) {
        return false;
    }

    return true;
}

/**
 * @return array<string, mixed>|null
 */
function book_mail_config(): ?array
{
    static $config = false;
    if ($config !== false) {
        return $config;
    }

    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    $configPath = __DIR__ . '/mail-config.php';
    if (!is_file($autoload) || !is_file($configPath)) {
        $config = null;
        return null;
    }
    require_once $autoload;
    $loaded = require $configPath;
    if (!is_array($loaded)) {
        $config = null;
        return null;
    }
    foreach (['smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'mail_from', 'mail_from_name', 'mail_to'] as $key) {
        if (!isset($loaded[$key]) || trim((string) $loaded[$key]) === '') {
            $config = null;
            return null;
        }
    }
    $config = $loaded;

    return $config;
}

/**
 * @param array<string, mixed> $config
 */
function book_configure_mailer(PHPMailer $mail, array $config): void
{
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
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
}

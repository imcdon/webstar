<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . webstar_url('contact.php'), true, 303);
    exit;
}

function contact_redirect(string $status, ?string $reason = null, ?string $fields = null): void
{
    $params = ['status' => $status];
    if ($reason) {
        $params['reason'] = $reason;
    }
    if ($fields) {
        $params['fields'] = $fields;
    }
    header('Location: ' . webstar_url('contact.php') . '?' . http_build_query($params) . '#inquiry', true, 303);
    exit;
}

if (!empty(trim((string) ($_POST['website'] ?? '')))) {
    contact_redirect('error', 'generic');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$missing = [];
if ($name === '' || strlen($name) > 120) {
    $missing[] = 'name';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $missing[] = 'email';
}
if ($phone !== '' && !preg_match('/^[\d\s().+\-]{7,30}$/', $phone)) {
    $missing[] = 'phone';
}
if ($message === '' || strlen($message) > 4000) {
    $missing[] = 'message';
}

if ($missing !== []) {
    contact_redirect('error', 'validation', implode(',', array_unique($missing)));
}

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoload)) {
    contact_redirect('error', 'config');
}
require $autoload;

$configPath = __DIR__ . '/mail-config.php';
if (!is_file($configPath)) {
    contact_redirect('error', 'config');
}
$config = require $configPath;
foreach (['smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'mail_from', 'mail_from_name', 'mail_to'] as $key) {
    if (!isset($config[$key]) || trim((string) $config[$key]) === '') {
        contact_redirect('error', 'config');
    }
}
$placeholders = ['smtp.example.com', 'your-smtp-user@example.com', 'your-app-password', 'your-email@domain.com'];
foreach (['smtp_host', 'smtp_user', 'smtp_pass', 'mail_to'] as $key) {
    if (in_array(trim((string) $config[$key]), $placeholders, true)) {
        contact_redirect('error', 'config');
    }
}

$body = implode("\n", [
    'New contact form inquiry',
    '------------------------',
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone !== '' ? $phone : '(none)'),
    '',
    'Message:',
    $message,
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
    $mail->addReplyTo($email, $name);
    $mail->Subject = 'Contact inquiry — ' . $name;
    $mail->Body = $body;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->send();
    contact_redirect('success');
} catch (MailerException $e) {
    contact_redirect('error', 'send');
}

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

// #region agent log
function book_agent_log(string $hypothesisId, string $message, array $data = []): void
{
    $line = json_encode([
        'sessionId' => 'f32897',
        'runId' => 'consultation-mail',
        'hypothesisId' => $hypothesisId,
        'location' => 'process-consultation.php',
        'message' => $message,
        'data' => $data,
        'timestamp' => (int) round(microtime(true) * 1000),
    ], JSON_UNESCAPED_SLASHES);
    @file_put_contents(dirname(__DIR__) . '/debug-f32897.log', $line . "\n", FILE_APPEND);
}
// #endregion

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

$mailConfig = webstar_mail_config();
// #region agent log
book_agent_log('A', 'post-book mail config', [
    'mail_config_ok' => $mailConfig !== null,
    'has_upload' => $hasUpload,
    'upload_is_file' => $hasUpload && is_array($upload) && is_uploaded_file((string) ($upload['tmp_name'] ?? '')),
    'upload_error' => $hasUpload ? (int) ($upload['error'] ?? -1) : null,
    'has_presence' => $presenceUrl !== '',
    'has_company' => $company !== '',
    'has_topic' => $topic !== '',
]);
// #endregion

$logoMailed = true;
$summaryMailed = true;
if ($mailConfig === null) {
    $logoMailed = !$hasUpload;
    $summaryMailed = false;
} else {
    $mail = new PHPMailer(true);
    try {
        webstar_configure_phpmailer($mail, $mailConfig);
        $mail->SMTPKeepAlive = true;

        if ($hasUpload) {
            $logoMailed = book_email_logo($mail, $mailConfig, $name, $email, $phone, $company, $topic, $presenceUrl, $start, $upload);
        }

        $audit = webstar_presence_audit($presenceUrl);
        $summaryMailed = book_email_presence_summary($mail, $mailConfig, $name, $email, $start, $audit);

        $mail->smtpClose();
    } catch (MailerException $e) {
        // #region agent log
        book_agent_log('C', 'mailer exception after book', [
            'error_info' => $mail->ErrorInfo,
        ]);
        // #endregion
        if ($hasUpload) {
            $logoMailed = false;
        }
        $summaryMailed = false;
    }
}

// #region agent log
book_agent_log('B', 'post-book mail results', [
    'logo_mailed' => $logoMailed,
    'summary_mailed' => $summaryMailed,
]);
// #endregion

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
 * @param array<string, mixed> $mailConfig
 * @param array<string, mixed> $upload
 */
function book_email_logo(
    PHPMailer $mail,
    array $mailConfig,
    string $name,
    string $email,
    string $phone,
    string $company,
    string $topic,
    string $presenceUrl,
    DateTimeImmutable $start,
    array $upload
): bool {
    $tmp = (string) ($upload['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp)) {
        // #region agent log
        book_agent_log('D', 'logo upload tmp missing after book', [
            'upload_error' => (int) ($upload['error'] ?? -1),
        ]);
        // #endregion
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
        'Online presence: ' . ($presenceUrl !== '' ? $presenceUrl : '(none)'),
        '',
        'What they want to talk about:',
        $topic !== '' ? $topic : '(none)',
    ]);

    try {
        $mail->clearAllRecipients();
        $mail->clearAttachments();
        $mail->clearReplyTos();
        $mail->addAddress((string) $mailConfig['mail_to']);
        $mail->addReplyTo($email, $name);
        $mail->addAttachment($tmp, (string) ($upload['name'] ?? 'logo-upload'));
        $mail->Subject = 'Consultation logo — ' . $name;
        $mail->Body = $body;
        $mail->send();
    } catch (MailerException $e) {
        // #region agent log
        book_agent_log('C', 'logo mail failed', [
            'error_info' => $mail->ErrorInfo,
        ]);
        // #endregion
        return false;
    }

    return true;
}

/**
 * @param array<string, mixed> $mailConfig
 * @param array{status: string, url: string, title: string, https: bool, finding: string} $audit
 */
function book_email_presence_summary(
    PHPMailer $mail,
    array $mailConfig,
    string $name,
    string $email,
    DateTimeImmutable $start,
    array $audit
): bool {
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

    try {
        $mail->clearAllRecipients();
        $mail->clearAttachments();
        $mail->clearReplyTos();
        $mail->clearCCs();
        $mail->addAddress($email, $name);
        $mail->addCC((string) $mailConfig['mail_to']);
        $mail->addReplyTo((string) $mailConfig['mail_to'], (string) $mailConfig['mail_from_name']);
        $mail->Subject = 'Your Webstar 1-on-1 — quick look before the call';
        $mail->Body = $body;
        $mail->send();
    } catch (MailerException $e) {
        // #region agent log
        book_agent_log('C', 'summary mail to guest failed, trying staff copy', [
            'error_info' => $mail->ErrorInfo,
        ]);
        // #endregion
        try {
            $staffBody = implode("\n", [
                'Consultation booked — guest summary email could not be delivered.',
                '',
                'Guest: ' . $name . ' <' . $email . '>',
                'When: ' . $when,
                '',
                $body,
            ]);
            $mail->clearAllRecipients();
            $mail->clearAttachments();
            $mail->clearReplyTos();
            $mail->clearCCs();
            $mail->addAddress((string) $mailConfig['mail_to']);
            $mail->addReplyTo($email, $name);
            $mail->Subject = 'Consultation summary (guest copy failed) — ' . $name;
            $mail->Body = $staffBody;
            $mail->send();
        } catch (MailerException $e2) {
            // #region agent log
            book_agent_log('C', 'summary mail staff fallback failed', [
                'error_info' => $mail->ErrorInfo,
            ]);
            // #endregion
            return false;
        }

        return false;
    }

    return true;
}

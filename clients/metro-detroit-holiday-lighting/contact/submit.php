<?php
declare(strict_types=1);
/*
 * contact/submit.php - Handle contact form POST.
 */
require dirname(__DIR__) . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('contact/'));
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$emailAddr = trim((string) ($_POST['email'] ?? ''));
$phoneField = trim((string) ($_POST['phone'] ?? ''));
$propertyType = trim((string) ($_POST['property_type'] ?? ''));
$city = trim((string) ($_POST['city'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$honeypot = trim((string) ($_POST['website'] ?? ''));

$_SESSION['contact_form'] = [
    'name' => $name,
    'email' => $emailAddr,
    'phone' => $phoneField,
    'property_type' => $propertyType,
    'city' => $city,
    'message' => $message,
];

if ($honeypot !== '') {
    unset($_SESSION['contact_form']);
    $_SESSION['contact_success'] = 'Thanks — your message has been sent.';
    header('Location: ' . url('contact/'));
    exit;
}

$errors = [];
if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($emailAddr === '' || !filter_var($emailAddr, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
}
if ($message === '') {
    $errors[] = 'Message is required.';
}

$to = $contact_form_to !== '' ? $contact_form_to : ($email !== '' ? $email : '');
if ($to === '') {
    $errors[] = 'Contact form is not configured yet. Please call us instead.';
}

if ($errors) {
    $_SESSION['contact_error'] = implode(' ', $errors);
    header('Location: ' . url('contact/'));
    exit;
}

$typeLabels = [
    'home' => 'Home',
    'business' => 'Business / storefront',
    'hoa' => 'HOA / community',
];
$typeLabel = $typeLabels[$propertyType] ?? ($propertyType !== '' ? $propertyType : 'Not specified');

$subject = 'Website inquiry from ' . $name;
$body = "Name: {$name}\n"
    . "Email: {$emailAddr}\n"
    . "Phone: {$phoneField}\n"
    . "Property type: {$typeLabel}\n"
    . "City: {$city}\n\n"
    . "Message:\n{$message}\n";

$fromAddr = !empty($mail_from)
    ? $mail_from
    : ('noreply@' . ($site_domain ?? 'metrodetroitholidaylighting.com'));
$headers = [
    'From: ' . $site_name . ' <' . $fromAddr . '>',
    'Reply-To: ' . $emailAddr,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = false;

// Prefer Webstar shared mail config when available (local / Path A staging under this repo).
$webstarMail = dirname(__DIR__, 3) . '/library/mail-config.php';
if (is_file($webstarMail) && is_file(dirname(__DIR__, 3) . '/vendor/autoload.php')) {
    try {
        require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
        $mailCfg = require $webstarMail;
        if (is_array($mailCfg) && class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = (string) ($mailCfg['smtp_host'] ?? '');
            $mailer->Port = (int) ($mailCfg['smtp_port'] ?? 587);
            $mailer->SMTPAuth = true;
            $mailer->Username = (string) ($mailCfg['smtp_user'] ?? '');
            $mailer->Password = (string) ($mailCfg['smtp_pass'] ?? '');
            $secure = strtolower((string) ($mailCfg['smtp_secure'] ?? 'tls'));
            if ($secure === 'ssl') {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $from = (string) ($mailCfg['mail_from'] ?? $fromAddr);
            $fromName = (string) ($mailCfg['mail_from_name'] ?? $site_name);
            $mailer->setFrom($from, $fromName);
            $mailer->addAddress($to);
            $mailer->addReplyTo($emailAddr, $name);
            $mailer->Subject = $subject;
            $mailer->Body = $body;
            $mailer->send();
            $sent = true;
        }
    } catch (Throwable $e) {
        $sent = false;
    }
}

if (!$sent) {
    $sent = @mail($to, $subject, $body, implode("\r\n", $headers));
}

if ($sent) {
    unset($_SESSION['contact_form']);
    $_SESSION['contact_success'] = 'Thanks — your message has been sent. Steven will get back to you soon.';
} else {
    $_SESSION['contact_error'] = 'Sorry, we could not send your message right now. Please call ' . $phone . ' instead.';
}

header('Location: ' . url('contact/'));
exit;

<?php
/*
 * contact/submit.php - Handle contact form POST (PHP mail + honeypot).
 */
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('contact/'));
    exit;
}

$name = trim($_POST['name'] ?? '');
$emailAddr = trim($_POST['email'] ?? '');
$phoneField = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');
$honeypot = trim($_POST['website'] ?? '');

$_SESSION['contact_form'] = [
    'name'    => $name,
    'email'   => $emailAddr,
    'phone'   => $phoneField,
    'service' => $service,
    'message' => $message,
];

if ($honeypot !== '') {
    // Likely bot — pretend success
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

$to = $contact_form_to !== ''
    ? $contact_form_to
    : ($email !== '' ? $email : '');

if ($to === '') {
    $errors[] = 'Contact form is not configured yet. Please call us instead.';
}

if ($errors) {
    $_SESSION['contact_error'] = implode(' ', $errors);
    header('Location: ' . url('contact/'));
    exit;
}

$serviceLabel = $contact_service_options[$service] ?? $service;
$subject = 'Website inquiry from ' . $name;
$body = "Name: {$name}\n"
    . "Email: {$emailAddr}\n"
    . "Phone: {$phoneField}\n"
    . "Service: {$serviceLabel}\n\n"
    . "Message:\n{$message}\n";

$fromAddr = !empty($mail_from)
    ? $mail_from
    : ('noreply@' . ($site_domain ?? 'superioriceadventures.com'));
$headers = [
    'From: ' . $site_name . ' <' . $fromAddr . '>',
    'Reply-To: ' . $emailAddr,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    unset($_SESSION['contact_form']);
    $_SESSION['contact_success'] = 'Thanks — your message has been sent. We will get back to you soon.';
} else {
    $_SESSION['contact_error'] = 'Sorry, we could not send your message right now. Please call us instead.';
}

header('Location: ' . url('contact/'));
exit;

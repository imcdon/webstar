<?php
declare(strict_types=1);
return [
    'smtp_host' => 'smtp.example.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    // Prefer Google Workspace: smtp.gmail.com:587 + App Password for ian@...
    'smtp_user' => 'ian@webstarbusinessservices.com',
    'smtp_pass' => 'your-app-password',
    'mail_from' => 'ian@webstarbusinessservices.com',
    'mail_from_name' => 'Webstar Business Services',
    'mail_to' => 'ian@webstarbusinessservices.com',
];

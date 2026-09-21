<?php
/*
 * logout.php - End admin session.
 */
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

logout_user();
header('Location: ' . url('admin/login.php'));
exit;

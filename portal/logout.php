<?php
declare(strict_types=1);
require dirname(__DIR__) . '/library/portal-auth.php';

portal_logout();
header('Location: ' . webstar_url('portal/login.php'), true, 303);
exit;

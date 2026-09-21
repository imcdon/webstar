<?php
declare(strict_types=1);
require dirname(__DIR__) . '/library/portal-auth.php';

portal_start_session();
if (portal_current_username() !== null) {
    header('Location: ' . webstar_url('portal/projects.php'), true, 303);
    exit;
}
header('Location: ' . webstar_url('portal/login.php'), true, 303);
exit;

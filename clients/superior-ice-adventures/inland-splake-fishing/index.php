<?php
require __DIR__ . '/../includes/config.php';
$service = get_service('inland-splake-fishing');
$page_title = $service['title'] . ' | ' . $site_name;
$page_description = $service['summary'];
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/partials/service-page.php';
require __DIR__ . '/../includes/footer.php';

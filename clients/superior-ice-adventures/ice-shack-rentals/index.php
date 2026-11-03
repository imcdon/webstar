<?php
require __DIR__ . '/../includes/config.php';
$service = get_service('ice-shack-rentals');
$page_title = $service['title'] . ' | ' . $site_name;
$page_description = $service['summary'];
$preload_image = url(ltrim($service['hero_image'], '/'));
$schema_type = 'service';
$schema_service = $service;
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => $service['nav_label'], 'url' => absolute_url($service['slug'] . '/')],
];
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/partials/service-page.php';
require __DIR__ . '/../includes/footer.php';

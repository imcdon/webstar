<?php

declare(strict_types=1);

require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/staging-gate.php';

$site_name = "DJ's Tackle";
$site_domain = 'djstackle.com';
$site_url = 'https://djstackle.com';
$site_tagline = 'Your one stop shop for jigs, Arctic Spinners, stinger hooks, bottom bouncers, crawler harnesses, and plastics';
$site_description = "DJ's Tackle — chip-free jig heads, durable plastics, River Ready boxes, stinger hooks, bottom bouncers, and Arctic Spinners. Family-owned in Sterling Heights, MI.";
$company_name = "DJ's Tackle LLC";

$phone = '(810) 627-9974';
$phone_href = 'tel:+18106279974';
$email = 'djstackle20@gmail.com';
$email_href = 'mailto:djstackle20@gmail.com';
$contact_form_to = 'djstackle20@gmail.com';
$mail_from = 'noreply@djstackle.com';

$address = [
    'line1' => '',
    'city' => 'Sterling Heights',
    'region' => 'MI',
    'postal_code' => '48313',
    'country' => 'US',
    'note' => 'Online store · Local pickup available',
];

$hours = [
    ['day' => 'Online', 'hours' => 'Open 24/7'],
    ['day' => 'Pickup', 'hours' => 'By appointment — Sterling Heights, MI'],
];

$social = [
    'facebook' => '',
    'instagram' => '',
];

$ga4_measurement_id = '';

$nav_links = [
    'Shop' => '/shop/',
    'Gift Cards' => '/gift-cards/',
    'About' => '/about/',
    'FAQ' => '/faq/',
    'Contact' => '/contact/',
];

$footer_links = [
    'Shipping & Returns' => '/shipping-returns/',
    'Account' => '/account/',
    'Wishlist' => '/wishlist/',
    'Privacy' => '/privacy/',
    'Terms' => '/terms/',
];

/** Fishing-show popup: true = show on every page load while designing; false = once per browser (localStorage). */
$fishing_show_modal_every_visit = false;

/** Retailers that carry DJ's Tackle — edit name + location; optional url for maps or store site */
$retail_stores = [
    ['name' => "Frank's Great Outdoors", 'location' => 'Linwood, MI'],
    ['name' => 'Shoreline Bait & Tackle', 'location' => 'Detroit, MI'],
    ['name' => "Toddy's Bait & Tackle", 'location' => 'St. Clair Shores, MI'],
    ['name' => 'Bottom Dollar Bait', 'location' => 'Monroe, MI'],
    ['name' => 'Snoozies Bait & Tackle', 'location' => 'Chesterfield, MI'],
    ['name' => 'Hot Spot Bait & Tackle', 'location' => 'Trenton, MI'],
];

$shop_config_file = __DIR__ . '/shop-config.php';
if (!is_file($shop_config_file)) {
    $shop_config_file = __DIR__ . '/shop-config.example.php';
}
$shop_config_path = $shop_config_file;

$shopConfig = require dirname(__DIR__, 2) . '/shared/shop/bootstrap.php';

$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
    global $base_path;
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $site_url = $scheme . '://' . $host . ($base_path ?? '');
}

if (!class_exists('Helpers', false)) {
    class_alias(\Webstar\Shop\Helpers::class, 'Helpers');
}

$shopCatalog = \Webstar\Shop\Catalog::make($shopConfig);
$shopCart = \Webstar\Shop\Cart::make($shopConfig);
$shopAccounts = \Webstar\Shop\Accounts::make($shopConfig);
$shopWishlist = \Webstar\Shop\Wishlist::make($shopConfig);
$shopAccount = $shopAccounts->current();
$cart_count = $shopCart->count();

if (!function_exists('product_image_url')) {
    function product_image_url(array $product): string
    {
        if (!empty($product['local_image']) && is_file(dirname(__DIR__) . '/' . $product['local_image'])) {
            return url($product['local_image']);
        }
        if (!empty($product['image_url'])) {
            return (string) $product['image_url'];
        }
        return url('assets/img/placeholder.svg');
    }
}

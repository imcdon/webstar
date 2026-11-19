<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/../shared/shop/vendor/autoload.php';

use Square\Locations\Requests\GetLocationsRequest;
use Square\Merchants\Requests\GetMerchantsRequest;
use Webstar\Shop\Config;
use Webstar\Shop\SquareClientFactory;

$cfg = require $root . '/includes/shop-config.php';
$c = Config::fromArray($cfg);
$client = SquareClientFactory::make($c);

$r = $client->locations->get(new GetLocationsRequest(['locationId' => $c->squareLocationId]));
$loc = $r->getLocation();
if ($loc === null) {
    fwrite(STDERR, "Location not found: {$c->squareLocationId}\n");
    exit(1);
}

echo 'location_id=' . ($loc->getId() ?? '') . "\n";
echo 'name=' . ($loc->getName() ?? '') . "\n";
echo 'status=' . ($loc->getStatus() ?? '') . "\n";
echo 'capabilities=' . json_encode($loc->getCapabilities()) . "\n";

$merchantId = $loc->getMerchantId() ?? 'me';
try {
    $mr = $client->merchants->get(new GetMerchantsRequest(['merchantId' => $merchantId]));
    $m = $mr->getMerchant();
    if ($m !== null) {
        echo 'merchant_business_name=' . ($m->getBusinessName() ?? '') . "\n";
        echo 'merchant_status=' . ($m->getStatus() ?? '') . "\n";
    }
} catch (Throwable $e) {
    echo 'merchant_error=' . $e->getMessage() . "\n";
}
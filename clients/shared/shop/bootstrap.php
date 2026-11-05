<?php

declare(strict_types=1);

/**
 * Bootstrap the Webstar shop module for a client site.
 *
 * Expects $shop_config_path (absolute path to shop-config.php) to be set
 * by the caller, OR $shop_config (array) already loaded.
 */

$shopRoot = __DIR__;

$autoload = $shopRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    throw new RuntimeException(
        'Shop vendor/ missing. From clients/shared/shop run: php ../../../composer.phar install'
    );
}
require_once $autoload;

use Webstar\Shop\Config;
use Webstar\Shop\Db;

if (!isset($shop_config) || !is_array($shop_config)) {
    $path = $shop_config_path ?? null;
    if (!is_string($path) || !is_file($path)) {
        throw new RuntimeException('Shop config not found. Set $shop_config_path or $shop_config.');
    }
    $shop_config = require $path;
}

$shopConfig = Config::fromArray($shop_config);
$shopDb = Db::connect($shopConfig->sqlitePath);

return $shopConfig;

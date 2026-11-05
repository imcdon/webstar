<?php

declare(strict_types=1);

namespace Webstar\Shop;

use RuntimeException;
use Square\Environments;
use Square\SquareClient;

final class SquareClientFactory
{
    public static function make(Config $config): SquareClient
    {
        if ($config->demoMode || $config->squareToken === '' || $config->squareToken === 'REPLACE_ME') {
            throw new RuntimeException(
                'Square credentials not configured. Copy includes/shop-config.example.php to shop-config.php and add sandbox credentials, or use demo seed (bin/seed-local.php).'
            );
        }

        return new SquareClient(
            token: $config->squareToken,
            options: [
                'baseUrl' => $config->isSandbox()
                    ? Environments::Sandbox->value
                    : Environments::Production->value,
            ],
        );
    }
}

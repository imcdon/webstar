<?php

declare(strict_types=1);

namespace Webstar\Shop;

use RuntimeException;
use Square\Catalog\Requests\SearchCatalogObjectsRequest;
use Square\Types\CatalogQuery;
use Square\Types\CatalogQueryExact;

/**
 * Resolve a coupon code to a Square CatalogDiscount by exact name match
 * (case-insensitive). Owner manages codes in Dashboard → Items → Discounts.
 */
final class Coupons
{
    public function __construct(private readonly Config $config)
    {
    }

    public static function make(Config $config): self
    {
        return new self($config);
    }

    /**
     * @return array{id:string,name:string}|null
     */
    public function findByCode(string $code): ?array
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }
        if ($this->config->demoMode) {
            // Demo: accept DEMO10 as a fixed 10% stand-in (no Square call)
            if (strcasecmp($code, 'DEMO10') === 0) {
                return ['id' => 'demo_discount_10', 'name' => 'DEMO10'];
            }
            return null;
        }

        $square = SquareClientFactory::make($this->config);
        // Exact attribute match is case-sensitive; try as entered then uppercase.
        foreach ([$code, strtoupper($code), strtolower($code)] as $try) {
            $response = $square->catalog->search(new SearchCatalogObjectsRequest([
                'objectTypes' => ['DISCOUNT'],
                'includeDeletedObjects' => false,
                'query' => new CatalogQuery([
                    'exactQuery' => new CatalogQueryExact([
                        'attributeName' => 'name',
                        'attributeValue' => $try,
                    ]),
                ]),
            ]));
            foreach ($response->getObjects() ?? [] as $obj) {
                if (!$obj->isDiscount()) {
                    continue;
                }
                $discount = $obj->asDiscount();
                $name = (string) ($discount->getDiscountData()?->getName() ?? '');
                if (strcasecmp($name, $code) === 0) {
                    return ['id' => $discount->getId(), 'name' => $name];
                }
            }
        }

        // Fallback: list discounts and match case-insensitively (small catalogs)
        $pager = $square->catalog->list(new \Square\Catalog\Requests\ListCatalogRequest([
            'types' => 'DISCOUNT',
        ]));
        foreach ($pager as $obj) {
            if (!$obj->isDiscount()) {
                continue;
            }
            $discount = $obj->asDiscount();
            $name = (string) ($discount->getDiscountData()?->getName() ?? '');
            if (strcasecmp($name, $code) === 0) {
                return ['id' => $discount->getId(), 'name' => $name];
            }
        }

        return null;
    }

    /**
     * @throws RuntimeException when code is non-empty but not found
     * @return array{id:string,name:string}|null
     */
    public function requireByCode(string $code): ?array
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }
        $found = $this->findByCode($code);
        if ($found === null) {
            throw new RuntimeException('Coupon code not found.');
        }
        return $found;
    }
}

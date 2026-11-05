<?php

declare(strict_types=1);

namespace Webstar\Shop;

final class Seo
{
    public function __construct(private readonly Config $config)
    {
    }

    public static function make(Config $config): self
    {
        return new self($config);
    }

    /**
     * @param array<string,mixed> $product
     */
    public function productJsonLd(array $product): array
    {
        $offers = [];
        foreach ($product['variations'] ?? [] as $v) {
            $offers[] = [
                '@type' => 'Offer',
                'sku' => $v['sku'] ?? $v['id'],
                'name' => $v['name'],
                'price' => number_format(((int) $v['price_cents']) / 100, 2, '.', ''),
                'priceCurrency' => $this->config->currency,
                'availability' => ((int) $v['quantity'] > 0 || !(int) $v['track_inventory'])
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => $this->config->absoluteUrl('product/?slug=' . urlencode($product['slug'])),
            ];
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['description'] ?: $product['name'],
            'image' => array_values(array_filter(array_map(
                fn ($img) => $img['url'] ?? null,
                $product['images'] ?? []
            ))),
            'offers' => count($offers) === 1 ? $offers[0] : $offers,
        ];

        if (!empty($product['review_count']) && !empty($product['avg_rating'])) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product['avg_rating'],
                'reviewCount' => $product['review_count'],
            ];
        }

        $reviews = [];
        foreach (array_slice($product['reviews'] ?? [], 0, 5) as $r) {
            $reviews[] = [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $r['author_name']],
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => (int) $r['rating']],
                'reviewBody' => $r['body'],
            ];
        }
        if ($reviews) {
            $data['review'] = $reviews;
        }

        return $data;
    }

    /**
     * @param list<array{name:string,url:string}> $crumbs
     */
    public function breadcrumbJsonLd(array $crumbs): array
    {
        $items = [];
        foreach ($crumbs as $i => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public function organizationJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->config->siteName,
            'url' => $this->config->siteUrl,
            'email' => $this->config->siteEmail,
            'telephone' => $this->config->supportPhone,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Sterling Heights',
                'addressRegion' => 'MI',
                'postalCode' => '48313',
                'addressCountry' => 'US',
            ],
        ];
    }
}

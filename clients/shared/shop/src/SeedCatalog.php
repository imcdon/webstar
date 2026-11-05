<?php

declare(strict_types=1);

namespace Webstar\Shop;

use RuntimeException;

/**
 * Loads a client's seed/catalog.php and expands option combinations into
 * concrete variations. Falls back to legacy seed/products.json shape.
 *
 * Normalized listing shape:
 *   name, category, description, local_image, images[],
 *   options => [['name' => 'Size', 'values' => [...]], ...],
 *   variations => [['name','sku','price_cents','quantity','option_values' => ['Size' => '1/2 oz']], ...]
 */
final class SeedCatalog
{
    /** @return list<array<string,mixed>> */
    public static function load(string $clientRoot): array
    {
        $structured = $clientRoot . '/seed/catalog.php';
        if (is_file($structured)) {
            $listings = require $structured;
            if (!is_array($listings)) {
                throw new RuntimeException('seed/catalog.php must return an array');
            }
            return array_map([self::class, 'expand'], array_values($listings));
        }

        $legacy = $clientRoot . '/seed/products.json';
        if (!is_file($legacy)) {
            throw new RuntimeException('No seed/catalog.php or seed/products.json found');
        }
        $json = json_decode((string) file_get_contents($legacy), true);
        if (!is_array($json)) {
            throw new RuntimeException('Invalid seed/products.json');
        }
        return array_map([self::class, 'fromLegacy'], $json);
    }

    /** @param array<string,mixed> $listing */
    public static function expand(array $listing): array
    {
        $options = [];
        foreach (($listing['options'] ?? []) as $optName => $values) {
            $options[] = ['name' => (string) $optName, 'values' => array_values(array_map('strval', $values))];
        }

        $combos = [[]];
        foreach ($options as $opt) {
            $next = [];
            foreach ($combos as $combo) {
                foreach ($opt['values'] as $value) {
                    $next[] = $combo + [$opt['name'] => $value];
                }
            }
            $combos = $next;
        }

        $prefix = (string) ($listing['sku_prefix'] ?? 'SKU');
        $defaultQty = (int) ($listing['quantity'] ?? 25);
        $variations = [];
        foreach ($combos as $i => $combo) {
            $label = $combo === [] ? 'Regular' : implode(' / ', $combo);
            $variations[] = [
                'name' => $label,
                'sku' => self::sku($prefix, $combo, $i),
                'price_cents' => self::price($listing, $combo),
                'quantity' => $defaultQty,
                'option_values' => $combo,
            ];
        }

        return [
            'key' => (string) ($listing['key'] ?? Helpers::slugify((string) $listing['name'])),
            'name' => (string) $listing['name'],
            'category' => (string) ($listing['category'] ?? 'Tackle'),
            'description' => (string) ($listing['description'] ?? ''),
            'local_image' => $listing['local_image'] ?? null,
            'images' => array_values($listing['images'] ?? []),
            'options' => $options,
            'variations' => $variations,
        ];
    }

    /** @param array<string,mixed> $p */
    private static function fromLegacy(array $p): array
    {
        $vars = [];
        foreach (($p['variations'] ?? [['name' => 'Default']]) as $i => $v) {
            $vars[] = [
                'name' => (string) ($v['name'] ?? 'Default'),
                'sku' => (string) ($v['sku'] ?? ('DJT-' . ($p['ecwid_id'] ?? 'X') . ($i ? "-$i" : ''))),
                'price_cents' => (int) ($v['price_cents'] ?? $p['price_cents'] ?? 0),
                'quantity' => (int) ($v['quantity'] ?? 25),
                'option_values' => [],
            ];
        }
        return [
            'key' => (string) ($p['ecwid_id'] ?? Helpers::slugify((string) $p['name'])),
            'name' => (string) $p['name'],
            'category' => (string) ($p['category'] ?? 'Tackle'),
            'description' => (string) ($p['description'] ?? ''),
            'local_image' => $p['local_image'] ?? null,
            'images' => array_values(array_filter($p['images'] ?? [], fn ($u) => is_string($u) && str_ends_with(strtolower($u), '.jpg'))),
            'options' => [],
            'variations' => $vars,
        ];
    }

    /**
     * @param array<string,mixed> $listing
     * @param array<string,string> $combo
     */
    private static function price(array $listing, array $combo): int
    {
        $key = implode(' / ', $combo);
        if (isset($listing['prices'][$key])) {
            return (int) $listing['prices'][$key];
        }
        foreach (($listing['price_by'] ?? []) as $optName => $map) {
            $value = $combo[$optName] ?? null;
            if ($value !== null && isset($map[$value])) {
                return (int) $map[$value];
            }
        }
        if (isset($listing['price_cents'])) {
            return (int) $listing['price_cents'];
        }
        throw new RuntimeException("No price for {$listing['name']} [{$key}]");
    }

    /** @param array<string,string> $combo */
    private static function sku(string $prefix, array $combo, int $index): string
    {
        if ($combo === []) {
            return $prefix;
        }
        $parts = [];
        foreach ($combo as $value) {
            $clean = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $value), '-');
            $parts[] = strtoupper($clean !== '' ? $clean : (string) $index);
        }
        return $prefix . '-' . implode('-', $parts);
    }
}

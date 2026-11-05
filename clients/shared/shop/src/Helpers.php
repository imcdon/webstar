<?php

declare(strict_types=1);

namespace Webstar\Shop;

final class Helpers
{
    public static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-') ?: 'item';
    }

    public static function money(int $cents, string $currency = 'USD'): string
    {
        $amount = number_format($cents / 100, 2);
        return $currency === 'USD' ? '$' . $amount : $amount . ' ' . $currency;
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function jsonLd(array $data): string
    {
        return '<script type="application/ld+json">' .
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) .
            '</script>';
    }

    public static function stockBadge(int $qty, int $lowThreshold, bool $track = true): array
    {
        if (!$track) {
            return ['label' => 'In stock', 'class' => 'in-stock', 'state' => 'in_stock'];
        }
        if ($qty <= 0) {
            return ['label' => 'Sold out', 'class' => 'sold-out', 'state' => 'sold_out'];
        }
        if ($qty <= $lowThreshold) {
            return ['label' => 'Low stock', 'class' => 'low-stock', 'state' => 'low_stock'];
        }
        return ['label' => 'In stock', 'class' => 'in-stock', 'state' => 'in_stock'];
    }
}

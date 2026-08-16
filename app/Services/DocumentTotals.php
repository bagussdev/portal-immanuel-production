<?php

namespace App\Services;

class DocumentTotals
{
    public static function money(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) (preg_replace('/\D+/', '', (string) $value) ?: 0);
    }

    public static function decimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = str_replace(',', '.', trim((string) $value));

        return is_numeric($value) ? (float) $value : null;
    }

    public static function summarize(int $subtotal, ?float $discountPercent, int $discountValue, ?float $taxPercent, int $taxValue): array
    {
        $discount = $discountPercent !== null
            ? (int) round($subtotal * ($discountPercent / 100))
            : $discountValue;
        $discount = min(max($discount, 0), $subtotal);

        $afterDiscount = max($subtotal - $discount, 0);
        $tax = $taxPercent !== null
            ? (int) round($afterDiscount * ($taxPercent / 100))
            : $taxValue;
        $tax = min(max($tax, 0), $afterDiscount);

        return [
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'discount_value' => $discount,
            'tax_percent' => $taxPercent,
            'tax_value' => $tax,
            'grand_total' => max($afterDiscount - $tax, 0),
        ];
    }
}

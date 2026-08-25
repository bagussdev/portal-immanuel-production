<?php

namespace App\Services;

use Illuminate\Support\Str;

class DocumentLocations
{
    public static function normalize(array $data, string $defaultFlow = 'install_teardown'): array
    {
        if (! empty($data['locations'])) {
            return array_values($data['locations']);
        }

        return [[
            'name' => $data['location_event'] ?? null,
            'event_start_date' => $data['event_date'] ?? null,
            'event_end_date' => $data['event_end_date'] ?? null,
            'loading_date' => $data['loading_date'] ?? null,
            'teardown_date' => $data['bongkaran_date'] ?? null,
            'work_flow' => $data['work_flow'] ?? $defaultFlow,
            'items' => $data['items'] ?? [],
        ]];
    }

    public static function prepare(array $locations, string $defaultFlow = 'install_teardown'): array
    {
        $prepared = [];
        $subtotal = 0;

        foreach ($locations as $locationIndex => $location) {
            $items = [];
            $leaderIndex = null;
            foreach ($location['items'] ?? [] as $row) {
                $qty = (float) ($row['qty'] ?? 0);
                $length = filled($row['length'] ?? null) ? (float) $row['length'] : null;
                $mode = ($row['pricing_mode'] ?? 'unit') === 'total' ? 'total' : 'unit';
                $unitPrice = $mode === 'unit' ? DocumentTotals::money($row['unit_price'] ?? null) : 0;
                $lineTotal = $mode === 'total'
                    ? DocumentTotals::money($row['line_total'] ?? $row['total'] ?? null)
                    : (int) round($qty * (($length ?? 0) > 0 ? $length : 1) * $unitPrice);
                $mergePrice = filter_var($row['merge_price'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($mergePrice && $leaderIndex !== null) {
                    if (! $items[$leaderIndex]['price_group']) {
                        $group = (string) Str::uuid();
                        $items[$leaderIndex]['price_group'] = $group;
                    }
                    $items[] = [
                        'item_name' => $row['item_name'], 'qty' => $qty, 'length' => $length,
                        'pricing_mode' => $mode, 'unit_price' => 0, 'total' => 0,
                        'price_group' => $items[$leaderIndex]['price_group'],
                    ];
                    continue;
                }

                $subtotal += $lineTotal;
                $items[] = [
                    'item_name' => $row['item_name'], 'qty' => $qty, 'length' => $length,
                    'pricing_mode' => $mode, 'unit_price' => $unitPrice, 'total' => $lineTotal,
                    'price_group' => null,
                ];
                $leaderIndex = array_key_last($items);
            }

            $prepared[] = [
                'name' => $location['name'] ?? null,
                'event_start_date' => $location['event_start_date'] ?? null,
                'event_end_date' => $location['event_end_date'] ?? null,
                'loading_date' => $location['loading_date'] ?? null,
                'teardown_date' => $location['teardown_date'] ?? null,
                'work_flow' => $location['work_flow'] ?? $defaultFlow,
                'sort_order' => $locationIndex,
                'items' => $items,
            ];
        }

        return [$prepared, $subtotal];
    }
}

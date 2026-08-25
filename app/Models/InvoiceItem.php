<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'invoice_location_id',
        'qty',
        'item_name',
        'length',
        'pricing_mode',
        'unit_price',
        'total',
        'price_group',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'qty' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $qty = (float) ($item->qty ?? 0);
            $unit = (int) ($item->unit_price ?? 0);
            $len = is_null($item->length) ? 1 : (float) $item->length;
            $len = $len > 0 ? $len : 1;
            if ($item->pricing_mode !== 'total') {
                $item->total = $item->price_group ? $unit : (int) ($qty * $unit * $len);
            }
        });

        static::saved(function (InvoiceItem $item) {
            if ($item->invoice) {
                $item->invoice->refresh();
                $item->invoice->recalcTotalsAndStatus();
            }
        });

        static::deleted(function (InvoiceItem $item) {
            if ($item->invoice) {
                $item->invoice->refresh();
                $item->invoice->recalcTotalsAndStatus();
            }
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InvoiceLocation::class, 'invoice_location_id');
    }
}

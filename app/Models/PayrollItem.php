<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    public const TYPE_BASE = 'base';

    public const TYPE_DEDUCTION = 'deduction';

    protected $fillable = ['payroll_id', 'type', 'name', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /** Relasi: item → slip */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    /** Jaga konsistensi & auto-recalc setelah perubahan item */
    protected static function booted(): void
    {
        static::creating(function (self $item) {
            // Normalisasi type & amount
            $item->type = in_array($item->type, [self::TYPE_BASE, self::TYPE_DEDUCTION], true)
                ? $item->type
                : self::TYPE_BASE;
            $item->amount = max(0, (float) $item->amount);
        });

        static::updating(function (self $item) {
            $item->type = in_array($item->type, [self::TYPE_BASE, self::TYPE_DEDUCTION], true)
                ? $item->type
                : self::TYPE_BASE;
            $item->amount = max(0, (float) $item->amount);
        });

        // Setelah create/update/delete → recalc totals di slip
        $recalc = function (self $item) {
            $item->payroll?->recalcTotals();
        };

        static::created($recalc);
        static::updated($recalc);
        static::deleted($recalc);
    }
}

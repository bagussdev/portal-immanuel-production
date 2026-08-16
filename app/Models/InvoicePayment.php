<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'paid_at',
        'method',
        'amount',
        'attachment',
        'notes',
        'reference',
        'received_by',
        'percent',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'percent' => 'decimal:2',
        'amount' => 'integer',
        'voided_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    protected static function booted(): void
    {
        static::saved(function (InvoicePayment $p) {
            if ($p->invoice) {
                $p->invoice->refresh();
                $p->invoice->recalcTotalsAndStatus();
            }
        });

        static::deleted(function (InvoicePayment $p) {
            if ($p->invoice) {
                $p->invoice->refresh();
                $p->invoice->recalcTotalsAndStatus();
            }
        });
    }
}

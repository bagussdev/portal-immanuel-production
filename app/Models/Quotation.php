<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'quotation_number', 'client_id', 'bank_detail_id', 'user_id', 'quotation_date', 'event_date',
        'event_name', 'description', 'subtotal', 'discount_percent', 'discount',
        'tax_percent', 'tax_value', 'grand_total', 'loading_date', 'bongkaran_date',
        'status', 'converted_to_invoice', 'location_event', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'quotation_date' => 'date', 'event_date' => 'date', 'loading_date' => 'datetime',
        'bongkaran_date' => 'datetime', 'approved_at' => 'datetime',
        'converted_to_invoice' => 'boolean', 'discount_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            if ($quotation->quotation_number) {
                return;
            }
            $prefix = 'IMP/'.now()->format('m/y').'/QTN';
            $last = static::withTrashed()->where('quotation_number', 'like', $prefix.'%')
                ->orderByDesc('quotation_number')->value('quotation_number');
            $next = $last ? ((int) str($last)->afterLast('QTN')->value() + 1) : 1;
            $quotation->quotation_number = sprintf('%s%04d', $prefix, $next);
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function bankDetail(): BelongsTo
    {
        return $this->belongsTo(BankDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}

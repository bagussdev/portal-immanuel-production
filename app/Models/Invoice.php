<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes;

    public const FLOW_INSTALL_TEARDOWN = 'install_teardown';

    public const FLOW_INSTALL_ONLY = 'install_only';

    public const FLOW_ONE_WAY = 'one_way';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_UNPAID = 'unpaid';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_PAID = 'paid';

    public const STATUS_OVERPAID = 'overpaid';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_VOID = 'void';

    protected $fillable = [
        'invoice_number', 'client_id', 'bank_detail_id', 'quotation_id', 'event_name', 'location_event',
        'event_date', 'issue_date', 'due_date', 'loading_date', 'bongkaran_date', 'work_flow',
        'status', 'subtotal', 'discount_percent', 'discount_value', 'tax_percent',
        'tax_value', 'grand_total', 'total_paid', 'balance_due', 'notes', 'operational_notes', 'created_by',
        'issued_at', 'issued_by', 'voided_at', 'voided_by', 'void_reason',
        'schedule_reminded_at',
    ];

    protected $casts = [
        'event_date' => 'date', 'issue_date' => 'date', 'due_date' => 'date',
        'loading_date' => 'datetime', 'bongkaran_date' => 'datetime',
        'discount_percent' => 'decimal:2', 'tax_percent' => 'decimal:2',
        'issued_at' => 'datetime', 'voided_at' => 'datetime',
        'schedule_reminded_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function bankDetail(): BelongsTo
    {
        return $this->belongsTo(BankDetail::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function fieldJob()
    {
        return $this->hasOne(FieldJob::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function nextNumber(): string
    {
        $prefix = 'IMP/'.now()->format('m/y').'/INV';
        $last = static::withTrashed()->where('invoice_number', 'like', $prefix.'%')
            ->lockForUpdate()->orderByDesc('invoice_number')->value('invoice_number');
        $next = $last ? ((int) Str::afterLast($last, 'INV') + 1) : 1;

        return sprintf('%s%04d', $prefix, $next);
    }

    public function recalcTotalsAndStatus(): void
    {
        $subtotal = (int) $this->items()->sum('total');
        $discount = min(max((int) $this->discount_value, 0), $subtotal);
        if ($this->discount_percent !== null) {
            $discount = min((int) round($subtotal * ((float) $this->discount_percent / 100)), $subtotal);
        }

        $afterDiscount = max($subtotal - $discount, 0);
        $tax = min(max((int) $this->tax_value, 0), $afterDiscount);
        if ($this->tax_percent !== null) {
            $tax = min((int) round($afterDiscount * ((float) $this->tax_percent / 100)), $afterDiscount);
        }

        $grandTotal = max($afterDiscount - $tax, 0);

        $this->payments()
            ->whereNull('voided_at')
            ->get()
            ->each(function (InvoicePayment $payment) use ($grandTotal): void {
                $payment->forceFill([
                    'percent' => $grandTotal > 0
                        ? min(round(((int) $payment->amount / $grandTotal) * 100, 2), 999.99)
                        : null,
                ])->saveQuietly();
            });

        $paid = (int) $this->payments()->whereNull('voided_at')->sum('amount');
        $balance = max($grandTotal - $paid, 0);
        $status = $this->status;

        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_VOID], true)) {
            $status = match (true) {
                $paid > $grandTotal => self::STATUS_OVERPAID,
                $grandTotal > 0 && $paid === $grandTotal => self::STATUS_PAID,
                $paid > 0 => self::STATUS_PARTIAL,
                $this->due_date && today()->gt($this->due_date) => self::STATUS_OVERDUE,
                default => self::STATUS_UNPAID,
            };
        }

        $this->forceFill([
            'subtotal' => $subtotal,
            'discount_value' => $discount,
            'tax_value' => $tax,
            'grand_total' => $grandTotal,
            'total_paid' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ])->saveQuietly();
    }

    public function workFlowLabel(): string
    {
        return match ($this->work_flow) {
            self::FLOW_INSTALL_ONLY => 'Pasang saja',
            self::FLOW_ONE_WAY => 'Sekali jalan',
            default => 'Pasang & Bongkar',
        };
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_UNPAID, self::STATUS_PARTIAL, self::STATUS_OVERDUE]);
    }
}

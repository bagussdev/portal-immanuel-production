<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'payroll_period_id',
        'user_id',
        'status',
        'total_base',
        'total_deductions',
        'net_pay',
        'notes',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'total_base' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function scopeInPeriod($q, int $periodId)
    {
        return $q->where('payroll_period_id', $periodId);
    }

    public function recalcTotals(): void
    {
        $base = (float) $this->items()->where('type', PayrollItem::TYPE_BASE)->sum('amount');
        $ded = (float) $this->items()->where('type', PayrollItem::TYPE_DEDUCTION)->sum('amount');

        $this->total_base = $base;
        $this->total_deductions = $ded;
        $this->net_pay = $base - $ded;
        $this->save();
    }

    public function addBase(string $name, float $amount): PayrollItem
    {
        $item = $this->items()->create([
            'type' => PayrollItem::TYPE_BASE,
            'name' => $name,
            'amount' => $amount,
        ]);

        return $item;
    }

    public function addDeduction(string $name, float $amount): PayrollItem
    {
        $item = $this->items()->create([
            'type' => PayrollItem::TYPE_DEDUCTION,
            'name' => $name,
            'amount' => $amount,
        ]);

        return $item;
    }

    public function markPaid(?int $userId = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'paid_by' => $userId,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    /** ===== Status enum ===== */
    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_REOPEN = 'reopen';

    protected $fillable = [
        'month',
        'year',
        'status',
        'open_by',
        'open_at',
        'closed_by',
        'closed_at',
        'reopened_by',
        'reopened_at',
    ];

    protected $casts = [
        'open_at' => 'datetime',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

    /** ===== Relationships ===== */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'open_by')->withDefault();
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by')->withDefault();
    }

    public function reopenedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopened_by')->withDefault();
    }

    /** ===== Scopes ===== */
    public function scopeForMonthYear($q, int $month, int $year)
    {
        return $q->where('month', $month)->where('year', $year);
    }

    public function scopeOpen($q)
    {
        return $q->where('status', self::STATUS_OPEN);
    }

    public function scopeClosed($q)
    {
        return $q->where('status', self::STATUS_CLOSED);
    }

    public function scopeReopen($q)
    {
        return $q->where('status', self::STATUS_REOPEN);
    }

    /** Periode aktif untuk input/edit (open atau reopen) */
    public function scopeActive($q)
    {
        return $q->whereIn('status', [self::STATUS_OPEN, self::STATUS_REOPEN]);
    }

    /** ===== Helpers ===== */
    public function label(): string
    {
        return sprintf('%02d/%d', (int) $this->month, (int) $this->year);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isReopen(): bool
    {
        return $this->status === self::STATUS_REOPEN;
    }

    /** aktif untuk input/edit */
    public function allowsEditing(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_REOPEN], true);
    }

    /** ===== State transitions ===== */

    /** Set ke OPEN (pembukaan awal) */
    public function markOpened(?int $userId = null): void
    {
        $this->update([
            'status' => self::STATUS_OPEN,
            'open_by' => $userId ?? $this->open_by,
            'open_at' => $this->open_at ?? now(),
        ]);
    }

    /** Tutup periode (CLOSED) */
    public function markClosed(?int $userId = null): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);
    }

    /** Reopen periode (status = REOPEN) */
    public function markReopened(?int $userId = null): void
    {
        $this->update([
            'status' => self::STATUS_REOPEN,
            'reopened_by' => $userId,
            'reopened_at' => now(),
        ]);
    }
}

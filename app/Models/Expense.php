<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Expense extends Model
{
    private const SERIES_PREFIX = 'IMP';

    private const SERIES_CODE = 'EXP';

    private const SEQ_PAD = 4;

    protected $fillable = [
        'expense_number',
        'expense_date',
        'name',
        'qty',
        'total',
        'notes',
        'created_by',
        'attachment',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'qty' => 'integer',
        'total' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Expense $m) {
            if (empty($m->expense_date)) {
                $m->expense_date = now()->toDateString();
            }
            if (empty($m->created_by) && Auth::check()) {
                $m->created_by = Auth::id();
            }

            // Guard period (tidak auto-create untuk bulan lampau)
            static::guardClosedForDate($m->expense_date);

            // Generate nomor berdasar bulan/tahun SAAT DIBUAT (now)
            if (empty($m->expense_number)) {
                $prefix = self::makePrefix(now());
                $tries = 0;
                do {
                    $seq = self::nextSequenceForPrefix($prefix);
                    $m->expense_number = $prefix.str_pad((string) $seq, self::SEQ_PAD, '0', STR_PAD_LEFT);
                    $exists = static::where('expense_number', $m->expense_number)->exists();
                } while ($exists && ++$tries < 3);
            }
        });

        static::updating(function (Expense $m) {
            static::guardClosedForDate($m->expense_date);
        });
    }

    private static function makePrefix(Carbon $dt): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            self::SERIES_PREFIX,
            $dt->format('m'),
            $dt->format('y'),
            self::SERIES_CODE
        );
    }

    private static function nextSequenceForPrefix(string $prefix): int
    {
        $last = static::where('expense_number', 'like', $prefix.'%')
            ->orderByDesc('expense_number')
            ->value('expense_number');

        if (! $last) {
            return 1;
        }
        $tail = substr($last, -self::SEQ_PAD);
        $num = ctype_digit($tail) ? (int) $tail : 0;

        return $num + 1;
    }

    /** Guard: blok kalau period belum dibuka (past month) atau CLOSED */
    protected static function guardClosedForDate($date): void
    {
        $dt = $date instanceof Carbon ? $date : Carbon::parse($date);
        $now = now();

        $period = ExpensePeriod::ofYm((int) $dt->year, (int) $dt->month)->first();
        $isCurrent = ((int) $dt->year === (int) $now->year) && ((int) $dt->month === (int) $now->month);

        if (! $period) {
            if ($isCurrent) {
                // bulan berjalan boleh otomatis dibuat/ditandai OPEN
                ExpensePeriod::ensureOpen((int) $dt->year, (int) $dt->month);
            } else {
                throw ValidationException::withMessages([
                    'expense_date' => 'Periode bulan tersebut belum dibuka. Reopen terlebih dahulu.',
                ]);
            }
        } else {
            if ($period->isClosed()) {
                throw ValidationException::withMessages([
                    'expense_date' => 'Periode bulan tersebut sudah ditutup (CLOSED).',
                ]);
            }
        }
    }

    // Relasi
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor
    public function getTotalRpAttribute(): string
    {
        return 'Rp '.number_format((int) $this->total, 0, ',', '.');
    }

    public function getPeriodYmAttribute(): string
    {
        return Carbon::parse($this->expense_date)->format('m/Y');
    }

    // Scopes
    public function scopeExpenseDateBetween($query, $start, $end)
    {
        return $query->whereBetween('expense_date', [
            Carbon::parse($start)->toDateString(),
            Carbon::parse($end)->toDateString(),
        ]);
    }

    public function scopeOfMonthYear($query, int $month, int $year)
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth();

        return $query->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()]);
    }
}

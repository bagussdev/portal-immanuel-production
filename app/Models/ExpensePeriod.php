<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExpensePeriod extends Model
{
    public const STATUS_OPEN = 'OPEN';

    public const STATUS_REOPEN = 'REOPEN';

    public const STATUS_CLOSED = 'CLOSED';

    protected $fillable = [
        'month',
        'year',
        'status',
        'opened_at',
        'reopened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'reopened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /* ===================== Scopes ===================== */

    /**
     * BUMP:
     * - Close semua period status OPEN yang sudah lewat bulan berjalan.
     * - Pastikan period bulan berjalan ada (kalau belum ada → OPEN).
     *
     * @param  bool  $closeReopen  true jika ingin menutup REOPEN juga (default: false)
     * @return array{closed:int, opened:bool, year:int, month:int}
     */
    public static function bump(bool $closeReopen = false): array
    {
        $now = now(); // pakai timezone app
        $curY = (int) $now->year;
        $curM = (int) $now->month;

        // Tutup semua period masa lalu sesuai opsi
        $statuses = $closeReopen ? ['OPEN', 'REOPEN'] : ['OPEN'];

        $closed = static::query()
            ->whereIn('status', $statuses)
            ->where(function ($q) use ($curY, $curM) {
                $q->where('year', '<', $curY)
                    ->orWhere(function ($qq) use ($curY, $curM) {
                        $qq->where('year', $curY)->where('month', '<', $curM);
                    });
            })
            ->update([
                'status' => 'CLOSED',
                'closed_at' => $now,
                'updated_at' => $now,
            ]);

        // Pastikan period bulan berjalan ada; jika belum → OPEN
        // NOTE: sesuaikan urutan param ofYm dgn punyamu: (month, year) atau (year, month).
        $current = static::ofYm($curM, $curY)->first(); // <— pakai (month, year) sesuai controller awalmu
        $opened = false;

        if (! $current) {
            static::create([
                'year' => $curY,
                'month' => $curM,
                'status' => 'OPEN',
                'opened_at' => $now,
            ]);
            $opened = true;
        }

        return ['closed' => (int) $closed, 'opened' => $opened, 'year' => $curY, 'month' => $curM];
    }

    /** Filter berdasar (tahun, bulan) */
    public function scopeOfYm($q, int $year, int $month)
    {
        return $q->where('year', $year)->where('month', $month);
    }

    /** Period bulan berjalan (pakai timezone app config) */
    public function scopeCurrent($q)
    {
        $now = now();

        return $q->where('year', (int) $now->year)->where('month', (int) $now->month);
    }

    /* ===================== Status helpers ===================== */

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isReopen(): bool
    {
        return $this->status === self::STATUS_REOPEN;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /** Label singkat, mis. "08/2025" */
    public function label(): string
    {
        return str_pad((string) $this->month, 2, '0', STR_PAD_LEFT).'/'.$this->year;
    }

    /* ===================== Factory helpers ===================== */

    /**
     * Ambil/buat period berdasarkan tanggal (string/Carbon).
     * Default status OPEN dan set opened_at bila belum terisi.
     */
    public static function forDate($date): self
    {
        $dt = $date instanceof Carbon ? $date : Carbon::parse($date);
        $p = static::firstOrCreate(
            ['year' => (int) $dt->year, 'month' => (int) $dt->month],
            ['status' => self::STATUS_OPEN, 'opened_at' => now()]
        );

        if ($p->opened_at === null && ($p->isOpen() || $p->isReopen())) {
            $p->opened_at = now();
            $p->save();
        }

        return $p;
    }

    /**
     * Pastikan period (tahun, bulan) ada & berstatus OPEN (tidak memaksa jika REOPEN/CLOSED).
     */
    public static function ensureOpen(int $year, int $month): self
    {
        $p = static::firstOrCreate(
            ['year' => $year, 'month' => $month],
            ['status' => self::STATUS_OPEN, 'opened_at' => now()]
        );

        if ($p->isClosed()) {
            return $p; // kalau CLOSED, jangan auto-buka tanpa aksi admin
        }

        if ($p->status !== self::STATUS_OPEN) {
            $p->status = self::STATUS_OPEN;
            $p->opened_at = $p->opened_at ?? now();
            $p->reopened_at = null;
            $p->closed_at = null;
            $p->save();
        }

        return $p;
    }

    /**
     * Menutup semua period yang lebih lama dari (year, month).
     * Hanya menutup yang masih OPEN; REOPEN tidak disentuh.
     *
     * @return int jumlah row yang di-update
     */
    public static function closeOlderThan(int $year, int $month): int
    {
        return static::query()
            ->where('status', self::STATUS_OPEN)
            ->where(function ($q) use ($year, $month) {
                $q->where('year', '<', $year)
                    ->orWhere(function ($qq) use ($year, $month) {
                        $qq->where('year', $year)->where('month', '<', $month);
                    });
            })
            ->update([
                'status' => self::STATUS_CLOSED,
                'closed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /* ===================== Mutators (actions) ===================== */

    /** Paksa OPEN (jarang dipakai; normalnya otomatis saat bulan berjalan) */
    public function open(): void
    {
        $this->status = self::STATUS_OPEN;
        $this->opened_at = $this->opened_at ?? now();
        $this->reopened_at = null;
        $this->closed_at = null;
        $this->save();
    }

    /** Reopen dari CLOSED → REOPEN (mengizinkan edit lagi) */
    public function reopen(): void
    {
        $this->status = self::STATUS_REOPEN;
        $this->reopened_at = now();
        $this->closed_at = null;
        $this->save();
    }

    /** Close dari OPEN/REOPEN → CLOSED (mengunci periode) */
    public function close(): void
    {
        $this->status = self::STATUS_CLOSED;
        $this->closed_at = now();
        $this->save();
    }
}

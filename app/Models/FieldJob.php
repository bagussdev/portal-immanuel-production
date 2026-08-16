<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FieldJob extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'invoice_id', 'job_number', 'client_name', 'event_name', 'location',
        'event_date', 'loading_date', 'teardown_date', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'loading_date' => 'datetime',
        'teardown_date' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FieldJobItem::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(FieldJobStage::class);
    }

    public function activeStages(): HasMany
    {
        return $this->stages()->where('is_active', true);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canViewAllFieldJobs()) {
            return $query;
        }

        return $query->whereHas('stages.assignees', fn (Builder $assignees) => $assignees->whereKey($user->id));
    }

    public static function nextNumber(): string
    {
        $prefix = 'JOB/'.now()->format('m/y').'/';
        $last = static::query()->where('job_number', 'like', $prefix.'%')
            ->lockForUpdate()->orderByDesc('job_number')->value('job_number');
        $next = $last ? ((int) Str::afterLast($last, '/') + 1) : 1;

        return sprintf('%s%03d', $prefix, $next);
    }

    public function recalculateStatus(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return;
        }

        $statuses = $this->activeStages()->pluck('status');
        $status = match (true) {
            $statuses->isNotEmpty() && $statuses->every(fn (string $value) => $value === FieldJobStage::STATUS_COMPLETED) => self::STATUS_COMPLETED,
            $statuses->contains(FieldJobStage::STATUS_IN_PROGRESS),
            $statuses->contains(FieldJobStage::STATUS_COMPLETED) => self::STATUS_IN_PROGRESS,
            default => self::STATUS_PENDING,
        };

        $this->forceFill(['status' => $status])->saveQuietly();
    }
}

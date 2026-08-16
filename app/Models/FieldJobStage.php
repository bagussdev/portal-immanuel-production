<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldJobStage extends Model
{
    public const TYPE_INSTALL = 'install';

    public const TYPE_TEARDOWN = 'teardown';

    public const TYPE_ONE_WAY = 'one_way';

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'field_job_id', 'type', 'scheduled_at', 'status', 'is_active', 'notes',
        'started_at', 'started_by', 'completed_at', 'completed_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function fieldJob(): BelongsTo
    {
        return $this->belongsTo(FieldJob::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'field_job_stage_user')
            ->withPivot('assigned_by')->withTimestamps();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FieldJobPhoto::class);
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function label(): string
    {
        return match ($this->type) {
            self::TYPE_INSTALL => 'Pasang / Loading',
            self::TYPE_TEARDOWN => 'Bongkar',
            self::TYPE_ONE_WAY => 'Sekali Jalan',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}

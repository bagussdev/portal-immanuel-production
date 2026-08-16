<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Armada extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_AVAILABLE = 'Available';

    public const STATUS_IN_USE = 'Dipakai';

    public const STATUS_MAINTENANCE = 'Maintenance';

    public const STATUS_DAMAGED = 'Rusak';

    protected $table = 'armada';

    protected $fillable = [
        'name', 'type', 'year', 'nomor_rangka', 'nomor_mesin', 'nomor_polisi',
        'qr_pertamina', 'foto_depan', 'foto_belakang', 'foto_samping',
        'stnk_expired', 'stnk_renewed_at', 'stnk_attachment', 'location_id',
        'user_id', 'notes', 'brand', 'model', 'status',
    ];

    protected $casts = ['stnk_expired' => 'date', 'stnk_renewed_at' => 'date', 'year' => 'integer'];

    protected $appends = ['document_status'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(ArmadaStnkRenewal::class);
    }

    public function getDocumentStatusAttribute(): string
    {
        if (! $this->stnk_expired) {
            return 'unknown';
        }
        if ($this->stnk_expired->isPast()) {
            return 'overdue';
        }

        return today()->diffInDays($this->stnk_expired, false) <= 30 ? 'due_soon' : 'safe';
    }
}

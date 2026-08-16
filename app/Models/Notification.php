<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    protected $fillable = ['type', 'data', 'user_id', 'read_at'];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /** Relations */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'notification_roles')->withTimestamps();
    }

    /** Accessors (ambil dari JSON 'data') */
    public function getTitleAttribute(): ?string
    {
        return data_get($this->data, 'title');
    }

    public function getMessageAttribute(): ?string
    {
        return data_get($this->data, 'message');
    }

    public function getLinkAttribute(): ?string
    {
        return data_get($this->data, 'link');
    }

    public function getIconAttribute(): ?string
    {
        return data_get($this->data, 'icon');
    }

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }

    /** Mutations */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->read_at = now();
            $this->save();
        }
    }

    /** Scopes */
    public function scopeUnread(Builder $q): Builder
    {
        return $q->whereNull('read_at');
    }

    public function scopeRead(Builder $q): Builder
    {
        return $q->whereNotNull('read_at');
    }

    public function scopeCreatedSince(Builder $q, $since): Builder
    {
        $ts = $since instanceof Carbon ? $since : Carbon::parse($since);

        return $q->where('created_at', '>=', $ts);
    }

    public function scopeUpdatedSince(Builder $q, $since): Builder
    {
        $ts = $since instanceof Carbon ? $since : Carbon::parse($since);

        return $q->where('updated_at', '>=', $ts);
    }

    /**
     * Role-target yang DIIZINKAN (allow-list):
     * lolos jika EXISTS preferensi allowed=1 untuk (role_id,type) milik user.
     */
    public function scopeTargetedToRolesAllowed(Builder $q, array $roleIds): Builder
    {
        if (empty($roleIds)) {
            return $q->whereRaw('1=0');
        }

        return $q->whereExists(function ($sub) use ($roleIds) {
            $sub->select(DB::raw(1))
                ->from('notification_roles as nr')
                ->join('notification_preferences as np', function ($j) {
                    $j->on('np.role_id', '=', 'nr.role_id')
                        ->on('np.type', '=', 'notifications.type')
                        ->where('np.allowed', '=', 1);
                })
                ->whereColumn('nr.notification_id', 'notifications.id')
                ->whereIn('nr.role_id', $roleIds);
        });
    }

    /** Notifikasi personal (selalu lolos) */
    public function scopePersonalTo(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /**
     * Gabungan: personal OR role-target (allow-list).
     */
    public function scopeForUserWithRoles(Builder $q, int $userId, array $roleIds): Builder
    {
        return $q->where(function (Builder $w) use ($userId, $roleIds) {
            $w->where('user_id', $userId);

            if (! empty($roleIds)) {
                $w->orWhere(function (Builder $r) use ($roleIds) {
                    $r->whereNull('user_id') // hanya role-target
                        ->targetedToRolesAllowed($roleIds);
                });
            }
        });
    }

    /** Terbaru limit (dropdown) */
    public function scopeLatestLimit(Builder $q, int $limit = 10): Builder
    {
        return $q->orderByDesc('created_at')->orderByDesc('id')->limit($limit);
    }
}

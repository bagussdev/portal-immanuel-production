<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'no_telf',
        'profile_photo_path',
        'ktp_photo_path',
        'role_id',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->role ? $this->role->permissions : collect();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->pluck('name')->contains($permission);
    }

    public function roleName(): string
    {
        return strtolower((string) optional($this->role)->name);
    }

    public function isMaster(): bool
    {
        return $this->roleName() === 'master';
    }

    public function isAdmin(): bool
    {
        return $this->roleName() === 'admin';
    }

    public function isMandor(): bool
    {
        return $this->roleName() === 'mandor';
    }

    public function canViewAllPayrolls(): bool
    {
        return in_array($this->roleName(), ['master', 'admin', 'mandor'], true);
    }

    public function canManageAllFieldJobs(): bool
    {
        return $this->isMaster()
            || (in_array($this->roleName(), ['admin', 'mandor'], true) && $this->hasPermission('managefieldjobs'));
    }

    public function canViewAllFieldJobs(): bool
    {
        return $this->isMaster() || in_array($this->roleName(), ['admin', 'mandor'], true);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

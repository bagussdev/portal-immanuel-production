<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArmadaStnkRenewal extends Model
{
    protected $fillable = [
        'armada_id', 'processed_at', 'previous_expired_at', 'new_expired_at',
        'cost', 'attachment', 'notes', 'created_by',
    ];

    protected $casts = [
        'processed_at' => 'date', 'previous_expired_at' => 'date',
        'new_expired_at' => 'date', 'cost' => 'integer',
    ];

    public function armada(): BelongsTo
    {
        return $this->belongsTo(Armada::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

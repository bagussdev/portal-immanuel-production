<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldJobPhoto extends Model
{
    protected $fillable = [
        'field_job_stage_id', 'path', 'original_name', 'mime_type',
        'size_bytes', 'caption', 'uploaded_by',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(FieldJobStage::class, 'field_job_stage_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

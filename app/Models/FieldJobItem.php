<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldJobItem extends Model
{
    protected $fillable = [
        'field_job_id', 'field_job_site_id', 'invoice_item_id', 'item_name', 'qty', 'length', 'work_flow',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'length' => 'decimal:2',
    ];

    public function fieldJob(): BelongsTo
    {
        return $this->belongsTo(FieldJob::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(FieldJobSite::class, 'field_job_site_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldJobSite extends Model
{
    protected $fillable = ['field_job_id', 'invoice_location_id', 'name', 'event_start_date', 'event_end_date', 'loading_date', 'teardown_date', 'work_flow', 'sort_order'];

    protected $casts = ['event_start_date' => 'date', 'event_end_date' => 'date', 'loading_date' => 'datetime', 'teardown_date' => 'datetime'];

    public function fieldJob(): BelongsTo { return $this->belongsTo(FieldJob::class); }

    public function invoiceLocation(): BelongsTo { return $this->belongsTo(InvoiceLocation::class); }

    public function items(): HasMany { return $this->hasMany(FieldJobItem::class); }

    public function stages(): HasMany { return $this->hasMany(FieldJobStage::class); }
}

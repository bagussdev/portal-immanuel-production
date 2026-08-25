<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationLocation extends Model
{
    protected $fillable = ['quotation_id', 'name', 'event_start_date', 'event_end_date', 'loading_date', 'teardown_date', 'work_flow', 'sort_order'];

    protected $casts = ['event_start_date' => 'date', 'event_end_date' => 'date', 'loading_date' => 'datetime', 'teardown_date' => 'datetime'];

    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }

    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
}

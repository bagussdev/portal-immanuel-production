<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceLocation extends Model
{
    protected $fillable = ['invoice_id', 'quotation_location_id', 'name', 'event_start_date', 'event_end_date', 'loading_date', 'teardown_date', 'work_flow', 'sort_order'];

    protected $casts = ['event_start_date' => 'date', 'event_end_date' => 'date', 'loading_date' => 'datetime', 'teardown_date' => 'datetime'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }

    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }

    public function quotationLocation(): BelongsTo { return $this->belongsTo(QuotationLocation::class); }
}

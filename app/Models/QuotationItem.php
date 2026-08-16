<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_name',
        'qty',
        'length',
        'unit_price',
        'total',
        'price_group',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'length' => 'decimal:2',
        'unit_price' => 'integer',
        'total' => 'integer',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}

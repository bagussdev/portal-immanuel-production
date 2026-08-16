<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'brand',
        'model',
        'serial_number',
        'qty',
        'status',
        'location',
        'photo',
        'notes',
        'created_by',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'location');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

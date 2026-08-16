<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $table = 'gudang';

    protected $fillable = [
        'name',
        'site_code',
        'location',
        'since',
    ];

    // Relasi ke Equipment (jika satu gudang punya banyak equipment)
    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'location');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGudangTable extends Migration
{
    public function up()
    {
        Schema::create('gudang', function (Blueprint $table) {
            $table->id(); // id
            $table->string('name'); // nama gudang
            $table->string('site_code')->unique(); // kode unik gudang
            $table->string('location')->nullable(); // alamat / lokasi
            $table->date('since')->nullable(); // tanggal mulai aktif
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gudang');
    }
}

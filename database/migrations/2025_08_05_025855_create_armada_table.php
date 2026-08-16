<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmadaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('armada', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama unit kendaraan
            $table->string('type'); // Jenis kendaraan, misal: pick-up, truck, mobil box, dll
            $table->string('brand');
            $table->string('model');
            $table->year('year'); // Tahun pembuatan
            $table->string('nomor_rangka')->unique(); // Nomor rangka kendaraan
            $table->string('nomor_mesin')->unique(); // Nomor mesin kendaraan
            $table->string('nomor_polisi')->unique(); // Plat nomor kendaraan

            $table->string('qr_pertamina')->nullable(); // Foto QR MyPertamina (path file)
            $table->string('foto_depan')->nullable(); // Foto tampak depan
            $table->string('foto_belakang')->nullable(); // Foto tampak belakang
            $table->string('foto_samping')->nullable(); // Foto tampak samping

            $table->date('stnk_expired')->nullable(); // Tanggal STNK habis
            $table->date('stnk_renewed_at')->nullable(); // Tanggal STNK terakhir diperpanjang
            $table->string('stnk_attachment')->nullable(); // Lampiran STNK terbaru (pdf/jpg/png)

            $table->foreignId('location_id')->constrained('gudang')->restrictOnDelete(); // Lokasi armada (gudang)
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->string('status', 24)->default('Available');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('armada');
    }
}

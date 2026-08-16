<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // tipe notif, mis. "invoice_due", "quotation_approved"
            $table->string('type', 100)->index();

            // payload fleksibel (title, message, link, icon, dll.)
            $table->json('data');

            // jika personal ke user tertentu
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            // penanda sudah dibaca
            $table->timestamp('read_at')->nullable()->index();

            $table->timestamps();

            // index tambahan untuk polling & urutan terbaru
            $table->index(['created_at', 'id']);
            $table->index('updated_at');
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

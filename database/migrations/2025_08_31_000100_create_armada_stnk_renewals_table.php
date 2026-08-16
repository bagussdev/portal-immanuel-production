<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('armada_stnk_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('armada_id')->constrained('armada')->cascadeOnDelete();
            $table->date('processed_at');
            $table->date('previous_expired_at')->nullable();
            $table->date('new_expired_at');
            $table->unsignedBigInteger('cost')->default(0);
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('armada_stnk_renewals');
    }
};

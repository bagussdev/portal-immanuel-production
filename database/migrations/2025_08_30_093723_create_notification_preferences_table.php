<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('type', 100);
            $table->boolean('allowed')->default(true);

            $table->timestamps();

            // 1 role hanya 1 baris per tipe
            $table->unique(['role_id', 'type']);
            $table->index(['type', 'allowed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_schedule_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('type', 20);
            $table->date('scheduled_for');
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(['invoice_id', 'type', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_schedule_reminders');
    }
};

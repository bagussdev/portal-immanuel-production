<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $t->dateTime('paid_at');
            $t->string('method', 50);
            $t->string('reference')->nullable();
            $t->unsignedBigInteger('amount');
            $t->decimal('percent', 5, 2)->nullable();
            $t->string('attachment')->nullable(); // slip pembayaran
            $t->text('notes')->nullable();
            $t->foreignId('received_by')->constrained('users');
            $t->timestamp('voided_at')->nullable();
            $t->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('void_reason')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};

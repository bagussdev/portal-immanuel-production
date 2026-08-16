<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            // Draft belum memiliki nomor resmi. Nomor dibuat saat invoice diterbitkan.
            $t->string('invoice_number')->nullable()->unique();
            $t->foreignId('client_id')->constrained('clients');
            $t->foreignId('quotation_id')->nullable()->constrained('quotations');
            $t->string('event_name')->nullable();
            $t->string('location_event')->nullable();
            $t->date('event_date')->nullable();
            $t->date('issue_date')->nullable();
            $t->date('due_date')->nullable();
            $t->dateTime('loading_date')->nullable();
            $t->dateTime('bongkaran_date')->nullable();
            $t->string('status', 24)->default('draft');

            // angka utama
            $t->unsignedBigInteger('subtotal')->default(0);
            $t->decimal('discount_percent', 5, 2)->nullable();
            $t->unsignedBigInteger('discount_value')->default(0);
            // Pajak adalah potongan pajak (mengurangi tagihan), seperti diskon.
            $t->decimal('tax_percent', 5, 2)->nullable();
            $t->unsignedBigInteger('tax_value')->default(0);
            $t->unsignedBigInteger('grand_total')->default(0);
            $t->unsignedBigInteger('total_paid')->default(0);
            $t->unsignedBigInteger('balance_due')->default(0);

            $t->text('notes')->nullable();
            $t->foreignId('created_by')->constrained('users');
            $t->timestamp('issued_at')->nullable();
            $t->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('voided_at')->nullable();
            $t->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('void_reason')->nullable();
            $t->timestamp('schedule_reminded_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

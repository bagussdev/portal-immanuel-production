<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('quotation_date')->nullable();
            $table->date('event_date')->nullable();
            $table->text('description')->nullable();

            // Ringkasan keuangan. Pajak di sistem ini adalah potongan, bukan penambah.
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->unsignedBigInteger('discount')->default(0);
            $table->decimal('tax_percent', 5, 2)->nullable();
            $table->unsignedBigInteger('tax_value')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);

            $table->string('status', 24)->default('draft');
            $table->boolean('converted_to_invoice')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

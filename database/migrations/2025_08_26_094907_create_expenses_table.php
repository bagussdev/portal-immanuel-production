<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->string('expense_number', 50)->unique(); // ex: IMP/08/25/EXP0001
            $table->date('expense_date');                   // tanggal kejadian (basis period)
            $table->string('name', 255);                    // nama pengeluaran
            $table->unsignedInteger('qty')->default(1);     // minimal 1 (sesuai validasi)
            $table->unsignedBigInteger('total')->default(0); // rupiah, tanpa desimal

            $table->text('notes')->nullable();
            $table->string('attachment', 255)->nullable();  // path storage/public/expenses/...

            // pembuat
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Index pendukung pencarian/filter
            $table->index('expense_date');
            $table->index('created_by');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Periode gajian (unik per bulan/tahun)
        Schema::create('payroll_periods', function (Blueprint $t) {
            $t->id();
            $t->unsignedTinyInteger('month');   // 1..12
            $t->unsignedSmallInteger('year');   // 2000+
            $t->string('status', 16)->default('open'); // open|closed|reopen
            $t->foreignId('open_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('open_at')->nullable();
            $t->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('closed_at')->nullable();
            $t->foreignId('reopened_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reopened_at')->nullable();
            $t->timestamps();

            $t->unique(['month', 'year'], 'uniq_payroll_period');
            $t->index(['year', 'month']);
        });

        // 2) Slip gaji per user per periode (ringkasan disimpan di sini)
        Schema::create('payrolls', function (Blueprint $t) {
            $t->id();
            $t->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            $t->string('status', 16)->default('draft'); // draft|paid

            // Ringkasan (cache) — semua angka berasal dari payroll_items
            $t->decimal('total_base', 15, 2)->default(0); // sum(items where type=base)
            $t->decimal('total_deductions', 15, 2)->default(0); // sum(items where type=deduction)
            $t->decimal('net_pay', 15, 2)->default(0); // total_base - total_deductions

            $t->text('notes')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->unique(['payroll_period_id', 'user_id'], 'uniq_payroll_user_in_period');
            $t->index(['user_id', 'status']);
        });

        // 3) Rincian item (base & deduction) per slip
        Schema::create('payroll_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['base', 'deduction']); // base = gaji pokok/komponen pokok; deduction = potongan
            $t->string('name');                     // contoh: "Gaji Pokok", "Potongan Keterlambatan"
            $t->decimal('amount', 15, 2);          // positif; validasi di controller
            $t->timestamps();

            $t->index(['payroll_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('payroll_periods');
    }
};

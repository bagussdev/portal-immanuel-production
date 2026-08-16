<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');     // 1..12
            $table->unsignedSmallInteger('year');     // 2000..2100
            $table->string('status', 16)->default('OPEN'); // OPEN|REOPEN|CLOSED
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
            $table->index(['status', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_periods');
    }
};

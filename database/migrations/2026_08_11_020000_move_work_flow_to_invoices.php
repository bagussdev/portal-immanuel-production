<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('work_flow', 24)
                ->default('install_teardown')
                ->after('bongkaran_date')
                ->index();
        });

        DB::table('invoices')->select('id')->orderBy('id')->chunkById(200, function ($invoices) {
            foreach ($invoices as $invoice) {
                $flows = DB::table('invoice_items')
                    ->where('invoice_id', $invoice->id)
                    ->whereNotNull('work_flow')
                    ->distinct()
                    ->pluck('work_flow');

                $workFlow = $flows->count() === 1
                    ? (string) $flows->first()
                    : 'install_teardown';

                DB::table('invoices')->where('id', $invoice->id)->update([
                    'work_flow' => $workFlow,
                ]);
            }
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['work_flow']);
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('work_flow');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('work_flow', 24)
                ->default('install_teardown')
                ->after('price_group')
                ->index();
        });

        DB::table('invoices')->select(['id', 'work_flow'])->orderBy('id')->chunkById(200, function ($invoices) {
            foreach ($invoices as $invoice) {
                DB::table('invoice_items')->where('invoice_id', $invoice->id)->update([
                    'work_flow' => $invoice->work_flow ?: 'install_teardown',
                ]);
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['work_flow']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('work_flow');
        });
    }
};

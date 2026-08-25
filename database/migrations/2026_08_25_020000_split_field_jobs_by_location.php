<?php

use App\Models\FieldJob;
use App\Models\Invoice;
use App\Services\FieldJobSynchronizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('field_jobs', 'field_jobs_invoice_id_index')) {
            Schema::table('field_jobs', fn (Blueprint $table) => $table->index('invoice_id', 'field_jobs_invoice_id_index')
            );
        }

        if (Schema::hasIndex('field_jobs', 'field_jobs_invoice_id_unique')) {
            Schema::table('field_jobs', fn (Blueprint $table) => $table->dropUnique('field_jobs_invoice_id_unique')
            );
        }

        Invoice::query()->where('status', '!=', Invoice::STATUS_DRAFT)->orderBy('id')
            ->eachById(fn (Invoice $invoice) => app(FieldJobSynchronizer::class)->sync($invoice));

        if (! Schema::hasIndex('field_job_sites', 'field_job_sites_invoice_location_id_unique')) {
            Schema::table('field_job_sites', fn (Blueprint $table) => $table->unique('invoice_location_id', 'field_job_sites_invoice_location_id_unique')
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('field_job_sites', 'field_job_sites_invoice_location_id_unique')) {
            Schema::table('field_job_sites', fn (Blueprint $table) => $table->dropUnique('field_job_sites_invoice_location_id_unique')
            );
        }

        DB::table('field_jobs')->select('invoice_id')->distinct()->orderBy('invoice_id')
            ->each(function (object $row): void {
                $jobs = FieldJob::query()->where('invoice_id', $row->invoice_id)->orderBy('id')->get();
                $primary = $jobs->shift();
                if (! $primary) {
                    return;
                }

                foreach ($jobs as $job) {
                    DB::table('field_job_sites')->where('field_job_id', $job->id)->update(['field_job_id' => $primary->id]);
                    DB::table('field_job_items')->where('field_job_id', $job->id)->update(['field_job_id' => $primary->id]);
                    DB::table('field_job_stages')->where('field_job_id', $job->id)->update(['field_job_id' => $primary->id]);
                    $job->delete();
                }
            });

        if (Schema::hasIndex('field_jobs', 'field_jobs_invoice_id_index')) {
            Schema::table('field_jobs', fn (Blueprint $table) => $table->dropIndex('field_jobs_invoice_id_index')
            );
        }
        if (! Schema::hasIndex('field_jobs', 'field_jobs_invoice_id_unique')) {
            Schema::table('field_jobs', fn (Blueprint $table) => $table->unique('invoice_id', 'field_jobs_invoice_id_unique')
            );
        }
    }
};

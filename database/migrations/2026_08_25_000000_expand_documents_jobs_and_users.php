<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 40)->nullable()->unique()->after('name');
            $table->string('profile_photo_path')->nullable()->after('no_telf');
            $table->string('ktp_photo_path')->nullable()->after('profile_photo_path');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
            $table->timestamp('resolved_at')->nullable()->after('void_reason');
            $table->foreignId('resolved_by')->nullable()->after('resolved_at')->constrained('users')->nullOnDelete();
            $table->text('resolution_note')->nullable()->after('resolved_by');
        });

        Schema::create('quotation_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->dateTime('loading_date')->nullable();
            $table->dateTime('teardown_date')->nullable();
            $table->string('work_flow', 24)->default('install_teardown');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('invoice_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_location_id')->nullable()->constrained('quotation_locations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->dateTime('loading_date')->nullable();
            $table->dateTime('teardown_date')->nullable();
            $table->string('work_flow', 24)->default('install_teardown');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('quotation_location_id')->nullable()->after('quotation_id')->constrained('quotation_locations')->nullOnDelete();
            $table->string('pricing_mode', 16)->default('unit')->after('length');
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('invoice_location_id')->nullable()->after('invoice_id')->constrained('invoice_locations')->nullOnDelete();
            $table->string('pricing_mode', 16)->default('unit')->after('length');
        });

        Schema::create('field_job_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_location_id')->nullable()->constrained('invoice_locations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->dateTime('loading_date')->nullable();
            $table->dateTime('teardown_date')->nullable();
            $table->string('work_flow', 24)->default('install_teardown');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::table('field_job_items', function (Blueprint $table) {
            $table->foreignId('field_job_site_id')->nullable()->after('field_job_id')->constrained('field_job_sites')->nullOnDelete();
        });
        Schema::table('field_job_stages', function (Blueprint $table) {
            $table->dropUnique(['field_job_id', 'type']);
            $table->foreignId('field_job_site_id')->nullable()->after('field_job_id')->constrained('field_job_sites')->nullOnDelete();
            $table->unique(['field_job_site_id', 'type']);
        });

        $now = now();
        DB::table('quotations')->orderBy('id')->chunkById(200, function ($rows) use ($now) {
            foreach ($rows as $row) {
                $locationId = DB::table('quotation_locations')->insertGetId([
                    'quotation_id' => $row->id,
                    'name' => $row->location_event,
                    'event_start_date' => $row->event_date,
                    'event_end_date' => $row->event_end_date,
                    'loading_date' => $row->loading_date,
                    'teardown_date' => $row->bongkaran_date,
                    'work_flow' => 'install_teardown',
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('quotation_items')->where('quotation_id', $row->id)->update(['quotation_location_id' => $locationId]);
            }
        });
        DB::table('invoices')->orderBy('id')->chunkById(200, function ($rows) use ($now) {
            foreach ($rows as $row) {
                $locationId = DB::table('invoice_locations')->insertGetId([
                    'invoice_id' => $row->id,
                    'name' => $row->location_event,
                    'event_start_date' => $row->event_date,
                    'event_end_date' => $row->event_end_date,
                    'loading_date' => $row->loading_date,
                    'teardown_date' => $row->bongkaran_date,
                    'work_flow' => $row->work_flow ?: 'install_teardown',
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('invoice_items')->where('invoice_id', $row->id)->update(['invoice_location_id' => $locationId]);
            }
        });
        DB::table('field_jobs')->orderBy('id')->chunkById(200, function ($rows) use ($now) {
            foreach ($rows as $row) {
                $invoiceLocation = DB::table('invoice_locations')->where('invoice_id', $row->invoice_id)->orderBy('sort_order')->first();
                $siteId = DB::table('field_job_sites')->insertGetId([
                    'field_job_id' => $row->id,
                    'invoice_location_id' => $invoiceLocation?->id,
                    'name' => $row->location,
                    'event_start_date' => $row->event_date,
                    'event_end_date' => null,
                    'loading_date' => $row->loading_date,
                    'teardown_date' => $row->teardown_date,
                    'work_flow' => $invoiceLocation?->work_flow ?: 'install_teardown',
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('field_job_items')->where('field_job_id', $row->id)->update(['field_job_site_id' => $siteId]);
                DB::table('field_job_stages')->where('field_job_id', $row->id)->update(['field_job_site_id' => $siteId]);
            }
        });

        $permission = DB::table('permissions')->where('name', 'exportuserdata')->value('id');
        if (! $permission) {
            $permission = DB::table('permissions')->insertGetId(['name' => 'exportuserdata', 'created_at' => $now, 'updated_at' => $now]);
        }
        $master = DB::table('roles')->where('name', 'master')->value('id');
        if ($master) {
            DB::table('role_permissions')->insertOrIgnore(['role_id' => $master, 'permission_id' => $permission]);
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->where('name', 'exportuserdata')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::table('field_job_stages', function (Blueprint $table) {
            $table->dropUnique(['field_job_site_id', 'type']);
            $table->dropConstrainedForeignId('field_job_site_id');
            $table->unique(['field_job_id', 'type']);
        });
        Schema::table('field_job_items', fn (Blueprint $table) => $table->dropConstrainedForeignId('field_job_site_id'));
        Schema::dropIfExists('field_job_sites');
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_location_id');
            $table->dropColumn('pricing_mode');
        });
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quotation_location_id');
            $table->dropColumn('pricing_mode');
        });
        Schema::dropIfExists('invoice_locations');
        Schema::dropIfExists('quotation_locations');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('resolved_by');
            $table->dropColumn(['event_end_date', 'resolved_at', 'resolution_note']);
        });
        Schema::table('quotations', fn (Blueprint $table) => $table->dropColumn('event_end_date'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['username', 'profile_photo_path', 'ktp_photo_path']));
    }
};

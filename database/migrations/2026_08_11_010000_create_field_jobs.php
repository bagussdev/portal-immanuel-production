<?php

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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('work_flow', 24)->default('install_teardown')->after('price_group')->index();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('operational_notes')->nullable()->after('notes');
        });

        Schema::create('field_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->unique()->constrained('invoices')->cascadeOnDelete();
            $table->string('job_number', 32)->unique();
            $table->string('client_name');
            $table->string('event_name')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date')->nullable();
            $table->dateTime('loading_date')->nullable();
            $table->dateTime('teardown_date')->nullable();
            $table->string('status', 24)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('field_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained('invoice_items')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('qty', 10, 2);
            $table->decimal('length', 10, 2)->nullable();
            $table->string('work_flow', 24)->index();
            $table->timestamps();
        });

        Schema::create('field_job_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->string('type', 24);
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status', 24)->default('pending')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['field_job_id', 'type']);
        });

        Schema::create('field_job_stage_user', function (Blueprint $table) {
            $table->foreignId('field_job_stage_id')->constrained('field_job_stages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->primary(['field_job_stage_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('field_job_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_job_stage_id')->constrained('field_job_stages')->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size_bytes');
            $table->string('caption', 255)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();
        $permissionNames = ['fieldjobsmenu', 'managefieldjobs', 'updatefieldjobstatus', 'uploadfieldjobphotos'];
        foreach ($permissionNames as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id', 'name');
        $roleIds = DB::table('roles')->whereIn('name', ['master', 'admin', 'mandor', 'user'])->pluck('id', 'name');

        $operationalRoleIds = collect(['mandor', 'user'])->map(fn ($role) => $roleIds[$role] ?? null)->filter()->all();
        if ($operationalRoleIds) {
            DB::table('notification_preferences')
                ->whereIn('role_id', $operationalRoleIds)
                ->whereIn('type', ['quotation_submitted', 'quotation_approved', 'invoice_due'])
                ->delete();
        }

        foreach (['master', 'admin', 'mandor'] as $role) {
            foreach ($permissionNames as $permission) {
                if (isset($roleIds[$role], $permissionIds[$permission])) {
                    DB::table('role_permissions')->insertOrIgnore([
                        'role_id' => $roleIds[$role],
                        'permission_id' => $permissionIds[$permission],
                    ]);
                }
            }
        }

        foreach (['fieldjobsmenu', 'updatefieldjobstatus', 'uploadfieldjobphotos'] as $permission) {
            if (isset($roleIds['user'], $permissionIds[$permission])) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id' => $roleIds['user'],
                    'permission_id' => $permissionIds[$permission],
                ]);
            }
        }

        if (Schema::hasTable('invoices')) {
            Invoice::query()
                ->where('status', '!=', Invoice::STATUS_DRAFT)
                ->orderBy('id')
                ->eachById(fn (Invoice $invoice) => app(FieldJobSynchronizer::class)->sync($invoice));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('field_job_photos');
        Schema::dropIfExists('field_job_stage_user');
        Schema::dropIfExists('field_job_stages');
        Schema::dropIfExists('field_job_items');
        Schema::dropIfExists('field_jobs');
        Schema::table('invoice_items', fn (Blueprint $table) => $table->dropColumn('work_flow'));
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn('operational_notes'));

        if (Schema::hasTable('permissions') && Schema::hasTable('role_permissions')) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', ['fieldjobsmenu', 'managefieldjobs', 'updatefieldjobstatus', 'uploadfieldjobphotos'])
                ->pluck('id');
            DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
    }
};

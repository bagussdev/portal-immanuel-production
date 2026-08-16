<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('email')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('npwp', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('bank_detail_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('bank_detail_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
        });
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('price_group', 64)->nullable()->after('total')->index();
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('price_group', 64)->nullable()->after('total')->index();
        });

        $now = now();
        DB::table('bank_details')->insert([
            [
                'label' => 'Sugito', 'email' => 'zakhariadesaign30@gmail.com',
                'bank_name' => 'BCA', 'account_name' => 'Zakharia Sugito Kurniawan',
                'account_number' => '0490392947', 'npwp' => null,
                'phone' => '085654265943', 'active' => true, 'notes' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'label' => 'Yayak', 'email' => null, 'bank_name' => null,
                'account_name' => null, 'account_number' => null, 'npwp' => null,
                'phone' => null, 'active' => true,
                'notes' => 'Lengkapi detail rekening Yayak saat datanya tersedia.',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        if (Schema::hasTable('permissions') && Schema::hasTable('roles') && Schema::hasTable('role_permissions')) {
            foreach (['bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail'] as $name) {
                DB::table('permissions')->insertOrIgnore(['name' => $name, 'created_at' => $now, 'updated_at' => $now]);
            }

            $permissionIds = DB::table('permissions')->whereIn('name', ['bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail'])->pluck('id', 'name');
            $roleIds = DB::table('roles')->whereIn('name', ['master', 'admin'])->pluck('id', 'name');
            foreach (['bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail'] as $name) {
                if (isset($roleIds['master'], $permissionIds[$name])) {
                    DB::table('role_permissions')->insertOrIgnore(['role_id' => $roleIds['master'], 'permission_id' => $permissionIds[$name]]);
                }
                if ($name !== 'deletebankdetail' && isset($roleIds['admin'], $permissionIds[$name])) {
                    DB::table('role_permissions')->insertOrIgnore(['role_id' => $roleIds['admin'], 'permission_id' => $permissionIds[$name]]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('invoice_items', fn (Blueprint $table) => $table->dropColumn('price_group'));
        Schema::table('quotation_items', fn (Blueprint $table) => $table->dropColumn('price_group'));
        Schema::table('invoices', fn (Blueprint $table) => $table->dropConstrainedForeignId('bank_detail_id'));
        Schema::table('quotations', fn (Blueprint $table) => $table->dropConstrainedForeignId('bank_detail_id'));
        Schema::dropIfExists('bank_details');

        if (Schema::hasTable('permissions') && Schema::hasTable('role_permissions')) {
            $ids = DB::table('permissions')->whereIn('name', ['bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail'])->pluck('id');
            DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
            DB::table('permissions')->whereIn('id', $ids)->delete();
        }
    }
};

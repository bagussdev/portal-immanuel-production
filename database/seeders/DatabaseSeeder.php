<?php

namespace Database\Seeders;

use App\Models\NotificationPreference;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seedPassword = $_ENV['SEED_DEFAULT_PASSWORD'] ?? $_SERVER['SEED_DEFAULT_PASSWORD'] ?? getenv('SEED_DEFAULT_PASSWORD');
        $seedPassword = is_string($seedPassword) && $seedPassword !== '' ? $seedPassword : 'ChangeMe123!';

        $roles = collect(['master', 'admin', 'mandor', 'user'])
            ->mapWithKeys(fn ($name) => [$name => Role::firstOrCreate(['name' => $name])]);

        $permissions = [
            'dashboard', 'equipmentmenu', 'createequipment', 'editequipment', 'deleteequipment',
            'clientmenu', 'createclient', 'editclient', 'deleteclient',
            'armadamenu', 'createarmada', 'editarmada', 'deletearmada', 'samsatarmada',
            'gudangmenu', 'creategudang', 'editgudang', 'deletegudang',
            'bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail',
            'quotationmenu', 'createquotation', 'editquotation', 'deletequotation', 'approvequotation',
            'invoicemenu', 'createinvoice', 'editinvoice', 'deleteinvoice', 'issueinvoice',
            'adddp', 'voidpayment', 'voidinvoice', 'paymentsmenu',
            'fieldjobsmenu', 'managefieldjobs', 'updatefieldjobstatus', 'uploadfieldjobphotos',
            'expensesmenu', 'createexpenses', 'editexpenses', 'deleteexpenses', 'manageexpenses',
            'payrollmenu', 'addpayroll', 'editpayroll', 'paypayroll', 'managepayroll',
            'menuuser', 'createuser', 'edituser', 'usercontrol', 'exportuserdata', 'permission', 'notification',
        ];

        $permissionModels = collect($permissions)
            ->mapWithKeys(fn ($name) => [$name => Permission::firstOrCreate(['name' => $name])]);

        $roles['master']->permissions()->sync($permissionModels->pluck('id'));

        $adminDenied = [
            'permission', 'menuuser', 'createuser', 'edituser', 'usercontrol',
            'deleteinvoice', 'deletequotation', 'deleteclient', 'deletearmada',
            'deleteequipment', 'deletegudang', 'deletebankdetail',
        ];
        $roles['admin']->permissions()->sync($permissionModels->except($adminDenied)->pluck('id'));

        $roles['mandor']->permissions()->sync($permissionModels->only([
            'dashboard', 'fieldjobsmenu', 'managefieldjobs',
            'updatefieldjobstatus', 'uploadfieldjobphotos', 'payrollmenu', 'addpayroll', 'editpayroll',
        ])->pluck('id'));

        $roles['user']->permissions()->sync($permissionModels->only([
            'dashboard', 'fieldjobsmenu', 'updatefieldjobstatus',
            'uploadfieldjobphotos', 'payrollmenu',
        ])->pluck('id'));

        $accounts = [
            ['role' => 'master', 'name' => 'Master', 'username' => 'master', 'email' => 'master@immanuel.test'],
            ['role' => 'admin', 'name' => 'Admin', 'username' => 'admin', 'email' => 'admin@immanuel.test'],
            ['role' => 'mandor', 'name' => 'Mandor', 'username' => 'mandor', 'email' => 'mandor@immanuel.test'],
            ['role' => 'user', 'name' => 'User', 'username' => 'user', 'email' => 'user@immanuel.test'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'role_id' => $roles[$account['role']]->id,
                    'active' => true,
                    'password' => Hash::make($seedPassword),
                ]
            );
        }

        foreach (['invoice_due', 'invoice_schedule_h7', 'armada_samsat_due'] as $type) {
            foreach ([$roles['master']->id, $roles['admin']->id] as $roleId) {
                NotificationPreference::updateOrCreate(
                    ['role_id' => $roleId, 'type' => $type],
                    ['allowed' => true]
                );
            }
        }
    }
}

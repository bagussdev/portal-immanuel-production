<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    private const FINANCIAL_PERMISSIONS = [
        'bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail',
        'quotationmenu', 'createquotation', 'editquotation', 'deletequotation', 'approvequotation',
        'invoicemenu', 'createinvoice', 'editinvoice', 'deleteinvoice', 'issueinvoice',
        'adddp', 'voidpayment', 'voidinvoice', 'paymentsmenu',
        'expensesmenu', 'createexpenses', 'editexpenses', 'deleteexpenses', 'manageexpenses',
    ];

    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('permission');

        $roles = Role::with('permissions')->get();
        $permissions = Permission::where('name', '!=', 'schedulemenu')->get();

        return view('role-permissions.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('permission');

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['integer', 'exists:permissions,id'],
        ]);
        $inputPermissions = $validated['permissions'] ?? [];

        // Ambil semua role
        $roles = Role::all();

        foreach ($roles as $role) {
            if (strtolower($role->name) === 'master') {
                continue;
            }
            $permissionIds = $inputPermissions[$role->id] ?? [];
            if (in_array(strtolower($role->name), ['mandor', 'user'], true)) {
                $financialIds = Permission::query()->whereIn('name', self::FINANCIAL_PERMISSIONS)->pluck('id')->all();
                $permissionIds = array_values(array_diff($permissionIds, $financialIds));
            }

            // Sinkronisasi permission, kalau tidak ada input berarti kosong
            $role->permissions()->sync($permissionIds);
        }

        return redirect()->route('role-permissions.index')->with('success', 'Permissions updated successfully.');
    }
}

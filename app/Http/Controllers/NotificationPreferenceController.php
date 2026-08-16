<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Role;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationPreferenceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('notification');
        $roles = Role::orderBy('id')->get();

        // Tipe: dari config + yang sudah pernah dipakai (biar selalu muncul)
        $cfgTypes = config('notification_types', []);
        $cfgKeys = array_keys($cfgTypes);
        $dbKeys = Notification::query()->distinct()->pluck('type')->all();
        $allKeys = array_values(array_unique(array_merge($cfgKeys, $dbKeys)));

        // Bentuk array types untuk view (fallback label = key)
        $types = [];
        foreach ($allKeys as $k) {
            $types[$k] = $cfgTypes[$k] ?? ['label' => $k, 'desc' => null];
        }

        // Ambil allow-list: role_id -> (type -> row)
        $prefs = NotificationPreference::query()
            ->whereIn('type', $allKeys)
            ->where('allowed', 1)
            ->get()
            ->groupBy('role_id')
            ->map(fn ($rows) => $rows->keyBy('type'));

        return view('notifications.preferences', compact('roles', 'types', 'prefs'));
    }

    public function store(Request $request)
    {
        $this->authorize('notification');
        // Role yang benar-benar tampil di form
        $presentRoles = collect($request->input('present_roles', []))
            ->map(fn ($v) => (int) $v)->filter()->unique()->values()->all();

        if (empty($presentRoles)) {
            return back()->with('error', 'Tidak ada role pada formulir.');
        }

        // Tipe lengkap
        $cfgKeys = array_keys(config('notification_types', []));
        $dbKeys = Notification::query()->distinct()->pluck('type')->all();
        $allKeys = array_values(array_unique(array_merge($cfgKeys, $dbKeys)));

        // Data dicentang: prefs[role_id][] = [type, ...]
        $submitted = (array) $request->input('prefs', []);

        DB::transaction(function () use ($presentRoles, $submitted, $allKeys) {
            $roleNames = Role::query()->whereIn('id', $presentRoles)->pluck('name', 'id');
            foreach ($presentRoles as $roleId) {
                $checked = collect($submitted[$roleId] ?? [])
                    ->intersect($allKeys)
                    ->when(in_array(strtolower((string) ($roleNames[$roleId] ?? '')), ['mandor', 'user'], true),
                        fn ($types) => $types->reject(fn ($type) => NotificationService::isFinancialType((string) $type)))
                    ->values()
                    ->all();

                // Hapus semua preferensi lama role ini
                NotificationPreference::where('role_id', $roleId)->delete();

                // Insert ulang hanya yang DICENTANG (allow-list)
                if (! empty($checked)) {
                    $rows = array_map(fn ($type) => [
                        'role_id' => $roleId,
                        'type' => $type,
                        'allowed' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $checked);

                    NotificationPreference::insert($rows);
                }
            }
        });

        return back()->with('success', 'Preferences saved.');
    }
}

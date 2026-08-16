<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * 🔔 Dropdown — Delta polling:
     * Query: since (ISO), visible[] (ids), limit (default 10)
     * Return: latest_ts, unread_count, created[], updated[], deleted[], top[]
     */
    public function changes(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $limit = (int) ($request->integer('limit') ?: 10);
        $limit = $limit > 0 ? $limit : 10;

        $sinceRaw = $request->input('since');
        $since = $sinceRaw ? Carbon::parse($sinceRaw) : now()->subSeconds(2);
        $graceSince = (clone $since)->subSeconds(2);

        $visible = array_values(array_filter(array_map('intval', (array) $request->input('visible', []))));

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);

        // TOP N terbaru
        $topIds = (clone $base)->orderByDesc('created_at')->orderByDesc('id')->limit($limit)->pluck('id')->all();

        // Deleted = yang tadinya terlihat sekarang tidak masuk TOP
        $deleted = array_values(array_diff($visible, $topIds));

        // Created sejak grace + masuk top
        $createdIds = (clone $base)
            ->where('created_at', '>=', $graceSince)
            ->orderByDesc('created_at')->orderByDesc('id')
            ->limit($limit * 2)->pluck('id')->all();
        $created = array_values(array_intersect($createdIds, $topIds));

        // Updated sejak grace (bukan created)
        $updatedIds = (clone $base)
            ->where('updated_at', '>=', $graceSince)
            ->where('created_at', '<', $graceSince)
            ->orderByDesc('updated_at')->orderByDesc('id')
            ->limit($limit * 2)->pluck('id')->all();
        $updated = array_values(array_intersect($updatedIds, $topIds));

        $latestTs = (clone $base)->selectRaw('GREATEST(
            COALESCE(MAX(updated_at), "1970-01-01 00:00:00"),
            COALESCE(MAX(created_at), "1970-01-01 00:00:00")
        ) as ts')->value('ts') ?? now();

        $unreadCount = (clone $base)->whereNull('read_at')->count();

        return response()->json([
            'latest_ts' => Carbon::parse($latestTs)->toIso8601String(),
            'unread_count' => $unreadCount,
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'top' => $topIds,
        ]);
    }

    /**
     * 🔔 Dropdown — kembalikan <li> untuk ids[] (partial HTML).
     * Bisa juga fallback dengan limit=… bila ids kosong.
     * View: resources/views/notifications/_rows.blade.php
     */
    public function rows(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $limit = max(1, (int) $request->input('limit', 10));

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);

        if (empty($ids)) {
            $items = (clone $base)->orderByDesc('created_at')->orderByDesc('id')->limit($limit)->get();

            return view('notifications._rows', ['notifications' => $items]);
        }

        $items = (clone $base)->whereIn('id', $ids)
            ->orderByDesc('created_at')->orderByDesc('id')
            ->get();

        return view('notifications._rows', ['notifications' => $items]);
    }

    /** Tandai satu notifikasi sebagai read (izin: personal atau target role user). */
    public function markRead(Request $request, Notification $notification)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $allowed = ($notification->user_id === $user->id)
            || ($roleId && $notification->roles()->where('roles.id', $roleId)->exists());

        abort_unless($allowed, 403);

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return response()->noContent();
    }

    /** Tandai semua notifikasi relevan sebagai read. */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);
        $base->whereNull('read_at')->update(['read_at' => now()]);

        return response()->noContent();
    }

    /** Halaman Index — daftar lengkap (filter ringkas & responsif). */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $q = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));
        $status = $request->input('status', 'all'); // all|unread|read
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = max(10, (int) $request->input('per_page', 20));

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);

        if ($status === 'unread') {
            $base->whereNull('read_at');
        } elseif ($status === 'read') {
            $base->whereNotNull('read_at');
        }

        if ($startDate) {
            $base->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $base->whereDate('created_at', '<=', $endDate);
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('type', 'like', "%{$q}%")
                    ->orWhere('data->title', 'like', "%{$q}%")
                    ->orWhere('data->message', 'like', "%{$q}%");
            });
        }

        $base->orderByDesc('created_at')->orderByDesc('id');

        $latestTs = (clone $base)->selectRaw('GREATEST(
            COALESCE(MAX(updated_at), "1970-01-01 00:00:00"),
            COALESCE(MAX(created_at), "1970-01-01 00:00:00")
        ) as ts')->value('ts') ?? now();

        $items = $base->paginate($perPage)->appends($request->query());

        return view('notifications.index', [
            'items' => $items,
            'latestTs' => Carbon::parse($latestTs)->toIso8601String(),
            'filters' => compact('q', 'status', 'startDate', 'endDate', 'perPage'),
        ]);
    }

    /** Index — delta polling (since + visible[] + deleted by diff). */
    public function indexChanges(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $q = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));
        $status = $request->input('status', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sinceRaw = $request->input('since');
        $since = $sinceRaw ? Carbon::parse($sinceRaw) : now()->subSeconds(2);
        $graceSince = (clone $since)->subSeconds(2);

        $visible = array_values(array_filter(array_map('intval', (array) $request->input('visible', []))));

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);

        if ($status === 'unread') {
            $base->whereNull('read_at');
        } elseif ($status === 'read') {
            $base->whereNotNull('read_at');
        }

        if ($startDate) {
            $base->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $base->whereDate('created_at', '<=', $endDate);
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('type', 'like', "%{$q}%")
                    ->orWhere('data->title', 'like', "%{$q}%")
                    ->orWhere('data->message', 'like', "%{$q}%");
            });
        }

        $created = (clone $base)->where('created_at', '>=', $graceSince)->pluck('id')->all();
        $updated = (clone $base)->where('updated_at', '>=', $graceSince)->where('created_at', '<', $graceSince)->pluck('id')->all();

        $existingVisible = (clone $base)->whereIn('id', $visible)->pluck('id')->all();
        $deleted = array_values(array_diff($visible, $existingVisible));

        $latestTs = (clone $base)->selectRaw('GREATEST(
            COALESCE(MAX(updated_at), "1970-01-01 00:00:00"),
            COALESCE(MAX(created_at), "1970-01-01 00:00:00")
        ) as ts')->value('ts') ?? now();

        return response()->json([
            'latest_ts' => Carbon::parse($latestTs)->toIso8601String(),
            'created' => array_values(array_unique($created)),
            'updated' => array_values(array_unique($updated)),
            'deleted' => $deleted,
        ]);
    }

    /** Index — kembalikan <tr> untuk ids[] (partial HTML). */
    public function indexRows(Request $request)
    {
        $user = Auth::user();
        $roleIds = $user->role_id ? [$user->role_id] : [];

        $q = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));
        $status = $request->input('status', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $ids = array_values(array_filter(array_map('intval', (array) $request->input('ids', []))));
        if (empty($ids)) {
            return '';
        }

        $base = Notification::query()->forUserWithRoles($user->id, $roleIds);

        if ($status === 'unread') {
            $base->whereNull('read_at');
        } elseif ($status === 'read') {
            $base->whereNotNull('read_at');
        }

        if ($startDate) {
            $base->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $base->whereDate('created_at', '<=', $endDate);
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('type', 'like', "%{$q}%")
                    ->orWhere('data->title', 'like', "%{$q}%")
                    ->orWhere('data->message', 'like', "%{$q}%");
            });
        }

        $items = $base->whereIn('id', $ids)->orderByDesc('created_at')->orderByDesc('id')->get();

        return view('notifications._index_rows', ['items' => $items]);
    }
}

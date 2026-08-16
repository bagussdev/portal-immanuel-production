<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />
        <div class="ip-page max-w-6xl">
            <header class="ip-page-header">
                <div><p class="ip-kicker">Keamanan</p><h1 class="ip-title">Management User</h1><p class="ip-subtitle">Kelola akun, role, dan status user.</p></div>
                @can('createuser')
                    <a href="{{ route('users.create') }}" class="ip-btn-primary">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        Tambah akun
                    </a>
                @endcan
            </header>

            <form class="ip-card flex gap-3 p-4">
                <div class="relative flex-1"><svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/></svg><input name="search" value="{{ $search }}" placeholder="Cari nama atau email" class="ip-input pl-10"></div>
                <button class="ip-btn-dark">Cari</button>
            </form>

            <div class="ip-card">
                <div class="ip-table-wrap">
                    <table class="ip-table min-w-[860px]">
                        <thead><tr><th>Pengguna</th><th>Email</th><th>Role</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-950 text-xs font-extrabold text-white shadow-sm dark:bg-red-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span><span class="font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</span></div></td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="font-bold capitalize text-slate-700 dark:text-slate-300">{{ $user->role?->name ?: '-' }}</span></td>
                                    <td><x-status-badge :status="$user->active ? 'active' : 'inactive'" /></td>
                                    <td class="whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            @can('edituser')
                                                <a href="{{ route('users.edit', $user) }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-extrabold text-sky-700 hover:bg-sky-100 dark:border-white/10 dark:bg-white/[.06] dark:text-white">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4 16-1 5 5-1L19 9l-4-4L4 16ZM13 7l4 4"/></svg>
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('usercontrol')
                                                @if ($user->active)
                                                    <form method="POST" action="{{ route('users.deactive', $user) }}" onsubmit="return confirmAndLoad('Nonaktifkan akun ini?')">@csrf @method('PATCH')
                                                        <button class="inline-flex min-h-9 items-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-extrabold text-red-700 hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300">
                                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg>
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('users.active', $user) }}">@csrf @method('PATCH')
                                                        <button class="inline-flex min-h-9 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-extrabold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="m8 5 11 7-11 7V5Z"/></svg>
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-sky-100 p-4 dark:border-white/10">{{ $users->links() }}</div>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>

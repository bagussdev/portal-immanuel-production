{{-- resources/views/notifications/preferences.blade.php --}}
<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <h1 class="mt-5 text-2xl font-bold mb-6">Notification Preferences</h1>

        <div class="mt-5 max-w-6xl mx-auto p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
            <form method="POST" action="{{ route('notifications.preferences.store') }}"
                onsubmit="return confirmAndLoad('Simpan pengaturan notifikasi?')">
                @csrf

                {{-- SEARCH --}}
                <div class="mb-3">
                    <div class="relative w-full sm:w-96">
                        <input id="notifPrefSearch" type="text" autocomplete="off"
                            placeholder="Search notification types…"
                            class="w-full pl-9 pr-16 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 104.473 8.627l3.2 3.2a1 1 0 001.414-1.414l-3.2-3.2A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <button type="button" id="notifPrefClear"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hidden">
                            Clear
                        </button>
                    </div>
                </div>

                {{-- Tandai semua role yang tampil (supaya role tanpa centang tetap diproses di server) --}}
                @foreach ($roles as $role)
                    <input type="hidden" name="present_roles[]" value="{{ $role->id }}">
                @endforeach

                <div class="overflow-x-auto">
                    <div class="max-h-[300px] overflow-y-auto">
                        <table class="w-full border text-sm whitespace-nowrap">
                            <thead
                                class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2 text-left w-1/3">Notification Type</th>
                                    @foreach ($roles as $role)
                                        <th class="px-4 py-2 text-center w-24">{{ ucfirst($role->name) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="notifPrefBody">
                                @foreach ($types as $typeKey => $meta)
                                    @php
                                        $label = $meta['label'] ?? $typeKey;
                                        $desc = $meta['desc'] ?? '';
                                    @endphp
                                    <tr
                                        class="{{ $loop->odd ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }} border-t dark:border-gray-700">
                                        <td class="px-4 py-2">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $label }}</div>
                                            @if ($desc)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $desc }}</div>
                                            @endif
                                            <div class="text-[11px] text-gray-400">key: <code>{{ $typeKey }}</code>
                                            </div>
                                        </td>

                                        @foreach ($roles as $role)
                                            @php
                                                /**
                                                 * ALLOW-LIST:
                                                 * - $prefs adalah map: role_id -> (map type -> row {allowed})
                                                 * - Checkbox TER-CENTANG hanya jika ADA row untuk (role_id, type) dan allowed == 1
                                                 */
                                                $roleMap = $prefs[$role->id] ?? null; // bisa array atau Collection
                                                if ($roleMap instanceof \Illuminate\Support\Collection) {
                                                    $row = $roleMap->get($typeKey);
                                                } elseif (is_array($roleMap)) {
                                                    $row = $roleMap[$typeKey] ?? null;
                                                } else {
                                                    $row = null;
                                                }
                                                $checked = $row && (int) ($row->allowed ?? 1) === 1;
                                                $locked = in_array(strtolower($role->name), ['mandor', 'user'], true) && \App\Services\NotificationService::isFinancialType($typeKey);
                                            @endphp
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" name="prefs[{{ $role->id }}][]"
                                                    value="{{ $typeKey }}" {{ $checked ? 'checked' : '' }}
                                                    @disabled($locked)
                                                    title="{{ $locked ? 'Notifikasi keuangan dikunci untuk role ini' : '' }}"
                                                    class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr id="notifPrefEmpty" class="hidden">
                                    <td colspan="{{ 1 + $roles->count() }}"
                                        class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No notification types found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-start">
                    <x-action-button text="Save Preferences" color="blue" type="submit" />
                </div>
            </form>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>

<script>
    (function() {
        const input = document.getElementById('notifPrefSearch');
        const clear = document.getElementById('notifPrefClear');
        const tbody = document.getElementById('notifPrefBody');
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.id !== 'notifPrefEmpty');
        const empty = document.getElementById('notifPrefEmpty');

        const norm = s => (s || '').toString().toLowerCase().trim();

        function apply() {
            const q = norm(input.value);
            let vis = 0;
            rows.forEach(tr => {
                const cell = tr.querySelector('td:first-child');
                const text = norm(cell ? cell.textContent : '');
                const match = !q || text.includes(q);
                tr.classList.toggle('hidden', !match);
                if (match) vis++;
            });
            empty.classList.toggle('hidden', vis !== 0);
            clear.classList.toggle('hidden', input.value.length === 0);
        }

        let raf = 0;
        const schedule = () => {
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(apply);
        };
        input.addEventListener('input', schedule);
        input.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                input.value = '';
                apply();
            }
        });
        clear.addEventListener('click', () => {
            input.value = '';
            apply();
            input.focus();
        });

        // init
        apply();
    })();
</script>

<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information></x-alert-information>

        <h1 class="mt-5 text-2xl font-bold mb-6">Role Permission Management</h1>
        <div class="mt-5 max-w-6xl mx-auto p-4 bg-white dark:bg-gray-800 rounded-xl shadow">

            <form method="POST" action="{{ route('role-permissions.store') }}"
                onsubmit="return confirmAndLoad('Are you sure you want to save permissions?')">
                @csrf

                {{-- SEARCH (client-side filter) --}}
                <div class="mb-3">
                    <div class="relative w-full sm:w-96">
                        <input id="permSearch" type="text" autocomplete="off" placeholder="Search permissions…"
                            class="w-full pl-9 pr-16 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        {{-- icon --}}
                        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 104.473 8.627l3.2 3.2a1 1 0 001.414-1.414l-3.2-3.2A5.5 5.5 0 009 3.5zm-4 5.5a4 4 0 118 0 4 4 0 01-8 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <button type="button" id="permSearchClear"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hidden">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div class="max-h-[300px] overflow-y-auto">
                        <table class="w-full border text-sm whitespace-nowrap">
                            <thead
                                class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2 text-left w-1/3">Permission</th>
                                    @foreach ($roles as $role)
                                        <th class="px-4 py-2 text-center w-24">{{ ucfirst($role->name) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="permTableBody">
                                @php($financialPermissions = ['bankdetailmenu','createbankdetail','editbankdetail','deletebankdetail','quotationmenu','createquotation','editquotation','deletequotation','approvequotation','invoicemenu','createinvoice','editinvoice','deleteinvoice','issueinvoice','adddp','voidpayment','voidinvoice','paymentsmenu','expensesmenu','createexpenses','editexpenses','deleteexpenses','manageexpenses'])
                                @foreach ($permissions as $permission)
                                    <tr
                                        class="{{ $loop->odd ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }} border-t dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $permission->name }}</td>
                                        @foreach ($roles as $role)
                                            @php($locked = strtolower($role->name) === 'master' || (in_array(strtolower($role->name), ['mandor','user'], true) && in_array($permission->name, $financialPermissions, true)))
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][]"
                                                    value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}
                                                    @disabled($locked)
                                                    title="{{ $locked && in_array(strtolower($role->name), ['mandor','user'], true) ? 'Akses keuangan dikunci untuk role ini' : '' }}"
                                                    class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                {{-- NO RESULTS ROW (muncul saat tidak ada yang cocok) --}}
                                <tr id="noResultsRow" class="hidden">
                                    <td colspan="{{ 1 + $roles->count() }}"
                                        class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No permissions found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-start">
                    <x-action-button text="Save Permissions" color="blue" type="submit" />
                </div>
            </form>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>


<script>
    (function() {
        const input = document.getElementById('permSearch');
        const clear = document.getElementById('permSearchClear');
        const tbody = document.getElementById('permTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.id !== 'noResultsRow');
        const emptyEl = document.getElementById('noResultsRow');

        const norm = (s) => (s || '').toString().toLowerCase().trim();

        function applyFilter() {
            const q = norm(input.value);
            let visible = 0;

            rows.forEach(tr => {
                const firstCell = tr.querySelector('td:first-child');
                const name = norm(firstCell ? firstCell.textContent : '');
                const match = !q || name.includes(q);
                tr.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (emptyEl) emptyEl.classList.toggle('hidden', visible !== 0);
            clear.classList.toggle('hidden', input.value.length === 0);
        }

        let raf = 0;
        const schedule = () => {
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(applyFilter);
        };

        input.addEventListener('input', schedule);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                input.value = '';
                applyFilter();
            }
        });
        clear.addEventListener('click', () => {
            input.value = '';
            applyFilter();
            input.focus();
        });

        // init (jaga-jaga kalau ada autofill)
        applyFilter();
    })();
</script>

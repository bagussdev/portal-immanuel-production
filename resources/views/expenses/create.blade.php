<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        {{-- Back --}}
        <div class="mb-4">
            <a href="{{ route('expenses.index', ['month' => $targetMonth ?? now()->month, 'year' => $targetYear ?? now()->year]) }}"
                onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-1">Add Expense</h2>
            <span class="block text-sm text-gray-500 dark:text-gray-400 mb-4">
                Period:
                {{ \Carbon\Carbon::create($targetYear, $targetMonth, 1)->locale('id')->translatedFormat('F Y') }}
            </span>

            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                onsubmit="sanitizeExpenseForm(this); return confirmAndLoad('Simpan pengeluaran ini?')">
                @csrf

                {{-- Kunci periode target untuk validasi server --}}
                <input type="hidden" name="target_month" value="{{ $targetMonth }}">
                <input type="hidden" name="target_year" value="{{ $targetYear }}">

                {{-- Row 1: Date, Name, Qty, Total --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Expense
                            Date</label>
                        <input type="date" name="expense_date"
                            value="{{ old('expense_date', $defaultDate ?? now()->format('Y-m-d')) }}"
                            min="{{ $minDate }}" max="{{ $maxDate }}" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        @error('expense_date')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Beli nasi kru"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Qty</label>
                        <input type="number" name="qty" min="1" value="{{ old('qty', 1) }}" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        @error('qty')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Total
                            (Rp)</label>
                        <input type="text" name="total" value="{{ old('total') }}" required
                            oninput="formatCurrencyID(this)"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-right" />
                        @error('total')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Attachment --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Attachment
                        (optional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300" />
                    <p class="text-[11px] text-gray-500 mt-1">Maks 4 MB. Format: JPG/PNG/PDF.</p>
                    @error('attachment')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Notes
                        (optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                        placeholder="Keterangan tambahan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if ($errors->any())
                    <div class="text-xs text-red-500">
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-center gap-2 pt-2">
                    <x-action-button text="Save" color="green" />
                </div>
            </form>
        </div>

        @push('scripts')
            <script>
                function formatCurrencyID(input) {
                    const raw = (input.value || '').replace(/\D/g, '');
                    input.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                }

                function sanitizeExpenseForm(form) {
                    const total = form.querySelector('input[name="total"]');
                    if (total) total.value = (total.value || '').replace(/\D/g, '') || '0';
                    return true;
                }
            </script>
        @endpush

    </x-dashboard.sidebar>
</x-app-layout>

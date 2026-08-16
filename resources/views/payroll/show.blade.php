<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        {{-- BACK --}}
        <div class="mb-6">
            <a href="{{ route('payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}"
                onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        @php
            /** @var \Illuminate\Support\Collection $baseItems */
            /** @var \Illuminate\Support\Collection $deductionItems */
            $baseItems = $baseItems ?? collect(); // dari controller
            $deductionItems = $deductionItems ?? collect();

            $baseTotal = (int) ($baseItems->sum('amount') ?? 0);
            $dedTotal = (int) ($deductionItems->sum('amount') ?? 0);
            $net = $baseTotal - $dedTotal;
            $idr = fn($n) => number_format((int) $n, 0, ',', '.');
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            {{-- Heading --}}
            <div class="mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Payroll Details</h2>
                <div class="text-sm text-gray-500">
                    Employee:
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $payroll->user->name }}</span>
                </div>
                <div class="text-sm text-gray-500">
                    Period:
                    {{ \Carbon\Carbon::create()->month($payroll->period->month)->locale('id')->translatedFormat('F') }}
                    {{ $payroll->period->year }}
                </div>
            </div>

            {{-- Summary --}}
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Base</div>
                    <div class="font-semibold text-blue-600">Rp {{ $idr($baseTotal) }}</div>
                </div>
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Deduction</div>
                    <div class="font-semibold text-red-600">Rp {{ $idr($dedTotal) }}</div>
                </div>
                <div class="p-3 rounded border dark:border-gray-700">
                    <div class="text-gray-500 text-xs">Net Total</div>
                    <div class="font-bold">Rp {{ $idr($net) }}</div>
                </div>
            </div>

            {{-- Rincian --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left: Items --}}
                <div class="lg:col-span-7">
                    <div class="rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Rincian Komponen
                        </div>
                        <div class="p-4">
                            {{-- Base (multi-row) --}}
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Base</div>
                                    <div class="text-xs text-gray-500">
                                        Total: <span class="font-semibold text-blue-600">Rp
                                            {{ $idr($baseTotal) }}</span>
                                    </div>
                                </div>

                                @if ($baseItems->count())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="text-left text-gray-500">
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="py-2 pr-2">No</th>
                                                    <th class="py-2 pr-2">Nama</th>
                                                    <th class="py-2 pr-2 text-right">Jumlah (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-800 dark:text-gray-100">
                                                @foreach ($baseItems as $i => $it)
                                                    <tr class="border-b last:border-0 dark:border-gray-700">
                                                        <td class="py-2 pr-2 align-top">{{ $i + 1 }}</td>
                                                        <td class="py-2 pr-2 align-top">{{ $it->name ?: 'Gaji Pokok' }}
                                                        </td>
                                                        <td class="py-2 pr-2 align-top text-right">Rp
                                                            {{ $idr($it->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">Tidak ada komponen base.</div>
                                @endif
                            </div>

                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            {{-- Deductions --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Deductions</div>
                                    <div class="text-xs text-gray-500">
                                        Total: <span class="font-semibold text-red-600">Rp {{ $idr($dedTotal) }}</span>
                                    </div>
                                </div>

                                @if ($deductionItems->count())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="text-left text-gray-500">
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="py-2 pr-2">No</th>
                                                    <th class="py-2 pr-2">Nama</th>
                                                    <th class="py-2 pr-2 text-right">Jumlah (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-800 dark:text-gray-100">
                                                @foreach ($deductionItems as $i => $it)
                                                    <tr class="border-b last:border-0 dark:border-gray-700">
                                                        <td class="py-2 pr-2 align-top">{{ $i + 1 }}</td>
                                                        <td class="py-2 pr-2 align-top">{{ $it->name }}</td>
                                                        <td class="py-2 pr-2 align-top text-right">Rp
                                                            {{ $idr($it->amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">Tidak ada potongan.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Notes & Info --}}
                <div class="lg:col-span-5">
                    <div class="rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Ringkasan Pembayaran
                        </div>
                        <div class="p-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Total Base</span>
                                <span class="font-semibold">Rp {{ $idr($baseTotal) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Total Potongan</span>
                                <span class="font-semibold text-red-600">- Rp {{ $idr($dedTotal) }}</span>
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between text-base">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Diterima (Net)</span>
                                <span class="font-bold">Rp {{ $idr($net) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg border dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 font-medium text-gray-700 dark:text-gray-200">
                            Catatan
                        </div>
                        <div class="p-4 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">
                            {{ $payroll->notes ?: '—' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions (BOTTOM) --}}
            <div class="mt-6 flex">
                <x-action-button as="a" href="{{ route('payroll.slip.pdf', $payroll) }}" target="_blank"
                    text="Export PDF" color="yellow" />
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>

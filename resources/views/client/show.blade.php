<x-app-layout>
    <x-dashboard.sidebar>
        <x-alert-information />

        <div class="mt-4 px-4">
            <a href="{{ route('client.index') }}" onclick="showFullScreenLoader();"
                class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </a>
        </div>

        <div class="flex justify-between items-center mt-4 w-full max-w-full px-4">
            <h2 class="font-bold text-xl sm:text-2xl">Client Detail</h2>
        </div>

        <hr class="h-[3px] my-4 bg-gray-200 border-0 dark:bg-gray-700 w-full">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-sm text-gray-700 dark:text-gray-300">
            {{-- MOBILE VERSION --}}
            <div class="block sm:hidden space-y-4">
                @php
                    $fields = [
                        'Name' => $client->name,
                        'Company' => $client->company ?? '-',
                        'Email' => $client->email ?? '-',
                        'Phone' => $client->phone ?? '-',
                    ];
                @endphp
                <div class="grid grid-cols-1 gap-3">
                    @foreach ($fields as $label => $value)
                        <div class="flex">
                            <div class="w-32 font-medium">{{ $label }}</div>
                            <div class="flex-1">: {!! nl2br(e($value)) !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- DESKTOP VERSION --}}
            <div class="hidden sm:grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div><span class="font-medium">Name :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $client->name }}</div>
                </div>
                <div><span class="font-medium">Company :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $client->company ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Email :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $client->email ?? '-' }}</div>
                </div>
                <div><span class="font-medium">Phone :</span>
                    <div class="bg-blue-50 dark:bg-blue-900 rounded px-3 py-2 mt-1">{{ $client->phone ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- INVOICE LIST --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">List Invoice</h2>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                            <tr>
                                <th class="px-4 py-2 text-left">Invoice No</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                                @php
                                    $invNo = $inv->invoice_number ?? 'INV-' . $inv->id;
                                    $invDate = \Illuminate\Support\Carbon::parse($inv->created_at)->format('d M Y');
                                    $invTot = (float) ($inv->grand_total ?? 0);
                                @endphp
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        <a href="{{ route('invoices.show', $inv->id) }}"
                                            onclick="showFullScreenLoader();"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                            {{ $invNo }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $invDate }}</td>
                                    <td class="px-4 py-2">Rp {{ number_format($invTot, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No
                                        invoice found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $invoices->onEachSide(1)->withQueryString()->links() }}
                    </div>
                </div>

                {{-- QUOTATION LIST --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">List Quotation</h2>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                            <tr>
                                <th class="px-4 py-2 text-left">Quotation No</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Total (Sisa)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quotations as $quo)
                                @php
                                    $quoNo = $quo->quotation_number ?? 'QUO-' . $quo->id;
                                    $quoDate = \Illuminate\Support\Carbon::parse($quo->created_at)->format('d M Y');
                                    $quoTot = (float) ($quo->grand_total ?? 0);
                                @endphp
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        <a href="{{ route('quotations.show', $quo->id) }}"
                                            onclick="showFullScreenLoader();"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                            {{ $quoNo }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $quoDate }}</td>
                                    <td class="px-4 py-2">Rp {{ number_format($quoTot, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No
                                        quotation found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $quotations->onEachSide(1)->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </x-dashboard.sidebar>
</x-app-layout>

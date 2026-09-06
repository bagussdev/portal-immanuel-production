@php
    /**
     * @var \Illuminate\Support\Collection|\App\Models\Payroll[] $rows
     * @var \App\Models\PayrollPeriod|null $period
     * @var int $month
     * @var int $year
     */
@endphp

@foreach ($rows as $row)
    @php
        $status = strtolower((string) $row->status); // draft|paid
        $periodStatus = strtolower($period->status ?? '');
        $isPaid = $status === 'paid';
        $periodEditable = in_array($periodStatus, ['open', 'reopen'], true);
        $canEdit = $periodEditable && (!$isPaid || auth()->user()->can('managepayroll'));
        $canDelete = $periodEditable && auth()->user()->can('managepayroll');
        $canAdd = in_array($periodStatus, ['open', 'reopen'], true);
    @endphp
    <tr data-id="{{ $row->id }}">
        <td class="px-4 py-3 text-slate-400">{{ $rowNumber ?? '' }}</td>
        <td class="px-4 py-3 text-left">{{ $row->user->name ?? '-' }}</td>
        <td class="px-4 py-3 text-blue-600">{{ number_format((float) $row->total_base, 0, ',', '.') }}</td>
        <td class="px-4 py-3 text-red-600">{{ number_format((float) $row->total_deductions, 0, ',', '.') }}</td>
        <td class="px-4 py-3 font-semibold">{{ number_format((float) $row->net_pay, 0, ',', '.') }}</td>
        <td class="px-4 py-3"><x-status-badge :status="$status" /></td>
        <td class="px-4 py-3">
            <div class="flex justify-center items-center gap-1">
                <x-action-button :href="route('payroll.show', ['payroll' => $row->id, 'month' => $month, 'year' => $year])" onclick="showFullScreenLoader();" color="blue" text="Detail" :dense="true" />

                @can('editpayroll')
                    @if($canEdit)<x-action-button :href="route('payroll.edit', ['payroll' => $row->id, 'month' => $month, 'year' => $year])" onclick="showFullScreenLoader();" color="green" text="Edit" :dense="true" />@endif
                @endcan

                @can('paypayroll')
                    @if ($status === 'draft' && $canAdd)
                        <form
                            action="{{ route('payroll.pay', $row) }}?month={{ $month }}&year={{ $year }}"
                            method="POST" onsubmit="return confirmAndLoad('Tandai slip ini sudah dibayar?')">
                            @csrf @method('PATCH')
                            <x-action-button color="green" text="Bayar" :dense="true" />
                        </form>
                    @endif
                @endcan

                @if($canDelete)
                    <form action="{{ route('payroll.destroy', $row) }}" method="POST" onsubmit="return confirmAndLoad('Hapus slip gaji {{ $row->user->name ?? '' }}?')">
                        @csrf @method('DELETE')
                        <x-action-button color="red" text="Hapus" :dense="true" />
                    </form>
                @endif
            </div>
        </td>
    </tr>
@endforeach

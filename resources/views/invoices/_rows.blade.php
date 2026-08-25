@foreach ($invoices as $invoice)
    <tr data-id="{{ $invoice->id }}">
        <td><a href="{{ route('invoices.show', $invoice) }}"
                class="font-extrabold text-sky-700 hover:text-sky-900 dark:text-white dark:hover:text-red-400">{{ $invoice->invoice_number ?: 'DRAFT #' . $invoice->id }}</a>
        </td>
        <td>
            <p class="font-bold text-slate-900 dark:text-white">{{ $invoice->client?->name ?: 'Client manual' }}</p>
        </td>
        <td class="max-w-[220px]"><p class="truncate text-sm font-semibold text-slate-700 dark:text-slate-300" title="{{ $invoice->event_name }}">{{ $invoice->event_name ?: '-' }}</p></td>
        <td class="whitespace-nowrap text-xs font-bold text-slate-600 dark:text-slate-300"><x-date-range :start="$invoice->issue_date ?: $invoice->created_at" /></td>
        <td class="whitespace-nowrap text-xs font-bold text-slate-600 dark:text-slate-300"><x-date-range :start="$invoice->event_date" :end="$invoice->event_end_date" /></td>
        <td><x-status-badge :status="$invoice->status" /></td>
        <td class="whitespace-nowrap text-right">
            {{ (int) $invoice->grand_total > 0 ? 'Rp ' . number_format($invoice->grand_total, 0, ',', '.') : '' }}</td>
        <td class="whitespace-nowrap text-right font-bold text-emerald-600">
            {{ (int) $invoice->total_paid > 0 ? 'Rp ' . number_format($invoice->total_paid, 0, ',', '.') : '' }}</td>
        <td class="whitespace-nowrap text-right font-extrabold text-slate-900 dark:text-white">
            {{ (int) $invoice->balance_due > 0 ? 'Rp ' . number_format($invoice->balance_due, 0, ',', '.') : '' }}</td>
        <td class="text-right"><div class="inline-flex gap-2"><a href="{{ route('invoices.show', $invoice) }}" class="inline-flex rounded-lg bg-sky-50 px-3 py-2 font-bold text-sky-700 hover:bg-sky-100 dark:bg-white/[.06] dark:text-red-400">Detail</a>@can('adddp')@if(in_array($invoice->status,['unpaid','partial','overdue','overpaid']) && !$invoice->resolved_at)<form method="POST" action="{{ route('invoices.complete',$invoice) }}" onsubmit="return confirmAndLoad('Selesaikan invoice ini?')">@csrf<input type="hidden" name="paid_at" value="{{ today()->format('Y-m-d') }}"><button class="rounded-lg bg-emerald-600 px-3 py-2 font-bold text-white">Selesaikan</button></form>@endif @endcan</div></td>
    </tr>
@endforeach

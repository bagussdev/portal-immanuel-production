@if ($bankDetail)
    <x-responsive-disclosure kicker="Detail rekening" title="{{ $bankDetail->label }}" description="Buka untuk melihat rekening tujuan pembayaran.">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            @foreach (['Email' => $bankDetail->email, 'Bank' => $bankDetail->bank_name, 'Atas nama' => $bankDetail->account_name, 'No. rekening' => $bankDetail->account_number, 'NPWP' => $bankDetail->npwp, 'No. HP' => $bankDetail->phone] as $label => $value)
                @if (filled($value))
                    <div><dt class="text-[10px] font-extrabold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 break-words font-bold text-slate-800 dark:text-slate-200">{{ $value }}</dd></div>
                @endif
            @endforeach
        </dl>
    </x-responsive-disclosure>
@endif

@php
    $isInvoice = $kind === 'invoice';
    $title = $isInvoice ? 'INVOICE' : 'QUOTATION';
    $number = $isInvoice ? ($document->invoice_number ?: 'DRAFT') : $document->quotation_number;
    $date = $isInvoice ? ($document->issue_date ?: $document->created_at) : ($document->quotation_date ?: $document->created_at);
    $discount = (int) ($isInvoice ? $document->discount_value : $document->discount);
    $money = fn($value) => (int)$value > 0 ? 'Rp '.number_format((int)$value, 0, ',', '.') : '';
    $notes = $isInvoice ? $document->notes : $document->description;
@endphp
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>{{ $title }}</title><style>
    @page { size: A4; margin: 42mm 14mm 14mm; }
    @font-face { font-family:Poppins; font-weight:400; src:url("{{ storage_path('fonts/Poppins-Regular.ttf') }}") format('truetype'); }
    @font-face { font-family:Poppins; font-weight:600; src:url("{{ storage_path('fonts/Poppins-SemiBold.ttf') }}") format('truetype'); }
    @font-face { font-family:Poppins; font-weight:700; src:url("{{ storage_path('fonts/Poppins-Bold.ttf') }}") format('truetype'); }
    * { box-sizing:border-box; } body { margin:0; font-family:Poppins, sans-serif; font-size:10px; color:#263244; line-height:1.4; }
    .header { position:fixed; top:-31mm; left:0; right:0; height:26mm; border-bottom:2px solid #dc2626; }
    .header table { width:100%; border-collapse:collapse; } .logo { width:112px; } .company { text-align:right; }
    .company strong { display:block; color:#0f172a; font-size:16px; } .muted { color:#64748b; }
    .document-head { margin-bottom:13px; } .document-head table { width:100%; border-collapse:collapse; }
    .document-title { color:#0f172a; font-size:25px; font-weight:700; letter-spacing:1px; }
    .document-number { text-align:right; font-size:11px; }
    .info { width:100%; margin-bottom:14px; border-collapse:collapse; background:#f8fafc; border:1px solid #cbd5e1; }
    .info td { padding:8px 10px; vertical-align:top; width:50%; } .info strong { color:#0f172a; }
    .location { margin:0 0 14px; page-break-inside:auto; } .location-head { padding:8px 10px; background:#eaf4fb; border-left:4px solid #0284c7; page-break-after:avoid; }
    .location-name { color:#0f172a; font-size:12px; font-weight:700; } .schedule { margin-top:3px; color:#64748b; font-size:9px; }
    table.items { width:100%; border-collapse:collapse; page-break-inside:auto; }
    table.items thead { display:table-header-group; } table.items tr { page-break-inside:avoid; }
    table.items th { padding:7px 6px; background:#0f172a; color:#fff; font-size:8px; letter-spacing:.5px; text-transform:uppercase; }
    table.items td { padding:7px 6px; border-bottom:1px solid #dbe4ef; vertical-align:top; } table.items tbody tr:nth-child(even) { background:#f8fafc; }
    .num,.qty,.size { text-align:center; } .price { text-align:right; white-space:nowrap; }
    .notes { margin:12px 0 0; padding:9px 11px; border:1px solid #cbd5e1; border-left:4px solid #94a3b8; background:#f8fafc; page-break-inside:avoid; }
    .final { width:100%; margin-top:14px; border-collapse:separate; border-spacing:10px 0; page-break-inside:avoid; }
    .final > tbody > tr > td { width:50%; vertical-align:top; }
    .bank,.summary { border:1px solid #cbd5e1; background:#fff; padding:10px; }
    .box-title { margin-bottom:6px; color:#0f172a; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
    .bank table,.summary table { width:100%; border-collapse:collapse; } .bank td,.summary td { padding:2px 0; }
    .summary td:last-child { text-align:right; font-weight:600; } .grand td { border-top:1px solid #cbd5e1; padding-top:7px; color:#0f172a; font-weight:700 !important; }
    .footer { margin-top:16px; padding:9px 12px; border:1px dashed #94a3b8; border-radius:6px; background:#f8fafc; text-align:center; color:#475569; font-size:9px; page-break-inside:avoid; clear:both; }
</style></head><body>
<div class="header"><table><tr><td><img class="logo" src="{{ $documentLogo }}" alt="Logo"></td><td class="company"><strong>IMMANUEL PRODUCTION</strong><span class="muted">Jl. Mekar Blok D1 No 15, Denpasar Selatan, Bali<br>admin@immanuelproduction.com | 0818550837</span></td></tr></table></div>

<div class="document-head"><table><tr><td><div class="document-title">{{ $title }}</div><div class="muted">{{ $isInvoice ? 'Tagihan pekerjaan' : 'Penawaran pekerjaan' }}</div></td><td class="document-number"><strong>{{ $number }}</strong><br><span class="muted">{{ $date?->format('d-m-Y') }}</span></td></tr></table></div>
<table class="info"><tr><td><strong>Client</strong><br>{{ $document->client?->name ?: '-' }}<br><br><strong>Acara</strong><br>{{ $document->event_name ?: '-' }}</td><td><strong>Jumlah lokasi</strong><br>{{ $document->locations->count() }} lokasi<br><br><strong>Tanggal acara</strong><br>{{ \App\Support\DateRange::format($document->locations->first()?->event_start_date, $document->locations->first()?->event_end_date) }}</td></tr></table>

@foreach($document->locations as $location)
    @php($showUnitPrice = $location->items->contains(fn($item) => $item->pricing_mode === 'unit' && (int)$item->unit_price > 0))
    <section class="location">
        <div class="location-head"><div class="location-name">{{ $location->name ?: 'Lokasi belum ditentukan' }}</div><div class="schedule">Acara: {{ \App\Support\DateRange::format($location->event_start_date, $location->event_end_date) }} | Loading: {{ optional($location->loading_date)->format('d-m-Y H:i') ?: '-' }} @if($location->work_flow === 'install_teardown') | Bongkar: {{ optional($location->teardown_date)->format('d-m-Y H:i') ?: '-' }} @endif</div></div>
        <table class="items"><thead><tr><th style="width:28px">No</th><th>Item</th><th style="width:48px">Qty</th><th style="width:55px">Size</th>@if($showUnitPrice)<th style="width:90px">Harga Satuan</th>@endif<th style="width:95px">Total</th></tr></thead><tbody>
            @foreach($location->items as $item)<tr><td class="num">{{ $loop->iteration }}</td><td>{{ $item->item_name }}</td><td class="qty">{{ rtrim(rtrim(number_format((float)$item->qty,2,',','.'),'0'),',') }}</td><td class="size">{{ filled($item->length) && (float)$item->length > 0 ? rtrim(rtrim(number_format((float)$item->length,2,',','.'),'0'),',') : '' }}</td>@if($showUnitPrice)<td class="price">{{ $item->pricing_mode === 'unit' ? $money($item->unit_price) : '' }}</td>@endif<td class="price"><strong>{{ $money($item->total) }}</strong></td></tr>@endforeach
        </tbody></table>
    </section>
@endforeach

@if(filled($notes))<div class="notes"><strong>Catatan</strong><br>{!! nl2br(e($notes)) !!}</div>@endif
<table class="final"><tr><td>
    @if($document->bankDetail)<div class="bank"><div class="box-title">Detail Rekening - {{ $document->bankDetail->label }}</div><table>@foreach(['Email'=>$document->bankDetail->email,'Bank'=>$document->bankDetail->bank_name,'Atas Nama'=>$document->bankDetail->account_name,'No Rek'=>$document->bankDetail->account_number,'NPWP'=>$document->bankDetail->npwp,'No HP'=>$document->bankDetail->phone] as $label=>$value)@if(filled($value))<tr><td class="muted">{{ $label }}</td><td>: <strong>{{ $value }}</strong></td></tr>@endif @endforeach</table></div>@endif
</td><td><div class="summary"><div class="box-title">Ringkasan</div><table><tr><td>Subtotal</td><td>{{ $money($document->subtotal) }}</td></tr>@if($discount > 0)<tr><td>Diskon</td><td>- {{ $money($discount) }}</td></tr>@endif @if((int)$document->tax_value > 0)<tr><td>Potongan pajak</td><td>- {{ $money($document->tax_value) }}</td></tr>@endif<tr class="grand"><td>Total</td><td>{{ $money($document->grand_total) }}</td></tr>@if($isInvoice && (int)$document->total_paid > 0)<tr><td>Dibayar</td><td>{{ $money($document->total_paid) }}</td></tr><tr><td>Sisa</td><td>{{ $money($document->balance_due) ?: 'Lunas' }}</td></tr>@endif</table></div></td></tr></table>
<div class="footer">Terima kasih atas kepercayaan Anda.<br><strong>IMMANUEL PRODUCTION {{ date('Y') }}</strong> | admin@immanuelproduction.com | 0818550837 | www.immanuelproduction.com</div>
</body></html>

@php
    $isInvoice = $kind === 'invoice';
    $title = $isInvoice ? 'INVOICE' : 'QUOTATION';
    $number = $isInvoice ? ($document->invoice_number ?: 'DRAFT') : $document->quotation_number;
    $date = $isInvoice ? ($document->issue_date ?: $document->created_at) : ($document->quotation_date ?: $document->created_at);
    $discount = (int) ($isInvoice ? $document->discount_value : $document->discount);
    $money = fn($value) => (int)$value > 0 ? 'Rp '.number_format((int)$value, 0, ',', '.') : '';
    $notes = $isInvoice ? $document->notes : $document->description;
    $locations = $document->locations;
    if ($locations->isEmpty()) {
        $locations = collect([new \Illuminate\Support\Fluent([
            'name' => $document->location_event,
            'loading_date' => $document->loading_date,
            'teardown_date' => $document->bongkaran_date,
            'items' => $document->items,
        ])]);
    }
    $multipleLocations = $locations->count() > 1;
    $singleLocation = $locations->first();
    $eventStart = $document->event_date ?: $singleLocation?->event_start_date;
    $eventEnd = $document->event_end_date ?: $singleLocation?->event_end_date;
@endphp
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>{{ $title }}</title><style>
    @page { size: A4; margin: 14mm; }
    @font-face { font-family:Poppins; font-weight:400; src:url("{{ storage_path('fonts/Poppins-Regular.ttf') }}") format('truetype'); }
    @font-face { font-family:Poppins; font-weight:600; src:url("{{ storage_path('fonts/Poppins-SemiBold.ttf') }}") format('truetype'); }
    @font-face { font-family:Poppins; font-weight:700; src:url("{{ storage_path('fonts/Poppins-Bold.ttf') }}") format('truetype'); }
    * { box-sizing:border-box; } body { margin:0; font-family:Poppins, sans-serif; font-size:10px; color:#374151; line-height:1.45; }
    .header { height:28mm; margin-bottom:6mm; border-bottom:1.5px solid #334155; }
    .header table { width:100%; border-collapse:collapse; } .header td { vertical-align:top; } .logo { width:122px; } .company { text-align:right; line-height:1.55; }
    .company strong { display:block; color:#27303f; font-size:15px; } .muted { color:#64748b; }
    .document-head { margin-bottom:13px; }
    .document-title { color:#27303f; font-family:Georgia, serif; font-size:24px; font-weight:700; letter-spacing:.5px; }
    .info { width:100%; margin:0 0 14px; border-collapse:collapse; }
    .info > tbody > tr > td { padding:7px 8px; vertical-align:top; width:50%; } .info strong { color:#334155; }
    .info-lines { width:100%; border-collapse:collapse; background:transparent; } .info-lines td { width:auto; padding:2px 0; vertical-align:top; }
    .info-lines td:first-child { width:90px; color:#64748b; }
    .location { margin:0 0 14px; page-break-inside:auto; } .location-head { padding:8px 10px; background:#eaf4fb; border-left:4px solid #0284c7; page-break-after:avoid; }
    .location-name { color:#0f172a; font-size:12px; font-weight:700; } .schedule { margin-top:3px; color:#64748b; font-size:9px; }
    table.items { width:100%; border-collapse:collapse; page-break-inside:auto; border:1px solid #cbd5e1; }
    table.items thead { display:table-header-group; } table.items tr { page-break-inside:avoid; }
    table.items th { padding:9px 6px; border:1px solid #cbd5e1; background:#3b4a60; color:#fff; font-size:8px; letter-spacing:.5px; text-transform:uppercase; }
    table.items td { padding:7px 6px; border:1px solid #cbd5e1; vertical-align:top; } table.items tbody tr:nth-child(even) { background:#f8fafc; }
    .num,.qty,.size { text-align:center; } .price { text-align:right; white-space:nowrap; }
    .notes { margin:12px 0 0; padding:9px 11px; border:1px solid #cbd5e1; border-left:4px solid #94a3b8; background:#f8fafc; page-break-inside:avoid; }
    .final { width:100%; margin-top:14px; border-collapse:collapse; table-layout:fixed; page-break-inside:avoid; }
    .final > tbody > tr > td { vertical-align:top; } .final .bank-cell { width:60%; padding-right:12px; } .final .summary-cell { width:40%; }
    .bank { border-left:4px solid #64748b; background:#f8fafc; padding:12px 14px; }
    .summary { overflow:hidden; border:1px solid #cbd5e1; background:#fff; }
    .box-title { margin-bottom:6px; color:#0f172a; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
    .bank table,.summary table { width:100%; border-collapse:collapse; } .bank td { padding:3px 0; }
    .summary .box-title { margin:0; padding:10px 12px; border-bottom:1px solid #cbd5e1; background:#f1f5f9; font-size:11px; text-transform:none; letter-spacing:0; }
    .summary table { margin:6px 0; } .summary td { padding:4px 10px; } .summary td:nth-child(2) { width:12px; padding-left:0; padding-right:0; text-align:center; }
    .summary td:last-child { text-align:right; font-weight:600; white-space:nowrap; } .grand td { padding-top:7px; color:#0f172a; font-weight:700 !important; }
    .balance td { border-top:1px solid #cbd5e1; padding-top:7px; color:#0f172a; font-weight:700 !important; }
    .footer { margin-top:16px; padding:9px 12px; border:1px dashed #94a3b8; border-radius:6px; background:#f8fafc; text-align:center; color:#475569; font-size:9px; page-break-inside:avoid; clear:both; }
</style></head><body>
<div class="header"><table><tr><td><img class="logo" src="{{ $documentLogo }}" alt="Logo"></td><td class="company"><strong>IMMANUEL PRODUCTION</strong><span class="muted">Jl. Mekar Blok D1 No 15, Denpasar Selatan, Bali<br>admin@immanuelproduction.com | 0818550837</span></td></tr></table></div>

<div class="document-head"><div class="document-title">{{ $title }}</div><div class="muted">{{ $isInvoice ? 'Tagihan pekerjaan' : 'Penawaran pekerjaan' }}</div></div>
<table class="info"><tr><td><table class="info-lines">
    <tr><td>Client</td><td>: <strong>{{ $document->client?->name ?: '-' }}</strong></td></tr>
    <tr><td>Tanggal acara</td><td>: {{ \App\Support\DateRange::format($eventStart, $eventEnd) }}</td></tr>
    <tr><td>Acara</td><td>: {{ $document->event_name ?: '-' }}</td></tr>
    @unless($multipleLocations)<tr><td>Lokasi</td><td>: {{ $singleLocation?->name ?: ($document->location_event ?: '-') }}</td></tr>
    @unless($isInvoice)<tr><td>Loading</td><td>: {{ optional($singleLocation?->loading_date)->format('d-m-Y') ?: '-' }}</td></tr>
    @if($singleLocation?->teardown_date)<tr><td>Bongkar</td><td>: {{ optional($singleLocation->teardown_date)->format('d-m-Y') }}</td></tr>@endif @endunless @endunless
</table></td><td><table class="info-lines">
    <tr><td>{{ $isInvoice ? 'Invoice No' : 'Quotation No' }}</td><td>: <strong>{{ $number }}</strong></td></tr>
    <tr><td>{{ $isInvoice ? 'Invoice Date' : 'Quotation Date' }}</td><td>: {{ $date?->format('d-m-Y') }}</td></tr>
    @if($multipleLocations)<tr><td>Jumlah lokasi</td><td>: {{ $locations->count() }} lokasi</td></tr>@endif
</table></td></tr></table>

@foreach($locations as $location)
    @php($showUnitPrice = $location->items->contains(fn($item) => (int)$item->unit_price > 0))
    <section class="location">
        @if($multipleLocations)<div class="location-head"><div class="location-name">{{ $location->name ?: 'Lokasi belum ditentukan' }}</div>@unless($isInvoice)<div class="schedule">Loading: {{ optional($location->loading_date)->format('d-m-Y') ?: '-' }}@if($location->teardown_date) | Bongkar: {{ optional($location->teardown_date)->format('d-m-Y') }}@endif</div>@endunless</div>@endif
        <table class="items"><thead><tr><th style="width:28px">No</th><th>Item</th><th style="width:48px">Qty</th><th style="width:55px">Size</th>@if($showUnitPrice)<th style="width:90px">Harga Satuan</th>@endif<th style="width:95px">Total</th></tr></thead><tbody>
            @foreach($location->items as $item)<tr><td class="num">{{ $loop->iteration }}</td><td>{{ $item->item_name }}</td><td class="qty">{{ rtrim(rtrim(number_format((float)$item->qty,2,',','.'),'0'),',') }}</td><td class="size">{{ filled($item->length) && (float)$item->length > 0 ? rtrim(rtrim(number_format((float)$item->length,2,',','.'),'0'),',') : '' }}</td>@if($showUnitPrice)<td class="price">{{ $money($item->unit_price) }}</td>@endif<td class="price"><strong>{{ $money($item->total) }}</strong></td></tr>@endforeach
        </tbody></table>
    </section>
@endforeach

@if(filled($notes))<div class="notes"><strong>Catatan</strong><br>{!! nl2br(e($notes)) !!}</div>@endif
<table class="final"><tr><td class="bank-cell">
    @if($document->bankDetail)<div class="bank"><div class="box-title">Detail Rekening - {{ $document->bankDetail->label }}</div><table>@foreach(['Email'=>$document->bankDetail->email,'Bank'=>$document->bankDetail->bank_name,'Atas Nama'=>$document->bankDetail->account_name,'No Rek'=>$document->bankDetail->account_number,'NPWP'=>$document->bankDetail->npwp,'No HP'=>$document->bankDetail->phone] as $label=>$value)@if(filled($value))<tr><td class="muted">{{ $label }}</td><td>: <strong>{{ $value }}</strong></td></tr>@endif @endforeach</table></div>@endif
</td><td class="summary-cell"><div class="summary"><div class="box-title">Ringkasan</div><table>
    <tr><td>Sub Total</td><td>:</td><td>{{ $money($document->subtotal) }}</td></tr>
    @if($discount > 0)<tr><td>Diskon</td><td>:</td><td>- {{ $money($discount) }}</td></tr>@endif
    @if((int)$document->tax_value > 0)<tr><td>Potongan pajak</td><td>:</td><td>- {{ $money($document->tax_value) }}</td></tr>@endif
    <tr class="grand"><td>{{ $isInvoice ? 'Total Tagihan' : 'Total Penawaran' }}</td><td>:</td><td>{{ $money($document->grand_total) }}</td></tr>
    @if($isInvoice && (int)$document->total_paid > 0)<tr><td>Dibayar</td><td>:</td><td>{{ $money($document->total_paid) }}</td></tr>@endif
    @if($isInvoice)<tr class="balance"><td>Sisa Tagihan</td><td>:</td><td>{{ $money($document->balance_due) ?: 'Lunas' }}</td></tr>@endif
</table></div></td></tr></table>
<div class="footer">Terima kasih atas kepercayaan Anda.<br><strong>IMMANUEL PRODUCTION {{ date('Y') }}</strong> | admin@immanuelproduction.com | 0818550837 | www.immanuelproduction.com</div>
</body></html>

@php
    $baseItems = $baseItems ?? ($payroll->relationLoaded('items')
        ? $payroll->items->where('type', 'base')->values()
        : $payroll->items()->where('type', 'base')->orderBy('id')->get());
    $deductionItems = $deductionItems ?? ($payroll->relationLoaded('items')
        ? $payroll->items->where('type', 'deduction')->values()
        : $payroll->items()->where('type', 'deduction')->orderBy('id')->get());
    $baseTotal = isset($baseTotal) ? (int) $baseTotal : (int) $baseItems->sum('amount');
    $dedTotal = isset($dedTotal) ? (int) $dedTotal : (int) $deductionItems->sum('amount');
    $net = isset($net) ? (int) $net : $baseTotal - $dedTotal;
    $idr = fn ($value) => number_format((int) $value, 0, ',', '.');
    $pMonth = (int) ($payroll->period?->month ?: $payroll->month ?: now()->month);
    $pYear = (int) ($payroll->period?->year ?: $payroll->year ?: now()->year);
    $periodText = \Carbon\Carbon::createFromDate($pYear, max(1, min(12, $pMonth)), 1)
        ->locale('id')->translatedFormat('F Y');
    $slipNumber = $payroll->payroll_no
        ?? 'IMP/'.sprintf('%02d/%02d/PYR%04d', $pMonth, $pYear % 100, $payroll->id);
    $isPaid = $payroll->status === \App\Models\Payroll::STATUS_PAID;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $payroll->user->name }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            src: url("{{ storage_path('fonts/Poppins-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            src: url("{{ storage_path('fonts/Poppins-SemiBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            src: url("{{ storage_path('fonts/Poppins-Bold.ttf') }}") format('truetype');
        }
        body {
            margin: 0;
            color: #334155;
            font-family: 'Poppins', sans-serif;
            font-size: 10px;
            line-height: 1.35;
        }
        table { border-collapse: collapse; }
        .header { width: 100%; border-bottom: 2px solid #1e293b; }
        .header td { padding-bottom: 9px; vertical-align: middle; }
        .header-logo { width: 42%; }
        .header-logo img { width: 46mm; max-height: 19mm; }
        .header-company { width: 58%; text-align: right; }
        .company-name { color: #1e293b; font-size: 14px; font-weight: 700; }
        .company-address { margin-top: 3px; color: #64748b; font-size: 8px; line-height: 1.45; }
        .title { width: 100%; margin-top: 18px; margin-bottom: 14px; }
        .title td { vertical-align: middle; }
        .title-main { width: 75%; }
        .title-status { width: 25%; text-align: right; }
        h1 { margin: 0; color: #1e293b; font-size: 22px; letter-spacing: .7px; }
        .subtitle { margin-top: 3px; color: #64748b; font-size: 9px; }
        .status {
            display: inline-block;
            border: 1px solid {{ $isPaid ? '#a7f3d0' : '#cbd5e1' }};
            background: {{ $isPaid ? '#ecfdf5' : '#f8fafc' }};
            color: {{ $isPaid ? '#047857' : '#475569' }};
            padding: 5px 10px;
            font-size: 8px;
            font-weight: 700;
        }
        .identity { width: 100%; margin-bottom: 15px; border: 1px solid #cbd5e1; background: #f8fafc; }
        .identity td { width: 50%; padding: 9px 11px; vertical-align: top; }
        .identity td + td { border-left: 1px solid #cbd5e1; }
        .label { color: #64748b; font-size: 7px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #1e293b; font-size: 10px; font-weight: 600; }
        .spacer { margin-top: 8px; }
        .items { width: 100%; page-break-inside: auto; }
        .items thead { display: table-header-group; }
        .items tr { page-break-inside: avoid; }
        .items th, .items td { border: 1px solid #cbd5e1; padding: 7px 8px; }
        .items th {
            background: #334155;
            color: #fff;
            font-size: 8px;
            letter-spacing: .35px;
            text-align: left;
            text-transform: uppercase;
        }
        .items tbody tr:nth-child(even) { background: #f8fafc; }
        .number { width: 34px; text-align: center; }
        .type { width: 90px; color: #64748b; font-size: 8px; font-weight: 600; }
        .amount { width: 138px; text-align: right; white-space: nowrap; }
        .deduction { color: #b91c1c; }
        .summary-wrap { width: 100%; margin-top: 14px; }
        .summary-spacer { width: 55%; }
        .summary {
            width: 45%;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 9px 11px;
            vertical-align: top;
        }
        .summary-title { margin-bottom: 5px; color: #1e293b; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .summary table { width: 100%; }
        .summary td { padding: 3px 0; }
        .summary td:last-child { text-align: right; font-weight: 600; }
        .summary .net td {
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            color: #047857;
            font-size: 11px;
            font-weight: 700;
        }
        .notice {
            margin-top: 13px;
            border: 1px solid #cbd5e1;
            border-left: 4px solid {{ $isPaid ? '#10b981' : '#94a3b8' }};
            background: #f8fafc;
            padding: 8px 10px;
            page-break-inside: avoid;
        }
        .notice-title { color: #1e293b; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .notice-copy { margin-top: 3px; color: #64748b; }
        .footer {
            margin-top: 18px;
            border-top: 1px solid #cbd5e1;
            padding-top: 7px;
            color: #94a3b8;
            font-size: 7px;
        }
        .footer table { width: 100%; }
        .footer td:last-child { text-align: right; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="header-logo"><img src="{{ public_path('assets/logo.png') }}" alt="Immanuel Production"></td>
            <td class="header-company">
                <div class="company-name">IMMANUEL PRODUCTION</div>
                <div class="company-address">
                    Jl. Mekar Blok D1 No 15, Denpasar Selatan, Bali<br>
                    admin@immanuelproduction.com | 0818550837
                </div>
            </td>
        </tr>
    </table>

    <table class="title">
        <tr>
            <td class="title-main">
                <h1>SLIP GAJI</h1>
                <div class="subtitle">Periode {{ $periodText }}</div>
            </td>
            <td class="title-status"><span class="status">{{ $isPaid ? 'SUDAH DIBAYAR' : 'DRAFT' }}</span></td>
        </tr>
    </table>

    <table class="identity">
        <tr>
            <td>
                <div class="label">Karyawan</div>
                <div class="value">{{ $payroll->user->name }}</div>
                <div class="label spacer">Periode</div>
                <div class="value">{{ $periodText }}</div>
            </td>
            <td>
                <div class="label">Nomor slip</div>
                <div class="value">{{ $slipNumber }}</div>
                <div class="label spacer">Tanggal pembayaran</div>
                <div class="value">{{ $payroll->paid_at?->format('d/m/Y H:i') ?: '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="number">No.</th>
                <th>Komponen</th>
                <th class="type">Jenis</th>
                <th class="amount">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php($rowNumber = 1)
            @forelse ($baseItems as $item)
                <tr>
                    <td class="number">{{ $rowNumber++ }}</td>
                    <td>{{ $item->name ?: 'Gaji pokok' }}</td>
                    <td class="type">Pendapatan</td>
                    <td class="amount">Rp {{ $idr($item->amount) }}</td>
                </tr>
            @empty
                @if ($baseTotal > 0)
                    <tr>
                        <td class="number">{{ $rowNumber++ }}</td>
                        <td>Gaji pokok</td>
                        <td class="type">Pendapatan</td>
                        <td class="amount">Rp {{ $idr($baseTotal) }}</td>
                    </tr>
                @endif
            @endforelse
            @foreach ($deductionItems as $item)
                <tr>
                    <td class="number">{{ $rowNumber++ }}</td>
                    <td>{{ $item->name ?: 'Potongan' }}</td>
                    <td class="type deduction">Potongan</td>
                    <td class="amount deduction">- Rp {{ $idr($item->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-wrap">
        <tr>
            <td class="summary-spacer"></td>
            <td class="summary">
                <div class="summary-title">Ringkasan</div>
                <table>
                    <tr><td>Total pendapatan</td><td>Rp {{ $idr($baseTotal) }}</td></tr>
                    <tr><td>Total potongan</td><td>- Rp {{ $idr($dedTotal) }}</td></tr>
                    <tr class="net"><td>Diterima bersih</td><td>Rp {{ $idr($net) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="notice">
        <div class="notice-title">Status pembayaran</div>
        <div class="notice-copy">
            {{ $isPaid && $payroll->paid_at
                ? 'Dibayarkan pada '.$payroll->paid_at->locale('id')->translatedFormat('d F Y, H:i').' WITA.'
                : 'Slip ini belum ditandai sebagai dibayar.' }}
        </div>
    </div>

    @if (filled($payroll->notes))
        <div class="notice">
            <div class="notice-title">Catatan</div>
            <div class="notice-copy">{!! nl2br(e($payroll->notes)) !!}</div>
        </div>
    @endif

    <div class="footer">
        <table><tr>
            <td>Dokumen internal dan rahasia - Immanuel Production</td>
            <td>Dicetak {{ now()->format('d/m/Y H:i') }}</td>
        </tr></table>
    </div>
</body>
</html>

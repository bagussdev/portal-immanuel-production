
<?php
    // ==== DATA PREP (punya fallback kalau controller belum kirim variabelnya) ====
    // Prefer data dari controller; kalau tidak ada, ambil dari relasi $payroll->items()
    /** @var \App\Models\Payroll $payroll */
    $baseItems =
        $baseItems ??
        (isset($baseItem) && $baseItem
            ? collect([$baseItem])
            : ($payroll->relationLoaded('items')
                ? $payroll->items->where('type', 'base')->values()
                : $payroll->items()->where('type', 'base')->orderBy('id')->get()));

    $deductionItems =
        $deductionItems ??
        ($payroll->relationLoaded('items')
            ? $payroll->items->where('type', 'deduction')->values()
            : $payroll->items()->where('type', 'deduction')->orderBy('id')->get());

    // Totalnya
    $baseTotal = isset($baseTotal) ? (int) $baseTotal : (int) ($baseItems->sum('amount') ?? 0);
    $dedTotal = isset($dedTotal) ? (int) $dedTotal : (int) ($deductionItems->sum('amount') ?? 0);
    $net = isset($net) ? (int) $net : (int) ($baseTotal - $dedTotal);

    // Format angka
    $idr = fn($n) => number_format((int) $n, 0, ',', '.');

    // Periode: gunakan period month/year kalau ada; aman terhadap null/0
    $pMonth = (int) optional($payroll->period)->month ?: (int) $payroll->month ?: now()->month;
    $pYear = (int) optional($payroll->period)->year ?: (int) $payroll->year ?: now()->year;
    $periodeText =
        \Carbon\Carbon::createFromDate($pYear, max(1, min(12, $pMonth)), 1)
            ->locale('id')
            ->translatedFormat('F') .
        ' ' .
        $pYear;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>

    <style>
        /* ====== A4 + ruang header/footer (dompdf-friendly) ====== */
        @page {
            size: A4;
            margin: 48mm 14mm 24mm 14mm;
        }

        html,
        body {
            margin: 0;
            height: 100%;
            line-height: .8;
            letter-spacing: 0;
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            src: url("<?php echo e(storage_path('fonts/Poppins-Regular.ttf')); ?>") format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            src: url("<?php echo e(storage_path('fonts/Poppins-SemiBold.ttf')); ?>") format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            src: url("<?php echo e(storage_path('fonts/Poppins-Bold.ttf')); ?>") format('truetype');
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #333;
        }

        strong,
        b,
        h1,
        h2,
        h3 {
            font-weight: 700;
        }

        :root {
            --red: #db0000;
            --line: #d7d7d7;
            --muted: #555;
            --soft: #f6f6f6;
        }

        /* ====== HEADER (repeat tiap halaman) ====== */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 30mm;
            background: #fff;
        }

        .pdf-header .inner {
            padding: 0 14mm;
        }

        .hdr-row {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-top: 30px;
        }

        .hdr-left {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
        }

        .hdr-right {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
            text-align: right;
        }

        .hdr-left img {
            height: 24mm;
            object-fit: contain;
        }

        .hdr-right .title {
            font-weight: 700;
            font-size: 14px;
        }

        .hdr-right .addr {
            color: #555;
            line-height: 1;
        }

        /* ====== FOOTER (repeat) ====== */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 24mm;
            background: #fff;
            border-top: 1px solid var(--line);
            color: #444;
            font-size: 11px;
            text-align: center;
        }

        .pdf-footer .inner {
            display: inline-block;
            background: var(--soft);
            border: 1px dashed var(--line);
            border-radius: 6px;
            padding: 10px 14px;
            line-height: 1;
        }

        .pdf-footer a {
            color: #444;
            text-decoration: none;
            font-weight: 600;
        }

        .pdf-footer::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -2px;
            height: 2px;
            background: #fff;
        }

        /* ====== CONTENT ====== */
        .content {
            margin-top: 35mm;
            padding: 0 14mm;
        }

        /* ====== TITLE ====== */
        .doc-title {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            color: var(--red);
            margin: 16px 0 6px;
            display: inline-block;
            position: relative;
            padding-right: 3mm;
        }

        .doc-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -3mm;
            width: 100%;
            height: 1mm;
            background: var(--red);
            opacity: .15;
        }

        .doc-sub {
            color: #666;
            font-size: 10px;
            margin: 0 0 10px;
        }

        /* ====== INFO ====== */
        .info {
            margin-bottom: 14px;
        }

        .info table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        .info td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info td:first-child {
            color: var(--muted);
            width: 110px;
        }

        /* ====== ITEMS TABLE ====== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        table.items thead {
            display: table-header-group;
        }

        table.items th,
        table.items td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }

        table.items th {
            background-color: var(--red);
            color: white;
            text-transform: uppercase;
            letter-spacing: .2px;
            text-align: center;
        }

        table.items tbody tr:nth-child(odd) {
            background: #fbfbfb;
        }

        /* ====== SUMMARY ====== */
        .summary {
            width: 40%;
            float: right;
            margin-top: 14px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-title {
            background: #ffecec;
            color: #7a0000;
            font-weight: 700;
            padding: 8px 10px;
            border-bottom: 1px solid var(--line);
        }

        .summary table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            font-size: 12px;
            padding: 8px 10px 10px;
        }

        .summary td {
            padding: 0;
        }

        .summary td:nth-child(2) {
            width: 10px;
            color: #666;
        }

        .summary td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .summary .emph td:first-child {
            font-weight: 700;
            color: #111;
        }

        .summary .emph td:last-child {
            font-weight: 800;
        }

        /* ====== NOTES ====== */
        .notes {
            clear: both;
            margin-top: 14px;
            background: var(--soft);
            border: 1px dashed var(--line);
            padding: 10px 12px;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <!-- HEADER (repeat) -->
    <div class="pdf-header">
        <div class="inner">
            <div class="hdr-row">
                <div class="hdr-left">
                    <img src="<?php echo e(public_path('assets/brand/immanuel-production-legacy-logo.png')); ?>" alt="Logo">
                </div>
                <div class="hdr-right">
                    <div class="title">IMMANUEL PRODUCTION</div>
                    <div class="addr">
                        Jl. Mekar Blok D1 No 15<br>
                        Denpasar Selatan, Denpasar, Bali<br>
                        Email: <a href="mailto:admin@immanuelproduction.com">admin@immanuelproduction.com</a> | Telp:
                        0818550837
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER (repeat) -->
    <div class="pdf-footer">
        <div class="inner">
            Terima kasih atas kerja samanya.
            <br>
            <strong>IMMANUEL PRODUCTION</strong> &copy; <?php echo e(date('Y')); ?> <br>
            Email: <a href="mailto:admin@immanuelproduction.com">admin@immanuelproduction.com</a>
            • Telp: 0818550837
            • <strong>www.immanuelproduction.com</strong>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        
        <div class="doc-title">PAYROLL SLIP</div>
        <div class="doc-sub">Slip Gaji Karyawan</div>

        
        <div class="info">
            <table width="100%">
                <tr>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>: <?php echo e($payroll->user->name); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Periode</strong></td>
                                <td>: <?php echo e($periodeText); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>No. Slip</strong></td>
                                <td>:
                                    <?php echo e($payroll->payroll_no ?? 'IMP/' . sprintf('%02d/%02d/PYR%04d', $pMonth, $pYear % 100, $payroll->id)); ?>

                                </td </tr>
                            <tr>
                                <td><strong>Tanggal Cetak</strong></td>
                                <td>: <?php echo e(now()->format('d M Y, H:i')); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        
        <table class="items">
            <thead>
                <tr>
                    <th style="width:40px;">NO</th>
                    <th>KOMPONEN</th>
                    <th style="width:180px;">JUMLAH (RP)</th>
                </tr>
            </thead>
            <tbody>
                <?php $rowNo = 1; ?>

                
                <?php $__empty_1 = true; $__currentLoopData = $baseItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($rowNo++); ?></td>
                        <td><?php echo e($it->name ?: 'Gaji Pokok'); ?></td>
                        <td style="text-align:right;"><?php echo e($idr($it->amount)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    
                    <?php if($baseTotal > 0): ?>
                        <tr>
                            <td><?php echo e($rowNo++); ?></td>
                            <td>Gaji Pokok</td>
                            <td style="text-align:right;"><?php echo e($idr($baseTotal)); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                
                <?php $__currentLoopData = $deductionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($rowNo++); ?></td>
                        <td>Potongan: <?php echo e($it->name); ?></td>
                        <td style="text-align:right;">- <?php echo e($idr($it->amount)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <tr>
                    <td></td>
                    <td style="text-align:right;"><strong>Total Base</strong></td>
                    <td style="text-align:right;"><strong><?php echo e($idr($baseTotal)); ?></strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align:right;"><strong>Total Potongan</strong></td>
                    <td style="text-align:right;"><strong>- <?php echo e($idr($dedTotal)); ?></strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align:right;"><strong>Diterima (Net)</strong></td>
                    <td style="text-align:right;"><strong><?php echo e($idr($net)); ?></strong></td>
                </tr>
            </tbody>
        </table>

        
        <div class="summary">
            <div class="summary-title">Ringkasan</div>
            <table>
                <tr>
                    <td>Total Base</td>
                    <td>:</td>
                    <td>Rp <?php echo e($idr($baseTotal)); ?></td>
                </tr>
                <tr>
                    <td>Total Potongan</td>
                    <td>:</td>
                    <td>- Rp <?php echo e($idr($dedTotal)); ?></td>
                </tr>
                <tr class="emph">
                    <td><strong>Diterima (Net)</strong></td>
                    <td>:</td>
                    <td><strong>Rp <?php echo e($idr($net)); ?></strong></td>
                </tr>
            </table>
        </div>

        
        <?php if(!empty($payroll->notes)): ?>
            <div class="notes">
                <strong>Catatan:</strong><br>
                <?php echo nl2br(e($payroll->notes)); ?>

            </div>
        <?php endif; ?>

    </div>
</body>

</html>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\payroll\slip.blade.php ENDPATH**/ ?>
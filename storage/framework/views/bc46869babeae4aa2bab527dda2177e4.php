<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

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
            line-height: 1.2;
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
            --accent: #334155;
            --accent-strong: #1e293b;
            --accent-soft: #f1f5f9;
            --line: #cbd5e1;
            --muted: #64748b;
            --soft: #f8fafc;
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
            border-bottom: 2px solid var(--accent-strong);
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
            font-weight: bold;
            font-size: 14px;
        }

        .hdr-right .addr {
            color: var(--muted);
            line-height: 1.25;
        }

        /* ====== FOOTER (repeat tiap halaman) ====== */
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
        .quotation-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: var(--accent-strong);
            margin: 16px 0 6px;
            display: inline-block;
            position: relative;
            padding-right: 3mm;
        }

        .quotation-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -3mm;
            width: 100%;
            height: 1mm;
            background: var(--accent);
            opacity: .15;
        }

        .quotation-sub {
            color: #666;
            font-size: 10px;
            margin: 0 0 10px;
        }

        /* ====== INFO ====== */
        .quotation-info {
            margin-bottom: 14px;
        }

        .quotation-info table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        .quotation-info td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .quotation-info td:first-child {
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
            border: 1px solid var(--line);
            padding: 6px 8px;
            text-align: left;
        }

        table.items th {
            background-color: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .2px;
            text-align: center;
        }

        table.items tbody tr:nth-child(odd) {
            background: #fafafa;
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
            background: var(--accent-soft);
            color: var(--accent-strong);
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

        .bank-detail {
            width: 52%;
            float: left;
            margin-top: 14px;
            padding: 10px 12px;
            border-left: 4px solid #64748b;
            background: var(--soft);
            line-height: 1.25;
            box-sizing: border-box;
        }

        .bank-detail-title { color: var(--accent-strong); font-size: 10px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 6px; }
        .bank-detail table { width: 100%; border-collapse: collapse; }
        .bank-detail td { padding: 2px 0; vertical-align: top; }
        .bank-detail td:first-child { width: 82px; color: var(--muted); }
        .group-price { vertical-align: middle !important; text-align: right !important; font-weight: 700; background: #fff; }

        /* ====== NOTES ====== */
        .notes {
            clear: both;
            margin-top: 12px;
            margin-bottom: 0;
            background: var(--soft);
            border: 1px solid var(--line);
            border-left: 4px solid #94a3b8;
            padding: 10px 12px;
            border-radius: 6px;
            color: #334155;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <?php
        // Angka-angka
        $grandTotal = (int) ($invoice->grand_total ?? 0);
        $discPercent = (float) ($invoice->discount_percent ?? 0);
        $discount = (int) ($invoice->discount_value ?? 0);
        $taxPercent = (float) ($invoice->tax_percent ?? 0);
        $tax = (int) ($invoice->tax_value ?? 0);
        $subTotal = (int) ($invoice->subtotal ?? 0);
        $balanceDue = (int) ($invoice->balance_due ?? 0);
        $priceGroupCounts = $invoice->items->whereNotNull('price_group')->countBy('price_group');
        $renderedPriceGroups = [];

        $dps = $invoice->payments()->whereNull('voided_at')->orderBy('paid_at')->get();

        $dpCount = $dps->count();
        $dpTotal = (int) $dps->sum('amount');
        $dpPercent = $grandTotal > 0 ? round(($dpTotal / $grandTotal) * 100) : 0;
    ?>

    <!-- HEADER (repeat) -->
    <div class="pdf-header">
        <div class="inner">
            <div class="hdr-row">
                <div class="hdr-left">
                    <img src="<?php echo e(public_path('assets/logo.png')); ?>" alt="Logo">
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
            Terima kasih atas kepercayaan Anda. Kami menunggu kabar baik dari Anda.
            <br>
            <strong>IMMANUEL PRODUCTION</strong> &copy; <?php echo e(date('Y')); ?> <br>
            Email: <a href="mailto:admin@immanuelproduction.com">admin@immanuelproduction.com</a>
            - Telp: 0818550837
            - <strong>www.immanuelproduction.com</strong>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        
        <div class="quotation-title">INVOICE</div>
        <div class="quotation-sub">Tagihan</div>

        
        <div class="quotation-info">
            <table width="100%">
                <tr>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>Client</strong></td>
                                <td>: <?php echo e($invoice->client->name ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Event Date</strong></td>
                                <td>:
                                    <?php echo e($invoice->event_date ? \Carbon\Carbon::parse($invoice->event_date)->format('d M Y') : '-'); ?>

                                </td>
                            </tr>
                            <tr>
                                <td><strong>Event</strong></td>
                                <td>: <?php echo e($invoice->event_name ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Location</strong></td>
                                <td>: <?php echo e($invoice->location_event ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Pekerjaan</strong></td>
                                <td>: <?php echo e($invoice->workFlowLabel()); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>Invoice No</strong></td>
                                <td>: <?php echo e($invoice->invoice_number ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Invoice Date</strong></td>
                                <td>:
                                    <?php echo e($invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') : 'DRAFT'); ?>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        
        <table class="items">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>SIZE</th>
                    <th>HARGA / TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e($item->item_name); ?></td>
                        <td><?php echo e((float) $item->qty > 0 ? rtrim(rtrim(number_format((float) $item->qty, 2, ',', '.'), '0'), ',') : ''); ?></td>
                        <td><?php echo e((float) $item->length > 0 ? rtrim(rtrim(number_format((float) $item->length, 2, ',', '.'), '0'), ',') : ''); ?></td>
                        <?php if($item->price_group): ?>
                            <?php if(! isset($renderedPriceGroups[$item->price_group])): ?>
                                <?php ($renderedPriceGroups[$item->price_group] = true); ?>
                                <td rowspan="<?php echo e($priceGroupCounts[$item->price_group]); ?>" class="group-price"><?php echo e((int) $item->total > 0 ? 'Rp ' . number_format($item->total, 0, ',', '.') : ''); ?></td>
                            <?php endif; ?>
                        <?php else: ?>
                            <td class="group-price"><?php echo e((int) $item->total > 0 ? 'Rp ' . number_format($item->total, 0, ',', '.') : ''); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        
        <?php if(!empty($invoice->notes)): ?>
            <div class="notes">
                <strong>Catatan</strong><br>
                <?php echo nl2br(e($invoice->notes)); ?>

            </div>
        <?php endif; ?>

        <?php if($invoice->bankDetail): ?>
            <div class="bank-detail">
                <div class="bank-detail-title">Detail Rekening - <?php echo e($invoice->bankDetail->label); ?></div>
                <table>
                    <?php $__currentLoopData = ['Email' => $invoice->bankDetail->email, 'Bank' => $invoice->bankDetail->bank_name, 'Atas Nama' => $invoice->bankDetail->account_name, 'No Rek' => $invoice->bankDetail->account_number, 'NPWP' => $invoice->bankDetail->npwp, 'No HP' => $invoice->bankDetail->phone]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(filled($value)): ?><tr><td><?php echo e($label); ?></td><td>: <strong><?php echo e($value); ?></strong></td></tr><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
            </div>
        <?php endif; ?>

        
        <div class="summary">
            <div class="summary-title">Ringkasan</div>
            <table>
                <tr>
                    <td>Sub Total</td>
                    <td>:</td>
                    <td><?php echo e($subTotal > 0 ? 'Rp ' . number_format($subTotal, 0, ',', '.') : ''); ?></td>
                </tr>
                <?php if($discount > 0): ?>
                    <tr>
                        <td>Discount (<?php echo e(number_format($discPercent, 0, ',', '.')); ?>%)</td>
                        <td>:</td>
                        <td>- Rp <?php echo e(number_format($discount, 0, ',', '.')); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if($tax > 0): ?>
                    <tr>
                        <td>Potongan Pajak<?php echo e($invoice->tax_percent !== null ? ' (' . number_format($taxPercent, 2, ',', '.') . '%)' : ''); ?></td>
                        <td>:</td>
                        <td>- Rp <?php echo e(number_format($tax, 0, ',', '.')); ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Total Tagihan</td>
                    <td>:</td>
                    <td><?php echo e($grandTotal > 0 ? 'Rp ' . number_format($grandTotal, 0, ',', '.') : ''); ?></td>
                </tr>
                
                <?php if($dpCount > 1): ?>
                    <?php $__currentLoopData = $dps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>Pembayaran <?php echo e($i + 1); ?></td>
                            <td>:</td>
                            <td>- Rp <?php echo e(number_format((int) $p->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php elseif($dpCount === 1): ?>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>- Rp <?php echo e(number_format((int) $dps->first()->amount, 0, ',', '.')); ?></td>
                    </tr>
                <?php endif; ?>

                <tr class="emph">
                    <td><strong>Sisa Tagihan</strong></td>
                    <td>:</td>
                    <td><strong><?php echo e($balanceDue > 0 ? 'Rp ' . number_format($balanceDue, 0, ',', '.') : ''); ?></strong></td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/invoices/pdf.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Quotation</title>

    <style>
        /* ====== A4 + ruang header/footer (dompdf-friendly) ====== */
        @page {
            size: A4;
            margin: 48mm 14mm 24mm 14mm;
            /* top right bottom left */
        }

        html,
        body {
            margin: 0;
            height: 100%;
            line-height: 0.8;
            /* atur rapatnya paragraf */
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
            font-family: 'poppins', sans-serif;
        }

        strong,
        b,
        h1,
        h2,
        h3 {
            font-weight: 700;
        }


        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #333;
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
            /* harus sama/kurang dari @page margin-top */
            background: #fff;
        }

        .pdf-header .inner {
            padding: 0 14mm;
            /* samakan dengan @page left/right */
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
            font-weight: bold;
            font-size: 14px;
        }

        .hdr-right .addr {
            color: #555;
            line-height: 1;
        }

        /* ====== FOOTER (repeat tiap halaman) ====== */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 24mm;
            /* harus sama/kurang dari @page margin-bottom */
            background: #fff;
            border-top: 1px solid var(--line);
            text-align: center;
            color: #444;
            font-size: 11px;
            text-align: center;
        }

        .pdf-footer .inner {
            display: inline-block;
            /* biar lebar sesuai konten */
            background: var(--soft);
            border: 1px dashed var(--line);
            border-radius: 6px;
            padding: 10px 14px;
            line-height: 1;
            /* jarak antar baris */
            text-align: center;
        }

        .pdf-footer a {
            color: #444;
            text-decoration: none;
            font-weight: 600;
        }

        /* masker anti-bleed merah (thead) saat page break tepat di atas footer */
        .pdf-footer::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -2px;
            height: 2px;
            background: #fff;
        }

        /* ====== CONTENT (ikuti area @page, jangan padding kiri/kanan lagi) ====== */
        .content {
            margin-top: 35mm;
            padding: 0 14mm;
        }

        /* ====== TITLE ====== */
        .quotation-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: var(--red);
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
            background: var(--red);
            opacity: .15;
        }

        .quotation-sub {
            color: #666;
            font-size: 10px;
            margin: 0px 0 10px;
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

        /* ====== ITEMS TABLE (struktur asli) ====== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        table.items thead {
            display: table-header-group;
        }

        /* repeat tiap halaman */
        table.items th,
        table.items td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }

        table.items th {
            background-color: var(--red);
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

        .bank-detail {
            width: 52%;
            float: left;
            margin-top: 14px;
            padding: 10px 12px;
            border-left: 4px solid var(--red);
            background: var(--soft);
            line-height: 1.25;
            box-sizing: border-box;
        }

        .bank-detail-title { color: var(--red); font-size: 10px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 6px; }
        .bank-detail table { width: 100%; border-collapse: collapse; }
        .bank-detail td { padding: 2px 0; vertical-align: top; }
        .bank-detail td:first-child { width: 82px; color: var(--muted); }
        .group-price { vertical-align: middle !important; text-align: right !important; font-weight: 700; background: #fff; }

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
    <?php
        $priceGroupCounts = $quotation->items->whereNotNull('price_group')->countBy('price_group');
        $renderedPriceGroups = [];
    ?>

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
            Terima kasih atas kepercayaan Anda. Kami menunggu kabar baik dari Anda.
            <br>
            <strong>IMMANUEL PRODUCTION</strong> &copy; <?php echo e(date('Y')); ?> <br>
            Email: <a href="mailto:admin@immanuelproduction.com">admin@immanuelproduction.com</a>
            • Telp: 0818550837
            • <strong>www.immanuelproduction.com</strong>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        
        <div class="quotation-title">QUOTATION</div>
        <div class="quotation-sub">Penawaran Harga</div>

        
        <div class="quotation-info">
            <table width="100%">
                <tr>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>Client</strong></td>
                                <td>: <?php echo e($quotation->client->name); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Event Date</strong></td>
                                <td>:
                                    <?php echo e($quotation->event_date ? \Carbon\Carbon::parse($quotation->event_date)->format('d M Y') : '-'); ?>

                                </td>
                            </tr>
                            <tr>
                                <td><strong>Event</strong></td>
                                <td>: <?php echo e($quotation->event_name ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Location</strong></td>
                                <td>: <?php echo e($quotation->location_event ?? '-'); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top;">
                        <table>
                            <tr>
                                <td><strong>Quotation No</strong></td>
                                <td>: <?php echo e($quotation->quotation_number ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Quotation Date</strong></td>
                                <td>:
                                    <?php echo e($quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') : \Carbon\Carbon::now()->format('d M Y')); ?>

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
                <?php $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        <?php if($quotation->bankDetail): ?>
            <div class="bank-detail">
                <div class="bank-detail-title">Detail Rekening - <?php echo e($quotation->bankDetail->label); ?></div>
                <table>
                    <?php $__currentLoopData = ['Email' => $quotation->bankDetail->email, 'Bank' => $quotation->bankDetail->bank_name, 'Atas Nama' => $quotation->bankDetail->account_name, 'No Rek' => $quotation->bankDetail->account_number, 'NPWP' => $quotation->bankDetail->npwp, 'No HP' => $quotation->bankDetail->phone]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(filled($value)): ?><tr><td><?php echo e($label); ?></td><td>: <strong><?php echo e($value); ?></strong></td></tr><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
            </div>
        <?php endif; ?>

        
        <div class="summary">
            <div class="summary-title">Ringkasan</div>
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>:</td>
                    <td><?php echo e((int) $quotation->subtotal > 0 ? 'Rp ' . number_format($quotation->subtotal, 0, ',', '.') : ''); ?></td>
                </tr>
                <?php if((int) $quotation->discount > 0): ?><tr>
                    <td>Discount (<?php echo e(number_format($quotation->discount_percent, 0, ',', '.')); ?>%)</td>
                    <td>:</td>
                    <td>- Rp <?php echo e(number_format($quotation->discount, 0, ',', '.')); ?></td>
                </tr><?php endif; ?>
                <?php if((int) $quotation->tax_value > 0): ?><tr>
                    <td>Potongan Pajak<?php echo e($quotation->tax_percent !== null ? ' (' . number_format($quotation->tax_percent, 2, ',', '.') . '%)' : ''); ?></td>
                    <td>:</td>
                    <td>- Rp <?php echo e(number_format($quotation->tax_value, 0, ',', '.')); ?></td>
                </tr><?php endif; ?>
                <tr class="emph">
                    <td><strong>Total Penawaran</strong></td>
                    <td>:</td>
                    <td><strong><?php echo e((int) $quotation->grand_total > 0 ? 'Rp ' . number_format($quotation->grand_total, 0, ',', '.') : ''); ?></strong></td>
                </tr>
            </table>
        </div>

        
        <?php if(!empty($quotation->description)): ?>
            <div class="notes">
                <strong>Catatan:</strong><br>
                <?php echo nl2br(e($quotation->description)); ?>

            </div>
        <?php endif; ?>

    </div>
</body>

</html>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views/quotations/pdf.blade.php ENDPATH**/ ?>
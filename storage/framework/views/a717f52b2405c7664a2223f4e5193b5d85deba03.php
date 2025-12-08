<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        @page  {
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.3;
        }

        h2 {
            margin: 0 0 5px 0;
            text-align: center;
            font-size: 14px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 12px;
            font-size: 10px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 4px 6px;
            vertical-align: top;
        }

        th {
            text-align: left;
            background: #334155;
            color: white;
            font-weight: bold;
            font-size: 8px;
        }

        th.right {
            text-align: right;
        }

        /* Section Headers */
        .section-pendapatan {
            background: #059669;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .section-beban {
            background: #dc2626;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        /* Group Account */
        .row-group-pendapatan {
            background: #d1fae5;
            font-weight: bold;
        }

        .row-group-beban {
            background: #fee2e2;
            font-weight: bold;
        }

        /* Account */
        .row-account {
            background: #fff;
        }

        .row-account td:first-child {
            padding-left: 15px;
        }

        /* Sub Account */
        .row-subaccount {
            background: #f9fafb;
            color: #4b5563;
            font-size: 8px;
        }

        .row-subaccount td:first-child {
            padding-left: 30px;
        }

        /* Totals */
        .total-pendapatan {
            background: #047857;
            color: white;
            font-weight: bold;
        }

        .total-beban {
            background: #b91c1c;
            color: white;
            font-weight: bold;
        }

        .row-summary {
            background: #e2e8f0;
            font-weight: bold;
        }

        .row-pajak {
            background: #fef3c7;
            font-weight: bold;
        }

        .row-laba-bersih {
            background: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .spacer {
            height: 8px;
            background: transparent;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <h2>LAPORAN LABA RUGI</h2>
    <div class="subtitle">
        Periode: <?php echo e(\Carbon\Carbon::parse($start_date)->translatedFormat('d M Y')); ?>

        s/d <?php echo e(\Carbon\Carbon::parse($end_date)->translatedFormat('d M Y')); ?>

    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">KETERANGAN</th>
                <th class="right" style="width: 16%;">SUB ACCOUNT</th>
                <th class="right" style="width: 17%;">ACCOUNT</th>
                <th class="right" style="width: 17%;">GROUP ACCOUNT</th>
            </tr>
        </thead>
        <tbody>
            
            <?php if(!empty($groupsPendapatan)): ?>
                <tr class="section-pendapatan">
                    <td colspan="4">PENDAPATAN</td>
                </tr>
                <?php $__currentLoopData = $groupsPendapatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <tr class="row-group-pendapatan">
                        <td><?php echo e($group['group']); ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><?php echo e(number_format($group['saldo_group'], 2, ',', '.')); ?></td>
                    </tr>
                    <?php $__currentLoopData = $group['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $hasSubAccounts = !empty($account['sub_accounts']);
                        ?>
                        
                        
                        <tr class="row-account">
                            <td><?php echo e($account['nama_akun']); ?></td>
                            <td></td>
                            <td class="text-right"><?php echo e(number_format($account['saldo_account'], 2, ',', '.')); ?></td>
                            <td></td>
                        </tr>
                        
                        <?php if($hasSubAccounts): ?>
                            <?php $__currentLoopData = $account['sub_accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <tr class="row-subaccount">
                                    <td><?php echo e($sub['kode_akun']); ?> - <?php echo e($sub['nama_akun']); ?></td>
                                    <td class="text-right"><?php echo e(number_format($sub['saldo'], 2, ',', '.')); ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <tr class="total-pendapatan">
                    <td>TOTAL PENDAPATAN</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($totalPendapatan, 2, ',', '.')); ?></td>
                </tr>
            <?php endif; ?>

            
            <tr class="spacer"><td colspan="4"></td></tr>

            
            <?php if(!empty($groupsBeban)): ?>
                <tr class="section-beban">
                    <td colspan="4">BEBAN</td>
                </tr>
                <?php $__currentLoopData = $groupsBeban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <tr class="row-group-beban">
                        <td><?php echo e($group['group']); ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><?php echo e(number_format($group['saldo_group'], 2, ',', '.')); ?></td>
                    </tr>
                    <?php $__currentLoopData = $group['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $hasSubAccounts = !empty($account['sub_accounts']);
                        ?>
                        
                        
                        <tr class="row-account">
                            <td><?php echo e($account['nama_akun']); ?></td>
                            <td></td>
                            <td class="text-right"><?php echo e(number_format($account['saldo_account'], 2, ',', '.')); ?></td>
                            <td></td>
                        </tr>
                        
                        <?php if($hasSubAccounts): ?>
                            <?php $__currentLoopData = $account['sub_accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <tr class="row-subaccount">
                                    <td><?php echo e($sub['kode_akun']); ?> - <?php echo e($sub['nama_akun']); ?></td>
                                    <td class="text-right"><?php echo e(number_format($sub['saldo'], 2, ',', '.')); ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <tr class="total-beban">
                    <td>TOTAL BEBAN</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo e(number_format($totalBeban, 2, ',', '.')); ?></td>
                </tr>
            <?php endif; ?>

            
            <tr class="spacer"><td colspan="4"></td></tr>

            
            <tr class="row-summary">
                <td>LABA SEBELUM PAJAK PENGHASILAN</td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo e(number_format($labaSebelumPajak, 2, ',', '.')); ?></td>
            </tr>
            <tr class="row-pajak">
                <td>BEBAN PAJAK PENGHASILAN</td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo e(number_format($bebanPajak, 2, ',', '.')); ?></td>
            </tr>
            <tr class="row-laba-bersih">
                <td>LABA BERSIH SETELAH PAJAK PENGHASILAN</td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo e(number_format($labaSetelahPajak, 2, ',', '.')); ?></td>
            </tr>
        </tbody>
    </table>

    
    <div class="footer">
        Dicetak: <?php echo e(now()->format('d/m/Y H:i')); ?> — Halaman <span class="pagenum"></span>
    </div>
</body>

</html>
<?php /**PATH C:\laragon\www\rca\resources\views/income_statement/pdf.blade.php ENDPATH**/ ?>
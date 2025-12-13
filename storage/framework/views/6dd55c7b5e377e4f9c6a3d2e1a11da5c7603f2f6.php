<table border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center;">
            LAPORAN CASH FLOW
        </th>
    </tr>
    <tr>
        <th colspan="8" style="text-align: center;">
            Periode: <?php echo e(\Carbon\Carbon::parse($tanggalAwal)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')); ?>

        </th>
    </tr>
    <tr><td colspan="8"></td></tr>

    
    
    
    <?php if($displayMode == 'source'): ?>
        <?php
            $grouped = collect($rows)->groupBy('source');
            $grandCashIn = 0;
            $grandCashOut = 0;
        ?>

        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th>Tanggal</th>
            <th>Source</th>
            <th>Akun Kas/Bank</th>
            <th>Lawan Akun</th>
            <th>Keterangan</th>
            <th style="text-align: right;">Cash In</th>
            <th style="text-align: right;">Cash Out</th>
            <th></th>
        </tr>

        <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td colspan="8">Source: <?php echo e($source); ?></td>
            </tr>

            <?php
                $subtotalIn = 0;
                $subtotalOut = 0;
            ?>

            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                    <td><?php echo e($r['source']); ?></td>
                    <td><?php echo e($r['akun_kas']); ?></td>
                    <td><?php echo e($r['lawan_akun']); ?></td>
                    <td><?php echo e($r['keterangan']); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
                    <td></td>
                </tr>
                <?php
                    $subtotalIn += $r['cash_in'];
                    $subtotalOut += $r['cash_out'];
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr style="background-color: #f5f5f5; font-weight: bold;">
                <td colspan="5" style="text-align: right;">Subtotal (<?php echo e($source); ?>)</td>
                <td style="text-align: right;"><?php echo e(number_format($subtotalIn, 2)); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($subtotalOut, 2)); ?></td>
                <td></td>
            </tr>

            <?php
                $grandCashIn += $subtotalIn;
                $grandCashOut += $subtotalOut;
            ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr style="background-color: #d0d0d0; font-weight: bold;">
            <td colspan="5" style="text-align: right;">TOTAL KESELURUHAN</td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashIn, 2)); ?></td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashOut, 2)); ?></td>
            <td></td>
        </tr>

    
    
    
    <?php elseif($displayMode == 'account'): ?>
        <?php
            $grouped = collect($rows)->groupBy('akun_kas');
            $grandCashIn = 0;
            $grandCashOut = 0;
        ?>

        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th>Tanggal</th>
            <th>Source</th>
            <th>Lawan Akun</th>
            <th>Line Comment</th>
            <th style="text-align: right;">Cash In</th>
            <th style="text-align: right;">Cash Out</th>
            <th style="text-align: right;">Net</th>
            <th></th>
        </tr>

        <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akunKas => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $subtotalIn = $items->sum('cash_in');
                $subtotalOut = $items->sum('cash_out');
                $netCashFlow = $subtotalIn - $subtotalOut;
                $grandCashIn += $subtotalIn;
                $grandCashOut += $subtotalOut;
            ?>

            <tr style="background-color: #cce5ff; font-weight: bold;">
                <td colspan="4"><?php echo e($akunKas); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($subtotalIn, 2)); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($subtotalOut, 2)); ?></td>
                <td style="text-align: right; <?php echo e($netCashFlow >= 0 ? 'color: green;' : 'color: red;'); ?>"><?php echo e(number_format($netCashFlow, 2)); ?></td>
                <td></td>
            </tr>

            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                    <td><?php echo e($r['source']); ?></td>
                    <td><?php echo e($r['lawan_akun']); ?></td>
                    <td><?php echo e($r['line_comment']); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                    <td style="text-align: right;"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr style="background-color: #d0d0d0; font-weight: bold;">
            <td colspan="4" style="text-align: right;">TOTAL KESELURUHAN</td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashIn, 2)); ?></td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashOut, 2)); ?></td>
            <td style="text-align: right; <?php echo e(($grandCashIn - $grandCashOut) >= 0 ? 'color: green;' : 'color: red;'); ?>"><?php echo e(number_format($grandCashIn - $grandCashOut, 2)); ?></td>
            <td></td>
        </tr>

    
    
    
    <?php else: ?>
        <?php
            $grandCashIn = collect($rows)->sum('cash_in');
            $grandCashOut = collect($rows)->sum('cash_out');
        ?>

        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th>Tanggal</th>
            <th>Source</th>
            <th>Akun Kas/Bank</th>
            <th>Lawan Akun</th>
            <th>Line Comment</th>
            <th>Keterangan</th>
            <th style="text-align: right;">Cash In</th>
            <th style="text-align: right;">Cash Out</th>
        </tr>

        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                <td><?php echo e($r['source']); ?></td>
                <td><?php echo e($r['akun_kas']); ?></td>
                <td><?php echo e($r['lawan_akun']); ?></td>
                <td><?php echo e($r['line_comment']); ?></td>
                <td><?php echo e($r['keterangan']); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                <td style="text-align: right;"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr style="background-color: #d0d0d0; font-weight: bold;">
            <td colspan="6" style="text-align: right;">TOTAL KESELURUHAN</td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashIn, 2)); ?></td>
            <td style="text-align: right;"><?php echo e(number_format($grandCashOut, 2)); ?></td>
        </tr>
        <tr style="background-color: #e0e0e0; font-weight: bold;">
            <td colspan="6" style="text-align: right;">NET CASH FLOW</td>
            <td colspan="2" style="text-align: right; <?php echo e(($grandCashIn - $grandCashOut) >= 0 ? 'color: green;' : 'color: red;'); ?>">
                <?php echo e(number_format($grandCashIn - $grandCashOut, 2)); ?>

            </td>
        </tr>
    <?php endif; ?>
</table>
<?php /**PATH C:\laragon\www\rca\resources\views/arus_kas/export_excel.blade.php ENDPATH**/ ?>
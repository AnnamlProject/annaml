<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">
                LAPORAN LABA RUGI
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 12px;">
                Periode <?php echo e(\Carbon\Carbon::parse($start_date)->translatedFormat('d M Y')); ?>

                s/d <?php echo e(\Carbon\Carbon::parse($end_date)->translatedFormat('d M Y')); ?>

            </th>
        </tr>
        <tr><td colspan="4"></td></tr>
        <tr style="background-color: #334155; color: white; font-weight: bold;">
            <th style="text-align: left; padding: 8px;">KETERANGAN</th>
            <th style="text-align: right; padding: 8px;">SUB ACCOUNT</th>
            <th style="text-align: right; padding: 8px;">ACCOUNT</th>
            <th style="text-align: right; padding: 8px;">GROUP ACCOUNT</th>
        </tr>
    </thead>
    <tbody>
        
        <?php if(!empty($groupsPendapatan)): ?>
            <tr style="background-color: #059669; color: white;">
                <td colspan="4" style="font-weight: bold; padding: 6px;">PENDAPATAN</td>
            </tr>
            <?php $__currentLoopData = $groupsPendapatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <tr style="background-color: #d1fae5;">
                    <td style="font-weight: bold; padding: 6px;"><?php echo e($group['group']); ?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold; padding: 6px;"><?php echo e(number_format($group['saldo_group'], 2, ',', '.')); ?></td>
                </tr>
                <?php $__currentLoopData = $group['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $hasSubAccounts = !empty($account['sub_accounts']);
                    ?>
                    
                    
                    <tr>
                        <td style="padding-left: 20px; padding: 4px 4px 4px 20px;"><?php echo e($account['nama_akun']); ?></td>
                        <td></td>
                        <td style="text-align: right; padding: 4px;"><?php echo e(number_format($account['saldo_account'], 2, ',', '.')); ?></td>
                        <td></td>
                    </tr>
                    
                    <?php if($hasSubAccounts): ?>
                        <?php $__currentLoopData = $account['sub_accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <tr style="background-color: #f9fafb;">
                                <td style="padding-left: 40px; padding: 3px 3px 3px 40px; color: #4b5563;"><?php echo e($sub['kode_akun']); ?> - <?php echo e($sub['nama_akun']); ?></td>
                                <td style="text-align: right; padding: 3px; color: #4b5563;"><?php echo e(number_format($sub['saldo'], 2, ',', '.')); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <tr style="background-color: #047857; color: white;">
                <td style="font-weight: bold; padding: 8px;">TOTAL PENDAPATAN</td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-weight: bold; padding: 8px;"><?php echo e(number_format($totalPendapatan, 2, ',', '.')); ?></td>
            </tr>
        <?php endif; ?>

        
        <tr><td colspan="4" style="height: 15px;"></td></tr>

        
        <?php if(!empty($groupsBeban)): ?>
            <tr style="background-color: #dc2626; color: white;">
                <td colspan="4" style="font-weight: bold; padding: 6px;">BEBAN</td>
            </tr>
            <?php $__currentLoopData = $groupsBeban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <tr style="background-color: #fee2e2;">
                    <td style="font-weight: bold; padding: 6px;"><?php echo e($group['group']); ?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold; padding: 6px;"><?php echo e(number_format($group['saldo_group'], 2, ',', '.')); ?></td>
                </tr>
                <?php $__currentLoopData = $group['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $hasSubAccounts = !empty($account['sub_accounts']);
                    ?>
                    
                    
                    <tr>
                        <td style="padding-left: 20px; padding: 4px 4px 4px 20px;"><?php echo e($account['nama_akun']); ?></td>
                        <td></td>
                        <td style="text-align: right; padding: 4px;"><?php echo e(number_format($account['saldo_account'], 2, ',', '.')); ?></td>
                        <td></td>
                    </tr>
                    
                    <?php if($hasSubAccounts): ?>
                        <?php $__currentLoopData = $account['sub_accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <tr style="background-color: #f9fafb;">
                                <td style="padding-left: 40px; padding: 3px 3px 3px 40px; color: #4b5563;"><?php echo e($sub['kode_akun']); ?> - <?php echo e($sub['nama_akun']); ?></td>
                                <td style="text-align: right; padding: 3px; color: #4b5563;"><?php echo e(number_format($sub['saldo'], 2, ',', '.')); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <tr style="background-color: #b91c1c; color: white;">
                <td style="font-weight: bold; padding: 8px;">TOTAL BEBAN</td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-weight: bold; padding: 8px;"><?php echo e(number_format($totalBeban, 2, ',', '.')); ?></td>
            </tr>
        <?php endif; ?>

        
        <tr><td colspan="4" style="height: 15px;"></td></tr>

        
        <tr style="background-color: #e2e8f0;">
            <td style="font-weight: bold; padding: 8px;">LABA SEBELUM PAJAK PENGHASILAN</td>
            <td></td>
            <td></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><?php echo e(number_format($labaSebelumPajak, 2, ',', '.')); ?></td>
        </tr>
        <tr style="background-color: #fef3c7;">
            <td style="font-weight: bold; padding: 6px;">BEBAN PAJAK PENGHASILAN</td>
            <td></td>
            <td></td>
            <td style="text-align: right; font-weight: bold; padding: 6px;"><?php echo e(number_format($bebanPajak, 2, ',', '.')); ?></td>
        </tr>
        <tr style="background-color: #1e40af; color: white;">
            <td style="font-weight: bold; padding: 10px; font-size: 14px;">LABA BERSIH SETELAH PAJAK PENGHASILAN</td>
            <td></td>
            <td></td>
            <td style="text-align: right; font-weight: bold; padding: 10px; font-size: 14px;"><?php echo e(number_format($labaSetelahPajak, 2, ',', '.')); ?></td>
        </tr>
    </tbody>
</table>
<?php /**PATH C:\laragon\www\rca\resources\views/income_statement/excel.blade.php ENDPATH**/ ?>
<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">
                LAPORAN NERACA
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 12px;">
                Per <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y')); ?>

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
        <?php
            $tipeConfig = [
                'Aset' => ['headerBg' => '#2563eb', 'groupBg' => '#dbeafe'],
                'Kewajiban' => ['headerBg' => '#d97706', 'groupBg' => '#fef3c7'],
                'Ekuitas' => ['headerBg' => '#7c3aed', 'groupBg' => '#ede9fe'],
            ];
            $norm = fn($v) => strtoupper(trim((string) $v));
        ?>

        <?php $__currentLoopData = ['Aset', 'Kewajiban', 'Ekuitas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!empty($neraca[$tipe])): ?>
                <?php
                    $config = $tipeConfig[$tipe];
                    $currentGroupName = null;
                    $currentGroupTotal = 0;
                ?>

                
                <tr style="background-color: <?php echo e($config['headerBg']); ?>; color: white;">
                    <td colspan="4" style="font-weight: bold; padding: 6px;"><?php echo e(strtoupper($tipe)); ?></td>
                </tr>

                <?php $__currentLoopData = $neraca[$tipe]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($akun['level_akun'] === 'HEADER'): ?>
                        <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                            <tr style="background-color: <?php echo e($config['groupBg']); ?>;">
                                <td style="font-weight: bold; padding: 4px;">Subtotal <?php echo e($currentGroupName); ?></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right; font-weight: bold; padding: 4px;"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                            </tr>
                            <?php $currentGroupName = null; $currentGroupTotal = 0; ?>
                        <?php endif; ?>
                        <tr style="background-color: #f3f4f6;">
                            <td colspan="4" style="font-weight: bold; padding: 6px;"><?php echo e($akun['nama_akun']); ?></td>
                        </tr>

                    
                    <?php elseif($akun['level_akun'] === 'GROUP ACCOUNT'): ?>
                        <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                            <tr style="background-color: <?php echo e($config['groupBg']); ?>;">
                                <td style="font-weight: bold; padding: 4px;">Subtotal <?php echo e($currentGroupName); ?></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right; font-weight: bold; padding: 4px;"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php
                            $currentGroupName = $akun['nama_akun'];
                            $currentGroupTotal = 0;
                        ?>
                        <tr style="background-color: <?php echo e($config['groupBg']); ?>;">
                            <td style="font-weight: bold; padding: 6px;"><?php echo e($akun['nama_akun']); ?></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right; font-weight: bold; padding: 6px;"></td>
                        </tr>

                    
                    <?php elseif($akun['level_akun'] === 'ACCOUNT'): ?>
                        <?php
                            $parentSaldo = $akun['saldo'] ?? 0;
                            $parentCode = (string) $akun['kode_akun'];
                            $parentPrefix = rtrim($parentCode, '0');

                            $childAccounts = collect($neraca[$tipe])->filter(
                                fn($sub) => $norm($sub['level_akun']) === 'SUB ACCOUNT' &&
                                    \Illuminate\Support\Str::startsWith((string) $sub['kode_akun'], $parentPrefix)
                            );
                            $hasChild = $childAccounts->isNotEmpty();
                            $currentGroupTotal += $parentSaldo;
                        ?>

                        
                        <tr>
                            <td style="padding-left: 20px; padding: 4px 4px 4px 20px;"><?php echo e($akun['kode_akun']); ?> - <?php echo e($akun['nama_akun']); ?></td>
                            <td></td>
                            <td style="text-align: right; padding: 4px;"><?php echo e(number_format($parentSaldo, 2, ',', '.')); ?></td>
                            <td></td>
                        </tr>

                        
                        <?php if($hasChild): ?>
                            <?php $__currentLoopData = $childAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr style="background-color: #f9fafb;">
                                    <td style="padding-left: 40px; padding: 4px 4px 4px 40px; color: #6b7280;"><?php echo e($sub['kode_akun']); ?> - <?php echo e($sub['nama_akun']); ?></td>
                                    <td style="text-align: right; padding: 4px; color: #6b7280;"><?php echo e(number_format($sub['saldo'] ?? 0, 2, ',', '.')); ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                    
                    <?php elseif($akun['level_akun'] === 'SUB ACCOUNT'): ?>
                        <?php continue; ?>

                    
                    <?php else: ?>
                        <?php $currentGroupTotal += $akun['saldo'] ?? 0; ?>
                        <tr>
                            <td style="padding-left: 20px; padding: 4px 4px 4px 20px; font-style: italic;"><?php echo e($akun['nama_akun']); ?></td>
                            <td></td>
                            <td style="text-align: right; padding: 4px;"><?php echo e(number_format($akun['saldo'] ?? 0, 2, ',', '.')); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                    <tr style="background-color: <?php echo e($config['groupBg']); ?>;">
                        <td style="font-weight: bold; padding: 4px;">Subtotal <?php echo e($currentGroupName); ?></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right; font-weight: bold; padding: 4px;"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                    </tr>
                <?php endif; ?>

                
                <tr style="background-color: <?php echo e($config['headerBg']); ?>; color: white;">
                    <td style="font-weight: bold; padding: 6px;">TOTAL <?php echo e(strtoupper($tipe)); ?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold; padding: 6px;">
                        <?php if($tipe === 'Aset'): ?>
                            <?php echo e(number_format($grandTotalAset, 2, ',', '.')); ?>

                        <?php elseif($tipe === 'Kewajiban'): ?>
                            <?php echo e(number_format($grandTotalKewajiban, 2, ',', '.')); ?>

                        <?php else: ?>
                            <?php echo e(number_format($grandTotalEkuitas, 2, ',', '.')); ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <tr><td colspan="4"></td></tr>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <tr style="background-color: #1e40af; color: white;">
            <td style="font-weight: bold; padding: 8px; font-size: 12px;">TOTAL KEWAJIBAN DAN EKUITAS</td>
            <td></td>
            <td></td>
            <td style="text-align: right; font-weight: bold; padding: 8px; font-size: 12px;"><?php echo e(number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.')); ?></td>
        </tr>
    </tbody>
</table>
<?php /**PATH C:\laragon\www\rca\resources\views/neraca/neraca_excel.blade.php ENDPATH**/ ?>
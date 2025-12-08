


<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-6">
        
        <div>
            <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600">
                <li><a href="<?php echo e(route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'excel'])); ?>">Export
                        to Excel</a></li>
                <li><a href="<?php echo e(route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'pdf'])); ?>">Export
                        to PDF</a></li>
                <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')" class="tab-link cursor-pointer">Modify</a>
                </li>
            </ul>
        </div>
        <div id="fileModify"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-7xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
                </h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <form method="GET" action="<?php echo e(route('neraca.neraca_report')); ?>"
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Show Account Number</label>
                            <div class="space-y-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="show_account_number" value="show_account_number"
                                        class="text-blue-600 focus:ring-blue-500"
                                        <?php echo e(request('show_account_number') == 'show_account_number' ? 'checked' : ''); ?>>
                                    <span class="ml-2 text-sm">Show Account Number</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hide Accounts With Zero Balance</label>
                            <div class="space-y-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hide_account_with_zero" value="hide_account_with_zero"
                                        class="text-blue-600 focus:ring-blue-500"
                                        <?php echo e(request('hide_account_with_zero') == 'hide_account_with_zero' ? 'checked' : ''); ?>>
                                    <span class="ml-2 text-sm">Hide Accounts With Zero Balance</span>
                                </label>
                            </div>
                        </div>
                        <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            <a href="<?php echo e(route('neraca.neraca_report')); ?>"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 font-medium text-sm rounded-md hover:bg-gray-200">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
                <div class="mt-4 text-right">
                    <button onclick="document.getElementById('fileModify').classList.add('hidden')"
                        class="text-sm text-gray-500 hover:text-gray-700">Tutup</button>
                </div>
            </div>
        </div>

        
        <div class="flex items-center gap-4 mb-4">
            <img src="<?php echo e(asset('storage/' . \App\Setting::get('logo', 'logo.jpg'))); ?>" alt="Logo" class="h-12">
            <h1 class="text-xl mt-5 font-bold uppercase"><?php echo e($siteTitle); ?></h1>
        </div>

        <h5 class="text-xl font-bold mb-2">NERACA</h5>
        <p class="text-gray-600 mb-6 uppercase">
            PER <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')); ?>

        </p>

        
        <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
            <table class="w-full text-sm" id="neracaTable">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-700 to-slate-800 text-white">
                        <th class="text-left py-3 px-4 font-semibold w-1/2">KETERANGAN</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">SUB ACCOUNT</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">ACCOUNT</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">GROUP ACCOUNT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                        $tipeConfig = [
                            'Aset' => ['headerBg' => 'bg-blue-600', 'groupBg' => 'bg-blue-50', 'groupBorder' => 'border-blue-400', 'textColor' => 'text-blue-800', 'totalBg' => 'bg-blue-700'],
                            'Kewajiban' => ['headerBg' => 'bg-amber-600', 'groupBg' => 'bg-amber-50', 'groupBorder' => 'border-amber-400', 'textColor' => 'text-amber-800', 'totalBg' => 'bg-amber-700'],
                            'Ekuitas' => ['headerBg' => 'bg-purple-600', 'groupBg' => 'bg-purple-50', 'groupBorder' => 'border-purple-400', 'textColor' => 'text-purple-800', 'totalBg' => 'bg-purple-700'],
                        ];
                        $norm = fn($v) => strtoupper(trim((string) $v));
                    ?>

                    <?php $__currentLoopData = ['Aset', 'Kewajiban', 'Ekuitas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipeIndex => $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($neraca[$tipe])): ?>
                            <?php
                                $config = $tipeConfig[$tipe];
                                $total = 0;
                                $currentGroupName = null;
                                $currentGroupTotal = 0;
                                $groupIndex = 0;
                            ?>

                            
                            <tr class="<?php echo e($config['headerBg']); ?> text-white">
                                <td colspan="4" class="py-2.5 px-4 font-bold text-base tracking-wide"><?php echo e(strtoupper($tipe)); ?></td>
                            </tr>

                            <?php $__currentLoopData = $neraca[$tipe]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akunIndex => $akun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php if($akun['level_akun'] === 'HEADER'): ?>
                                    
                                    <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                                        <tr class="<?php echo e($config['groupBg']); ?> border-t">
                                            <td class="py-2 px-4 font-semibold <?php echo e($config['textColor']); ?>">Subtotal <?php echo e($currentGroupName); ?></td>
                                            <td></td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-bold <?php echo e($config['textColor']); ?>"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                                        </tr>
                                        <?php $currentGroupName = null; $currentGroupTotal = 0; ?>
                                    <?php endif; ?>
                                    <tr class="bg-gray-100">
                                        <td colspan="4" class="py-2 px-4 font-bold text-gray-900"><?php echo e($akun['nama_akun']); ?></td>
                                    </tr>

                                
                                <?php elseif($akun['level_akun'] === 'GROUP ACCOUNT'): ?>
                                    
                                    <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                                        <tr class="<?php echo e($config['groupBg']); ?> border-t">
                                            <td class="py-2 px-4 font-semibold <?php echo e($config['textColor']); ?>">Subtotal <?php echo e($currentGroupName); ?></td>
                                            <td></td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-bold <?php echo e($config['textColor']); ?>"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php
                                        $currentGroupName = $akun['nama_akun'];
                                        $currentGroupTotal = 0;
                                        $groupIndex++;
                                    ?>
                                    
                                    <tr class="<?php echo e($config['groupBg']); ?> hover:bg-opacity-70 transition-colors cursor-pointer group-toggle border-l-4 <?php echo e($config['groupBorder']); ?>"
                                        data-target="<?php echo e($tipe); ?>-group-<?php echo e($groupIndex); ?>">
                                        <td class="py-2.5 px-4">
                                            <span class="flex items-center font-semibold <?php echo e($config['textColor']); ?>">
                                                <span class="toggle-icon mr-2 w-5 h-5 flex items-center justify-center <?php echo e(str_replace('bg-', 'bg-', $config['groupBg'])); ?> rounded text-xs font-bold">+</span>
                                                <?php echo e($akun['nama_akun']); ?>

                                            </span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td class="py-2.5 px-4 text-right font-bold <?php echo e($config['textColor']); ?> group-total" id="<?php echo e($tipe); ?>-group-<?php echo e($groupIndex); ?>-total"></td>
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
                                        $allAccounts = implode(',', array_merge([$akun['kode_akun']], $childAccounts->pluck('kode_akun')->all()));
                                        
                                        $total += $parentSaldo;
                                        $currentGroupTotal += $parentSaldo;
                                    ?>

                                    <?php if($hasChild): ?>
                                        
                                        <tr class="hidden <?php echo e($tipe); ?>-group-<?php echo e($groupIndex); ?> bg-white hover:bg-gray-50 transition-colors cursor-pointer account-toggle border-l-4 <?php echo e(str_replace('border-', 'border-', $config['groupBorder'])); ?> border-opacity-50"
                                            data-target="<?php echo e($tipe); ?>-account-<?php echo e($akunIndex); ?>">
                                            <td class="py-2 px-4 pl-10">
                                                <span class="flex items-center text-gray-700">
                                                    <span class="toggle-icon mr-2 text-gray-400 w-4 h-4 flex items-center justify-center bg-gray-100 rounded text-xs">+</span>
                                                    <?php echo e($showAccountNumber ? $akun['kode_akun'] . ' - ' . $akun['nama_akun'] : $akun['nama_akun']); ?>

                                                </span>
                                            </td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-medium text-gray-700"><?php echo e(number_format($parentSaldo, 2, ',', '.')); ?></td>
                                            <td></td>
                                        </tr>
                                        
                                        <?php $__currentLoopData = $childAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $showChild = !($hideAccountWithZero && ($sub['saldo_self'] ?? 0) == 0);
                                            ?>
                                            <?php if($showChild): ?>
                                                <tr class="hidden <?php echo e($tipe); ?>-account-<?php echo e($akunIndex); ?> bg-gray-50 hover:bg-gray-100 transition-colors border-l-4 border-gray-200">
                                                    <td class="py-1.5 px-4 pl-16">
                                                        <a href="<?php echo e(route('buku_besar.buku_besar_report', [
                                                            'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                            'end_date' => request('end_date') ?? $tanggalAkhir,
                                                            'selected_accounts' => $sub['kode_akun'],
                                                        ])); ?>"
                                                           class="text-gray-600 hover:text-blue-600 hover:underline">
                                                            <?php echo e($showAccountNumber ? $sub['kode_akun'] . ' - ' . $sub['nama_akun'] : $sub['nama_akun']); ?>

                                                        </a>
                                                    </td>
                                                    <td class="py-1.5 px-4 text-right text-gray-600"><?php echo e(number_format($sub['saldo_self'] ?? ($sub['saldo'] ?? 0), 2, ',', '.')); ?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        
                                        <tr class="hidden <?php echo e($tipe); ?>-group-<?php echo e($groupIndex); ?> bg-white hover:bg-gray-50 transition-colors border-l-4 <?php echo e(str_replace('border-', 'border-', $config['groupBorder'])); ?> border-opacity-50">
                                            <td class="py-2 px-4 pl-10">
                                                <a href="<?php echo e(route('buku_besar.buku_besar_report', [
                                                    'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                    'end_date' => request('end_date') ?? $tanggalAkhir,
                                                    'selected_accounts' => $allAccounts,
                                                ])); ?>"
                                                   class="text-gray-700 hover:text-blue-600 hover:underline">
                                                    <?php echo e($showAccountNumber ? $akun['kode_akun'] . ' - ' . $akun['nama_akun'] : $akun['nama_akun']); ?>

                                                </a>
                                            </td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-medium text-gray-700"><?php echo e(number_format($parentSaldo, 2, ',', '.')); ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>

                                
                                <?php elseif($akun['level_akun'] === 'SUB ACCOUNT'): ?>
                                    <?php continue; ?>

                                
                                <?php else: ?>
                                    <?php
                                        $total += $akun['saldo'] ?? 0;
                                        $currentGroupTotal += $akun['saldo'] ?? 0;
                                    ?>
                                    <tr class="hidden <?php echo e($tipe); ?>-group-<?php echo e($groupIndex); ?> bg-white hover:bg-gray-50">
                                        <td class="py-2 px-4 pl-10 italic text-gray-700"><?php echo e($akun['nama_akun']); ?></td>
                                        <td></td>
                                        <td class="py-2 px-4 text-right font-medium text-gray-700"><?php echo e(number_format($akun['saldo'] ?? 0, 2, ',', '.')); ?></td>
                                        <td></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <?php if($currentGroupName && $currentGroupTotal != 0): ?>
                                <tr class="<?php echo e($config['groupBg']); ?> border-t">
                                    <td class="py-2 px-4 font-semibold <?php echo e($config['textColor']); ?>">Subtotal <?php echo e($currentGroupName); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="py-2 px-4 text-right font-bold <?php echo e($config['textColor']); ?>"><?php echo e(number_format($currentGroupTotal, 2, ',', '.')); ?></td>
                                </tr>
                            <?php endif; ?>

                            
                            <tr class="<?php echo e($config['totalBg']); ?> text-white">
                                <td class="py-3 px-4 font-bold">TOTAL <?php echo e(strtoupper($tipe)); ?></td>
                                <td></td>
                                <td></td>
                                <td class="py-3 px-4 text-right font-bold text-lg">
                                    <?php if($tipe === 'Aset'): ?>
                                        <?php echo e(number_format($grandTotalAset, 2, ',', '.')); ?>

                                    <?php elseif($tipe === 'Kewajiban'): ?>
                                        <?php echo e(number_format($grandTotalKewajiban, 2, ',', '.')); ?>

                                    <?php else: ?>
                                        <?php echo e(number_format($grandTotalEkuitas, 2, ',', '.')); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>

                            
                            <tr class="h-4 bg-gray-100"><td colspan="4"></td></tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <tr class="bg-gradient-to-r from-indigo-700 to-purple-800 text-white">
                        <td class="py-4 px-4 font-bold text-base">TOTAL KEWAJIBAN DAN EKUITAS</td>
                        <td></td>
                        <td></td>
                        <td class="py-4 px-4 text-right font-bold text-xl"><?php echo e(number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.')); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
            <span class="flex items-center"><span class="w-3 h-3 bg-blue-200 rounded mr-1"></span> Aset</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-amber-200 rounded mr-1"></span> Kewajiban</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-purple-200 rounded mr-1"></span> Ekuitas</span>
            <span class="flex items-center"><i class="fas fa-plus-square text-gray-400 mr-1"></i> Klik untuk expand/collapse</span>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle GROUP
            document.querySelectorAll('.group-toggle').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return;
                    
                    const targetClass = this.getAttribute('data-target');
                    const targets = document.querySelectorAll('.' + targetClass);
                    const icon = this.querySelector('.toggle-icon');
                    
                    targets.forEach(function(target) {
                        if (target.classList.contains('hidden')) {
                            target.classList.remove('hidden');
                        } else {
                            target.classList.add('hidden');
                            // Also hide sub-accounts
                            const subTarget = target.getAttribute('data-target');
                            if (subTarget) {
                                document.querySelectorAll('.' + subTarget).forEach(function(sub) {
                                    sub.classList.add('hidden');
                                });
                                const subIcon = target.querySelector('.toggle-icon');
                                if (subIcon) subIcon.textContent = '+';
                            }
                        }
                    });
                    
                    icon.textContent = icon.textContent === '+' ? '-' : '+';
                });
            });

            // Toggle ACCOUNT
            document.querySelectorAll('.account-toggle').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return;
                    
                    const targetClass = this.getAttribute('data-target');
                    const targets = document.querySelectorAll('.' + targetClass);
                    const icon = this.querySelector('.toggle-icon');
                    
                    targets.forEach(function(target) {
                        target.classList.toggle('hidden');
                    });
                    
                    icon.textContent = icon.textContent === '+' ? '-' : '+';
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/neraca/neraca_report.blade.php ENDPATH**/ ?>
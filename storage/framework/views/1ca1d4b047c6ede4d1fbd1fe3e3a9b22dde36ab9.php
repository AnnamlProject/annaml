

<?php $__env->startSection('content'); ?>
    <div class="py-4">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-4">
                <div>
                    <ul class="flex border-b mb-3 space-x-4 text-[10px] font-medium text-gray-600">
                        <li>
                            
                        </li>
                        <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')"
                                class="tab-link cursor-pointer">Modify</a></li>
                    </ul>
                </div>
                <div id="fileModify"
                    class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
                    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-7xl">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
                        </h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <form method="GET" action="<?php echo e(route('arus_kas.arus_kas_report')); ?>"
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="periode" class="block text-sm font-semibold text-gray-700 mb-1">Periode
                                        Buku</label>
                                    <select name="periode_buku" id="periode"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">---Pilih---</option>
                                    </select>
                                </div>

                                
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Awal</label>
                                    <input type="date" id="start_date" name="start_date"
                                        value="<?php echo e(request('start_date', \Carbon\Carbon::parse($tanggalAwal)->format('Y-m-d'))); ?>"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Akhir</label>
                                    <input type="date" id="end_date" name="end_date"
                                        value="<?php echo e(request('end_date', \Carbon\Carbon::parse($tanggalAkhir)->format('Y-m-d'))); ?>"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                <input type="hidden" name="selected_accounts" value="<?php echo e(request('selected_accounts')); ?>">
                                <input type="hidden" name="display_mode" value="<?php echo e($displayMode); ?>">

                                
                                <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                        <i class="fas fa-filter mr-2"></i> Ok
                                    </button>
                                    <a href="<?php echo e(route('arus_kas.filter_arus_kas')); ?>"
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
                
                <h3 class="text-lg font-bold mb-2">LAPORAN CASH FLOW</h3>
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[10px]">
                        <span class="font-semibold">Periode:</span>
                        <?php echo e(\Carbon\Carbon::parse($tanggalAwal)->format('d M Y')); ?> -
                        <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')); ?>

                    </p>
                    <div class="text-[10px]">
                        <span class="font-semibold">Mode:</span>
                        <?php if($displayMode == 'source'): ?>
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[10px]">Detail per Source</span>
                        <?php elseif($displayMode == 'account'): ?>
                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[10px]">Per Account Kas/Bank</span>
                        <?php else: ?>
                            <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded text-[10px]">Universal</span>
                        <?php endif; ?>
                    </div>
                </div>

                
                
                
                <?php if($displayMode == 'source'): ?>
                    <?php
                        $grouped = collect($rows)->groupBy('source');
                        $grandCashIn = 0;
                        $grandCashOut = 0;
                    ?>

                    <table class="min-w-full border-collapse border border-gray-300 text-[10px]">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1 w-20">Tanggal</th>
                                <th class="border px-2 py-1 w-32">Source</th>
                                <th class="border px-2 py-1">Akun Kas/Bank</th>
                                <th class="border px-2 py-1">Lawan Akun</th>
                                <th class="border px-2 py-1">Keterangan</th>
                                <th class="border px-2 py-1 text-right w-24">Cash In</th>
                                <th class="border px-2 py-1 text-right w-24">Cash Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <tr class="bg-gray-200 font-semibold">
                                    <td colspan="7" class="px-2 py-1 text-left">
                                        Source: <?php echo e($source); ?>

                                    </td>
                                </tr>

                                <?php
                                    $subtotalIn = 0;
                                    $subtotalOut = 0;
                                ?>

                                
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-2 py-1"><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['source']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['akun_kas']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['lawan_akun']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['keterangan']); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
                                    </tr>

                                    <?php
                                        $subtotalIn += $r['cash_in'];
                                        $subtotalOut += $r['cash_out'];
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                
                                <tr class="font-semibold bg-gray-100">
                                    <td colspan="5" class="px-2 py-1 text-right">Subtotal (<?php echo e($source); ?>)</td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalIn, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalOut, 2)); ?></td>
                                </tr>

                                <?php
                                    $grandCashIn += $subtotalIn;
                                    $grandCashOut += $subtotalOut;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>

                        
                        <tfoot>
                            <tr class="bg-gray-300 font-bold">
                                <td colspan="5" class="px-2 py-1 text-right">TOTAL KESELURUHAN</td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashIn, 2)); ?></td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashOut, 2)); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                
                
                
                <?php elseif($displayMode == 'account'): ?>
                    <?php
                        $grouped = collect($rows)->groupBy('akun_kas');
                        $grandCashIn = 0;
                        $grandCashOut = 0;
                        $accountIndex = 0;
                    ?>

                    
                    <div class="mb-2 flex gap-2" x-data="{ allExpanded: false }">
                        <button type="button" 
                            @click="allExpanded = !allExpanded; document.querySelectorAll('[data-detail-rows]').forEach(el => el.style.display = allExpanded ? 'table-row' : 'none'); document.querySelectorAll('[data-toggle-icon]').forEach(el => el.classList.toggle('fa-chevron-right', !allExpanded)); document.querySelectorAll('[data-toggle-icon]').forEach(el => el.classList.toggle('fa-chevron-down', allExpanded))"
                            class="px-3 py-1 text-[10px] bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-expand-alt mr-1"></i> 
                            <span x-text="allExpanded ? 'Collapse All' : 'Expand All'">Expand All</span>
                        </button>
                    </div>

                    <table class="min-w-full border-collapse border border-gray-300 text-[10px]">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1 w-20">Tanggal</th>
                                <th class="border px-2 py-1 w-32">Source</th>
                                <th class="border px-2 py-1">Lawan Akun</th>
                                <th class="border px-2 py-1">Line Comment</th>
                                <th class="border px-2 py-1 text-right w-24">Cash In</th>
                                <th class="border px-2 py-1 text-right w-24">Cash Out</th>
                                <th class="border px-2 py-1 text-right w-24">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akunKas => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $subtotalIn = $items->sum('cash_in');
                                    $subtotalOut = $items->sum('cash_out');
                                    $netCashFlow = $subtotalIn - $subtotalOut;
                                    $grandCashIn += $subtotalIn;
                                    $grandCashOut += $subtotalOut;
                                    $accountIndex++;
                                ?>

                                
                                <tr class="bg-blue-100 font-semibold cursor-pointer hover:bg-blue-200" 
                                    onclick="toggleAccountRows(<?php echo e($accountIndex); ?>)">
                                    <td colspan="4" class="px-2 py-1 text-left">
                                        <i class="fas fa-chevron-right mr-1 text-blue-600 transition-transform" data-toggle-icon data-account="<?php echo e($accountIndex); ?>"></i>
                                        <i class="fas fa-wallet mr-1"></i><?php echo e($akunKas); ?>

                                    </td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalIn, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalOut, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right <?php echo e($netCashFlow >= 0 ? 'text-green-700' : 'text-red-700'); ?>">
                                        <?php echo e(number_format($netCashFlow, 2)); ?>

                                    </td>
                                </tr>

                                
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50" data-detail-rows data-account="<?php echo e($accountIndex); ?>" style="display: none;">
                                        <td class="border px-2 py-1"><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['source']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['lawan_akun']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['line_comment']); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
                                        <td class="border px-2 py-1 text-right"></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                
                                <tr class="font-semibold bg-blue-50" data-detail-rows data-account="<?php echo e($accountIndex); ?>" style="display: none;">
                                    <td colspan="4" class="px-2 py-1 text-right">Subtotal</td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalIn, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($subtotalOut, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right <?php echo e($netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                                        <?php echo e(number_format($netCashFlow, 2)); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>

                        
                        <tfoot>
                            <tr class="bg-gray-300 font-bold">
                                <td colspan="4" class="px-2 py-1 text-right">TOTAL KESELURUHAN</td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashIn, 2)); ?></td>
                                <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashOut, 2)); ?></td>
                                <td class="border px-2 py-1 text-right <?php echo e(($grandCashIn - $grandCashOut) >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e(number_format($grandCashIn - $grandCashOut, 2)); ?>

                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <script>
                        function toggleAccountRows(accountId) {
                            const rows = document.querySelectorAll(`[data-detail-rows][data-account="${accountId}"]`);
                            const icon = document.querySelector(`[data-toggle-icon][data-account="${accountId}"]`);
                            const isHidden = rows[0]?.style.display === 'none';
                            
                            rows.forEach(row => row.style.display = isHidden ? 'table-row' : 'none');
                            icon?.classList.toggle('fa-chevron-right', !isHidden);
                            icon?.classList.toggle('fa-chevron-down', isHidden);
                        }
                    </script>

                
                
                
                <?php else: ?>
                    <?php
                        $grandCashIn = collect($rows)->sum('cash_in');
                        $grandCashOut = collect($rows)->sum('cash_out');
                    ?>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300 text-[10px]">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-2 py-1 w-20">Tanggal</th>
                                    <th class="border px-2 py-1 w-32">Source</th>
                                    <th class="border px-2 py-1">Akun Kas/Bank</th>
                                    <th class="border px-2 py-1">Lawan Akun</th>
                                    <th class="border px-2 py-1">Line Comment</th>
                                    <th class="border px-2 py-1">Keterangan</th>
                                    <th class="border px-2 py-1 text-right w-24">Cash In</th>
                                    <th class="border px-2 py-1 text-right w-24">Cash Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-2 py-1"><?php echo e(\Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y')); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['source']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['akun_kas']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['lawan_akun']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['line_comment']); ?></td>
                                        <td class="border px-2 py-1"><?php echo e($r['keterangan']); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_in'], 2)); ?></td>
                                        <td class="border px-2 py-1 text-right"><?php echo e(number_format($r['cash_out'], 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                            
                            <tfoot>
                                <tr class="bg-gray-300 font-bold">
                                    <td colspan="6" class="px-2 py-1 text-right">TOTAL KESELURUHAN</td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashIn, 2)); ?></td>
                                    <td class="border px-2 py-1 text-right"><?php echo e(number_format($grandCashOut, 2)); ?></td>
                                </tr>
                                <tr class="bg-gray-200 font-semibold">
                                    <td colspan="6" class="px-2 py-1 text-right">NET CASH FLOW</td>
                                    <td colspan="2" class="border px-2 py-1 text-right <?php echo e(($grandCashIn - $grandCashOut) >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                                        <?php echo e(number_format($grandCashIn - $grandCashOut, 2)); ?>

                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/arus_kas/report_arus_kas.blade.php ENDPATH**/ ?>
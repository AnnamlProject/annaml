

<?php $__env->startSection('content'); ?>
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div>
                    <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600">
                        <li>
                            <a
                                href="<?php echo e(route('buku_besar.export', [
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'selected_accounts' => request('selected_accounts'),
                                    'format' => 'excel',
                                ])); ?>">
                                Export to Excel
                            </a>
                        </li>
                        <li>
                            <a
                                href="<?php echo e(route('buku_besar.export', [
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'selected_accounts' => request('selected_accounts'),
                                    'format' => 'pdf',
                                ])); ?>">
                                Export to PDF
                            </a>
                        </li>
                        
                        <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')"
                                class="tab-link">Modify</a></li>
                        <li><a href="#linked" class="tab-link"></a></li>
                    </ul>
                </div>
                <div id="fileModify"
                    class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
                    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-7xl">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
                        </h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <form method="GET" action="<?php echo e(route('buku_besar.buku_besar_report')); ?>"
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="periode" class="block text-sm font-semibold text-gray-700 mb-1">Periode
                                        Buku</label>
                                    <select name="periode_buku" id="periode"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">---Pilih---</option>
                                        <?php $__currentLoopData = $tahun_buku; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>" data-tahun="<?php echo e(trim($item->tahun)); ?>"
                                                <?php echo e(request('periode_buku') == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->tahun); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Awal</label>
                                    <input type="date" id="start_date" name="start_date"
                                        value="<?php echo e(request('start_date', \Carbon\Carbon::parse($start_date)->format('Y-m-d'))); ?>"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Akhir</label>
                                    <input type="date" id="end_date" name="end_date"
                                        value="<?php echo e(request('end_date', \Carbon\Carbon::parse($end_date)->format('Y-m-d'))); ?>"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Account</label>
                                    <input type="hidden" name="selected_accounts" id="selected_accounts"
                                        value="<?php echo e(request('selected_accounts')); ?>">

                                    <input type="text" id="search-account" placeholder="Cari akun..."
                                        class="border p-2 rounded mb-3 w-full" />

                                    <div class="border rounded shadow-sm max-h-60 overflow-y-auto">
                                        <table class="min-w-full text-sm text-left text-gray-700" id="account-table">
                                            <thead class="bg-gray-100 sticky top-0">
                                                <tr>
                                                    <th class="px-2 py-1">
                                                        <input type="checkbox" id="select-all" class="form-checkbox">
                                                    </th>
                                                    <th class="px-2 py-1">Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $selectedAccounts = explode(',', request('selected_accounts', ''));
                                                ?>
                                                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="hover:bg-gray-50" data-level="<?php echo e($akun->level_akun); ?>"
                                                        data-tipe="<?php echo e(strtolower($akun->tipe_akun)); ?>">
                                                        <td class="px-2 py-1">
                                                            <input type="checkbox" class="account-checkbox form-checkbox"
                                                                value="<?php echo e($akun->kode_akun); ?> - <?php echo e($akun->nama_akun); ?>"
                                                                <?php echo e(in_array($akun->kode_akun . ' - ' . $akun->nama_akun, $selectedAccounts) ? 'checked' : ''); ?>>
                                                        </td>
                                                        <td class="px-2 py-1">
                                                            <?php echo e($akun->kode_akun); ?> - <?php echo e($akun->nama_akun); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                                    <div class="space-y-1">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sort_by" value="transaction_number"
                                                class="text-blue-600 focus:ring-blue-500"
                                                <?php echo e(request('sort_by') == 'transaction_number' ? 'checked' : ''); ?>>
                                            <span class="ml-2 text-sm">Transaction Number</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="sort_by" value="date"
                                                class="text-blue-600 focus:ring-blue-500"
                                                <?php echo e(request('sort_by') == 'date' ? 'checked' : ''); ?>>
                                            <span class="ml-2 text-sm">Date</span>
                                        </label>
                                    </div>
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">For General Journal
                                        Entries</label>
                                    <div class="space-y-1">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="show_comment" value="transaction_comment"
                                                <?php echo e(request('show_comment') == 'transaction_comment' ? 'checked' : ''); ?>>
                                            <span class="ml-2 text-sm">Show Transaction Comment</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="show_comment" value="line_comment"
                                                <?php echo e(request('show_comment') == 'line_comment' ? 'checked' : ''); ?>>
                                            <span class="ml-2 text-sm">Show Line Comment</span>
                                        </label>
                                    </div>
                                </div>

                                
                                <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                        <i class="fas fa-filter mr-2"></i> Ok
                                    </button>
                                    <a href=""
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
                <h3 class="text-xl font-bold mb-4">LAPORAN BUKU BESAR</h3>
                <div class="mb-4">
                    <p class="text-sm">
                        <span class="font-semibold">Periode:</span>
                        <?php echo e(\Carbon\Carbon::parse($start_date)->format('d M Y')); ?> -
                        <?php echo e(\Carbon\Carbon::parse($end_date)->format('d M Y')); ?>

                    </p>
                </div>

                <?php if($rows->count() > 0): ?>
                    <?php $__currentLoopData = $groupedByAccount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaAkun => $akunRows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalDebit = $akunRows->sum('debits');
                            $totalKredit = $akunRows->sum('credits');

                            $akunPertama = $akunRows->first();
                            $kodeAkun = $akunPertama->chartOfAccount->kode_akun ?? '-';
                            $namaAkun = $akunPertama->chartOfAccount->nama_akun ?? '-';
                            $saldoBerjalan = $saldoAwalPerAkun[$kodeAkun] ?? 0;
                            $tipeAkun = strtolower($akunPertama->chartOfAccount->tipe_akun ?? '');
                        ?>

                        <div class="mb-6">
                            <h3 class="text-xs font-bold mb-2">
                                <?php echo e($kodeAkun); ?> - <?php echo e($namaAkun); ?>

                            </h3>

                            <div class="overflow-x-auto text-xs leading-tight">
                                <table style="table-layout: fixed; width: 100%;">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="w-20 px-2 py-1 border">Tanggal</th>
                                            <th class="w-50 px-2 py-1 text-center border">Comment</th>
                                            <th class="w-32 px-2 py-1 text-center border">Source</th>
                                            <th class="w-24 px-2 py-1 border text-right">Debits</th>
                                            <th class="w-24 px-2 py-1 border text-right">Credits</th>
                                            <th class="w-24 px-2 py-1 border text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <tr class="bg-gray-50">
                                            <td colspan="5" class="px-2 py-1 border text-center">Saldo Awal</td>
                                            <td class="px-2 py-1 border text-right">
                                                <?php echo e(number_format($saldoBerjalan, 2, ',', '.')); ?>

                                            </td>
                                        </tr>

                                        
                                        <?php $__currentLoopData = $akunRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $debit = $row->debits;
                                                $kredit = $row->credits;

                                                if (in_array($tipeAkun, ['aset', 'beban'])) {
                                                    $saldoBerjalan += $debit - $kredit;
                                                } else {
                                                    $saldoBerjalan += $kredit - $debit;
                                                }
                                            ?>
                                            <tr>
                                                <td class="px-2 py-1 border">
                                                    <?php echo e(optional($row->journalEntry)->tanggal); ?>

                                                </td>
                                                <td class="px-2 py-1 border">
                                                    <?php if($showComment == 'transaction_comment'): ?>
                                                        <?php echo e(optional($row->journalEntry)->comment ?? '-'); ?>

                                                    <?php else: ?>
                                                        <?php echo e($row->comment ?? '-'); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-2 py-1 border">
                                                    <a href="<?php echo e(route('journal_entry.edit', $row->journalEntry->id)); ?>">
                                                        <?php echo e(optional($row->journalEntry)->source ?? '-'); ?>

                                                    </a>
                                                </td>
                                                <td class="px-2 py-1 border text-right">
                                                    <a href="<?php echo e(route('journal_entry.show', $row->journalEntry->id)); ?>">
                                                        <?php echo e(number_format($debit, 2, ',', '.')); ?>

                                                    </a>
                                                </td>
                                                <td class="px-2 py-1 border text-right">
                                                    <a href="<?php echo e(route('journal_entry.show', $row->journalEntry->id)); ?>">
                                                        <?php echo e(number_format($kredit, 2, ',', '.')); ?>

                                                    </a>
                                                </td>
                                                <td class="px-2 py-1 border text-right">
                                                    <?php echo e(number_format($saldoBerjalan, 2, ',', '.')); ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        <tr class="bg-gray-100 font-semibold">
                                            <td colspan="3" class="px-2 py-1 text-right">Total</td>
                                            <td class="px-2 py-1 text-right">
                                                <?php echo e(number_format($totalDebit, 2, ',', '.')); ?>

                                            </td>
                                            <td class="px-2 py-1 text-right">
                                                <?php echo e(number_format($totalKredit, 2, ',', '.')); ?>

                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-gray-500">Tidak ada data untuk ditampilkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const periodeSelect = document.getElementById('periode');
            const tanggalInput = document.getElementById('start_date');
            const tanggalAkhir = document.getElementById('end_date');

            // helper untuk ganti tahun tapi pertahankan bulan/hari
            function gantiTahun(dateStr, tahunBaru) {
                if (!dateStr) return '';
                const parts = dateStr.split('-'); // [YYYY, MM, DD]
                return `${tahunBaru}-${parts[1]}-${parts[2]}`;
            }

            function setRangeFromOption(option) {
                const tahun = option?.getAttribute('data-tahun')?.trim();
                if (/^\d{4}$/.test(tahun)) {
                    tanggalInput.min = `${tahun}-01-01`;
                    tanggalInput.max = `${tahun}-12-31`;
                    tanggalAkhir.min = `${tahun}-01-01`;
                    tanggalAkhir.max = `${tahun}-12-31`;

                    // Ganti tahun saja, bulan & tanggal tetap
                    tanggalInput.value = gantiTahun(tanggalInput.value, tahun) || `${tahun}-01-01`;
                    tanggalAkhir.value = gantiTahun(tanggalAkhir.value, tahun) || `${tahun}-12-31`;
                } else {
                    tanggalInput.min = tanggalInput.max = '';
                    tanggalAkhir.min = tanggalAkhir.max = '';
                    tanggalInput.value = '';
                    tanggalAkhir.value = '';
                }
            }

            // Saat select berubah
            periodeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                setRangeFromOption(selectedOption);
            });

            // Saat pertama kali load
            const selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                setRangeFromOption(selectedOption);
            }
        });
    </script>
    <script>
        document.getElementById('menu-button').addEventListener('click', function() {
            document.getElementById('dropdown-menu').classList.toggle('hidden');
        });

        window.addEventListener('click', function(e) {
            const button = document.getElementById('menu-button');
            const menu = document.getElementById('dropdown-menu');
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.account-checkbox');
            const hiddenInput = document.getElementById('selected_accounts');

            // Toggle semua checkbox
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedAccounts();
            });

            // Saat checkbox akun diklik
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const row = cb.closest('tr');
                    const level = row.dataset.level;
                    const tipe = row.dataset.tipe;

                    // Kalau akun ini level Header → toggle semua akun dengan tipe sama
                    if (level && level.toLowerCase() === 'header') {
                        const allSameType = document.querySelectorAll(
                            `#account-table tbody tr[data-tipe="${tipe}"] .account-checkbox`
                        );
                        allSameType.forEach(cb2 => {
                            cb2.checked = cb.checked;
                        });
                    }

                    updateSelectedAccounts();
                });
            });

            function updateSelectedAccounts() {
                const selected = [];
                checkboxes.forEach(cb => {
                    if (cb.checked) selected.push(cb.value);
                });
                hiddenInput.value = selected.join(',');
            }
        });

        // Search/filter functionality
        document.getElementById('search-account').addEventListener('keyup', function() {
            var keyword = this.value.toLowerCase();
            var rows = document.querySelectorAll('#account-table tbody tr');

            rows.forEach(function(row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/buku_besar/buku_besar_report.blade.php ENDPATH**/ ?>
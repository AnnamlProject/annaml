

<?php $__env->startSection('content'); ?>
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                <?php
                    $themeColor = \App\Setting::get('theme_color', '#4F46E5');
                ?>

                <div class="sticky top-0 z-20 px-6 py-5 border-b border-gray-100 flex justify-between items-center"
                    style="background: <?php echo e($themeColor); ?>;">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3 text-white text-xl"></i>
                        General Journal
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <!-- Filter Button -->
                        <button onclick="document.getElementById('filterPanel').classList.toggle('hidden')"
                            class="inline-flex items-center px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-filter text-gray-500 mr-2"></i> Filter
                        </button>
                        <!-- File Button -->
                        <button onclick="document.getElementById('fileModal').classList.remove('hidden')"
                            class="inline-flex items-center px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-file-export text-blue-500 mr-2"></i> File
                        </button>
                        <!-- Add Button -->
                        
                    </div>
                </div>

                <div id="filterPanel"
                    class="<?php echo e(request('search') || request('filter_tipe') ? '' : 'hidden'); ?> px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="<?php echo e(route('journal_entry.index')); ?>">
                        <div class="flex flex-wrap gap-4">
                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                    placeholder="Cari level,nama, dan sifat"
                                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm w-64 shadow-sm" />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            
                            <select name="filter_bulan"
                                class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                                <option value="">Semua Bulan</option>
                                <option value="1" <?php echo e(request('filter_bulan') == '1' ? 'selected' : ''); ?>>Januari
                                </option>
                                <option value="2" <?php echo e(request('filter_bulan') == '2' ? 'selected' : ''); ?>>Februari
                                </option>
                                <option value="3" <?php echo e(request('filter_bulan') == '3' ? 'selected' : ''); ?>>Maret
                                </option>
                                <option value="4" <?php echo e(request('filter_bulan') == '4' ? 'selected' : ''); ?>>April
                                </option>
                                <option value="5" <?php echo e(request('filter_bulan') == '5' ? 'selected' : ''); ?>>Mei</option>
                                <option value="6" <?php echo e(request('filter_bulan') == '6' ? 'selected' : ''); ?>>Juni</option>
                                <option value="7" <?php echo e(request('filter_bulan') == '7' ? 'selected' : ''); ?>>Juli
                                </option>
                                <option value="8" <?php echo e(request('filter_bulan') == '8' ? 'selected' : ''); ?>>Agustus
                                </option>
                                <option value="9" <?php echo e(request('filter_bulan') == '9' ? 'selected' : ''); ?>>September
                                </option>
                                <option value="10" <?php echo e(request('filter_bulan') == '10' ? 'selected' : ''); ?>>Oktober
                                </option>
                                <option value="11" <?php echo e(request('filter_bulan') == '11' ? 'selected' : ''); ?>>November
                                </option>
                                <option value="12" <?php echo e(request('filter_bulan') == '12' ? 'selected' : ''); ?>>Desember
                                </option>
                            </select>

                            
                            <select name="filter_tahun"
                                class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                                <option value="">Semua Tahun</option>
                                <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tahun); ?>"
                                        <?php echo e(request('filter_tahun') == $tahun ? 'selected' : ''); ?>>
                                        <?php echo e($tahun); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>


                            <!-- Tombol Filter -->
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded shadow-sm hover:bg-blue-600 text-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>

                            <!-- Tombol Reset -->
                            <a href="<?php echo e(route('journal_entry.index')); ?>"
                                class="inline-flex items-center px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-times mr-1 text-gray-400"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table Container -->
                <div class="relative overflow-x-auto" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Source
                                </th>
                                <th scope="col"
                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Comment
                                </th>

                                <th scope="col"
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-2 py-1 text-center whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($loop->iteration); ?>

                                    </td>
                                    <td class="px-2 py-1 text-center whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($item->source); ?></div>
                                    </td>
                                    <td class="px-2 py-1 text-center whitespace-nowrap">
                                        <?php echo e($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-'); ?>

                                    </td>
                                    <td class="px-2 py-1 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($item->comment); ?></div>
                                    </td>

                                    <td class="px-2 py-1 text-center whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('journal_entry.update')): ?>
                                                <a href="<?php echo e(route('journal_entry.show', $item->id)); ?>"
                                                    class="text-blue-500 hover:text-blue-700 p-2 rounded-full hover:bg-blue-50 transition-colors"
                                                    title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('journal_entry.update')): ?>
                                                <a href="<?php echo e(route('journal_entry.edit', $item->id)); ?>"
                                                    class="text-yellow-500 hover:text-yellow-700 p-2 rounded-full hover:bg-yellow-50 transition-colors"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('journal_entry.delete')): ?>
                                                <form id="delete-form-<?php echo e($item->id); ?>"
                                                    action="<?php echo e(route('journal_entry.destroy', $item->id)); ?>" method="POST"
                                                    style="display: none;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                </form>

                                                <button type="button" onclick="confirmDelete(<?php echo e($item->id); ?>)"
                                                    class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-box-open text-4xl mb-4"></i>
                                            <p class="text-lg font-medium text-gray-500">No Journal Entry found</p>
                                            <a href="<?php echo e(route('journal_entry.create')); ?>"
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                                <i class="fas fa-plus mr-2"></i> Create First Journal Entry
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- File Modal -->
    <div id="fileModal"
        class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
            </h3>
            <div class="space-y-3 text-sm text-gray-700">
                <a href="<?php echo e(asset('template/journal_entry_import_template.xlsx')); ?>" download
                    class="block hover:bg-gray-50 p-2 rounded-lg">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i> Download Template Excel
                </a>

                <a href="<?php echo e(route('export.journal_entry')); ?>" class="block hover:bg-gray-50 p-2 rounded-lg">
                    <i class="fas fa-file-download mr-2 text-blue-500"></i> Export
                </a>

                <form action="<?php echo e(route('import.journal_entry')); ?>" method="POST" enctype="multipart/form-data"
                    class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <label class="block text-sm font-medium text-gray-700">Import File Excel:</label>
                    <input type="file" name="file" class="block w-full text-sm border rounded px-2 py-1" required>
                    <button type="submit" class="bg-green-500 text-white w-full py-1 rounded hover:bg-green-600 text-sm">
                        <i class="fas fa-file-upload mr-1"></i> Import
                    </button>
                </form>
            </div>
            <div class="mt-4 text-right">
                <button onclick="document.getElementById('fileModal').classList.add('hidden')"
                    class="text-sm text-gray-500 hover:text-gray-700">Tutup</button>
            </div>
        </div>
    </div>

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
    <style>
        /* Improved sticky header implementation */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* Table header sticky positioning */
        table thead {
            position: sticky;
            top: 68px;
            /* Height of the sticky-header */
            z-index: 10;
        }

        /* Beautiful scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Smooth transitions */
        tr {
            transition: background-color 0.2s ease;
        }
    </style>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/journal_entry/index.blade.php ENDPATH**/ ?>
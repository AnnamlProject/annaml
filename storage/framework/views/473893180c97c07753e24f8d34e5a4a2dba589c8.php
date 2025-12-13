

<?php $__env->startSection('content'); ?>
    
    <script src="//unpkg.com/alpinejs" defer></script>

    
    <?php if(session('success')): ?>
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <div
                class="flex items-start bg-green-50 border-l-4 border-green-600 rounded-lg shadow-md p-4 space-x-3 animate-fade-in-down">
                <i class="fas fa-check-circle text-green-600 text-xl mt-1"></i>
                <div>
                    <p class="font-semibold text-green-800">Berhasil!</p>
                    <p class="text-green-700 text-sm"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('not_found')): ?>
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2 text-red-600"></i>
                    <p class="text-sm"><?php echo e(session('not_found')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-md rounded-xl p-6">

                <form method="GET" action="<?php echo e(route('income_statement.income_statement_departement')); ?>"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            required>
                    </div>


                    
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Departemen</label>

                        
                        <input type="hidden" name="selected_departemens" id="selected_departemens">


                        <!-- Search Input -->
                        <input type="text" id="search-account" placeholder="Cari departemen..."
                            class="border p-2 rounded mb-3 w-full" />

                        <!-- Table Container -->
                        <div class="border rounded shadow-sm max-h-60 overflow-y-auto">
                            <table class="min-w-full text-sm text-left text-gray-700" id="account-table">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2">
                                            <input type="checkbox" id="select-all" class="form-checkbox">
                                        </th>
                                        <th class="px-4 py-2">Departemen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $departemens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2">
                                                <input type="checkbox" class="account-checkbox form-checkbox"
                                                    value="<?php echo e($item->kode); ?> - <?php echo e($item->deskripsi); ?>">
                                            </td>
                                            <td class="px-4 py-2">
                                                <?php echo e($item->kode); ?> - <?php echo e($item->deskripsi); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                    <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>

                        <a href=""
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 font-medium text-sm rounded-md hover:bg-gray-200">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                    </div>



                </form>

            </div>
        </div>
    </div>
    <div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deptCheckboxes = document.querySelectorAll('.account-checkbox');
            const hiddenDeptInput = document.getElementById('selected_departemens');

            function updateSelectedDepts() {
                const selected = [];
                deptCheckboxes.forEach(cb => {
                    if (cb.checked) selected.push(cb.value.split(' - ')[1]); // ambil deskripsi saja
                });
                hiddenDeptInput.value = selected.join(',');
            }

            deptCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedDepts));
        });
    </script>
    <!-- Script for Search and Select All -->
    <script>
        // Search/filter functionality
        document.getElementById('search-account').addEventListener('keyup', function() {
            var keyword = this.value.toLowerCase();
            var rows = document.querySelectorAll('#account-table tbody tr');

            rows.forEach(function(row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });

        // Select all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.account-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = event.target.checked;
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.account-checkbox');
            const hiddenInput = document.getElementById('selected_accounts');

            // Toggle all checkboxes
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedAccounts();
            });

            // Update hidden input when individual checkbox changes
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedAccounts);
            });

            function updateSelectedAccounts() {
                const selected = [];
                checkboxes.forEach(cb => {
                    if (cb.checked) selected.push(cb.value);
                });
                hiddenInput.value = selected.join(',');
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/income_statement/filter_income_statement_departement.blade.php ENDPATH**/ ?>
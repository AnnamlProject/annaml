

<?php $__env->startSection('content'); ?>
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <?php
                $themeColor = \App\Setting::get('theme_color', '#4F46E5');
            ?>
            <div class="bg-white shadow-lg rounded-xl p-6 border-t-4" style="border-color:<?php echo e($themeColor); ?>">
                <h2 class="mb-6 font-bold text-lg">Account Edit</h2>

                <form method="POST" action="<?php echo e(route('chartOfAccount.update', $chartOfAccounts->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <?php if($errors->any()): ?>
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Akun</label>
                            <input type="text" name="kode_akun"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(old('kode_akun', $chartOfAccounts->kode_akun)); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Akun</label>
                            <input type="text" name="nama_akun"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(old('nama_akun', $chartOfAccounts->nama_akun)); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Akun</label>
                            <select name="tipe_akun"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <?php $__currentLoopData = $tipe_akun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tipe); ?>"
                                        <?php echo e(old('tipe_akun', $chartOfAccounts->tipe_akun) == $tipe ? 'selected' : ''); ?>>
                                        <?php echo e($tipe); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level Akun</label>
                            <select name="level_akun"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <?php $__currentLoopData = ['HEADER', 'GROUP ACCOUNT', 'ACCOUNT', 'SUB ACCOUNT', 'X']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($level); ?>"
                                        <?php echo e(old('level_akun', $chartOfAccounts->level_akun) == $level ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($level)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Klasifikasi Akun</label>
                            <select name="klasifikasi_id" id="klasifikasi_akun_id"
                                class="w-full select-klasifikasi border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <?php $__currentLoopData = $klasifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($g->id); ?>"
                                        <?php echo e(isset($chartOfAccounts) && $chartOfAccounts->klasifikasi_id == $g->id ? 'selected' : ''); ?>>
                                        <?php echo e($g->nama_klasifikasi); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>


                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fiscal.access')): ?>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Akun</label>
                                <select name="fiscal_account_id" id="fiscal_account_id"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Fiscal --</option>
                                    <?php $__currentLoopData = $fiscalAccount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"
                                            <?php echo e(old('fiscal_account_id', $chartOfAccounts->fiscal_account_id ?? '') == $item->id ? 'selected' : ''); ?>>
                                            <?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <!-- Checkboxes -->
                        <div class="col-span-1 md:col-span-2 mt-4 space-y-2">
                            <div class="flex items-center">
                                <input id="omit" name="omit_zero_balance" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    <?php echo e(old('omit_zero_balance', $chartOfAccounts->omit_zero_balance) ? 'checked' : ''); ?>>
                                <label for="omit" class="ml-2 block text-sm text-gray-700">
                                    Omit from Financial Statements if Balance is Zero
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="project_allocation" name="allow_project_allocation" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    <?php echo e(old('allow_project_allocation', $chartOfAccounts->allow_project_allocation) ? 'checked' : ''); ?>>
                                <label for="project_allocation" class="ml-2 block text-sm text-gray-700">
                                    Allow Project Allocation
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="aktif" name="aktif" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    <?php echo e(old('aktif', $chartOfAccounts->aktif) ? 'checked' : ''); ?>>
                                <label for="aktif" class="ml-2 block text-sm text-gray-700">
                                    Inactive Account
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="is_income_tax" class="block text-gray-700 font-medium mb-1">Akun Pajak
                                Penghasilan</label>
                            <select name="is_income_tax" id="is_income_tax" required
                                class="w-1/3 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="1"
                                    <?php echo e(old('is_income_tax', $chartOfAccounts->is_income_tax ?? '') == '1' ? 'selected' : ''); ?>>
                                    Ya</option>
                                <option value="0"
                                    <?php echo e(old('is_income_tax', $chartOfAccounts->is_income_tax ?? '') == '0' ? 'selected' : ''); ?>>
                                    Tidak
                                </option>
                            </select>
                            <?php $__errorArgs = ['is_income_tax'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Catatan -->
                        <div class="col-span-1 md:col-span-2 mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Deskripsi</label>
                            <textarea name="catatan" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo e(old('catatan', $chartOfAccounts->catatan)); ?></textarea>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus Pajak</label>
                            <textarea name="catatan_pajak" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo e(old('catatan_pajak', $chartOfAccounts->catatan_pajak)); ?></textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="<?php echo e(route('chartOfAccount.index')); ?>"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md">
                            Process
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#klasifikasi_akun_id,#fiscal_account_id').select2({
            placeholder: "-- Pilih --",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\rca\resources\views/chartOfAccount/edit.blade.php ENDPATH**/ ?>
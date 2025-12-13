  <?php
      $themeColor = \App\Setting::get('theme_color', '#4F46E5');
  ?>
  <div class="bg-white shadow-lg rounded-xl p-6 border-t-4" style="border-color:<?php echo e($themeColor); ?>">
      <h3 class="text-xl font-semibold mb-4">Manajemen User</h3>

      <?php if(session('success')): ?>
          <div class="mb-4 text-green-600"><?php echo e(session('success')); ?></div>
      <?php endif; ?>

      <?php if($errors->any()): ?>
          <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
              <ul class="list-disc list-inside">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <li><?php echo e($error); ?></li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
          </div>
      <?php endif; ?>

      <!-- Form Tambah User -->
      <form action="<?php echo e(route('users.store')); ?>" method="POST" class="mb-6">
          <?php echo csrf_field(); ?>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                  <div>
                      <label>Employee</label>
                      <select id="employeeSelect" name="employee_id"
                          class="select-employee w-full border rounded px-3 py-2">
                          <option value="">- Pilih Employee -</option>
                          <?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($emp->id); ?>" data-nama="<?php echo e($emp->nama_karyawan); ?>"
                                  data-email="<?php echo e($emp->email); ?>">
                                  <?php echo e($emp->nama_karyawan); ?>

                              </option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>

                  </div>
              </div>
              <div>
                  <label>Nama</label>
                  <input type="text" name="name" id="nameInput" class="w-full border rounded px-3 py-2" required>
              </div>
              <div>
                  <label>Email</label>
                  <input type="email" name="email" id="emailInput" class="w-full border rounded px-3 py-2" required>
              </div>
              <div>
                  <label>Password</label>
                  <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
              </div>
              <div>
                  <label>Role</label>
                  <select name="role_id" class="w-full border rounded px-3 py-2">
                      <option value="">- Pilih Role -</option>
                      <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
              </div>
          </div>
          <div class="mt-4">
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
          </div>
      </form>

      <!-- Tabel User -->
      <table class="w-full table-auto text-left">
          <thead>
              <tr>
                  <th class="border-b py-2">Nama</th>
                  <th class="border-b py-2">Email</th>
                  <th class="border-b py-2">Role</th>
                  <th class="border-b py-2">Aksi</th>
              </tr>
          </thead>
          <tbody>
              <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                      <td class="py-2"><?php echo e($user->name); ?></td>
                      <td class="py-2"><?php echo e($user->email); ?></td>
                      <td class="py-2"><?php echo e($user->role->name ?? '-'); ?></td>
                      <td class="py-2 space-x-2">
                          <button
                              @click="document.getElementById('edit-user-<?php echo e($user->id); ?>').classList.toggle('hidden')"
                              class="text-blue-500">Edit</button>
                          <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST" class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                              <button class="text-red-500">Hapus</button>
                          </form>
                      </td>
                  </tr>

                  <!-- Form Edit -->
                  <tr id="edit-user-<?php echo e($user->id); ?>" class="hidden">
                      <td colspan="4">
                          <form action="<?php echo e(route('users.update', $user->id)); ?>" method="POST"
                              class="mt-2 bg-gray-100 p-4 rounded">
                              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                  <div>
                                      <label>Nama</label>
                                      <input type="text" name="name" value="<?php echo e($user->name); ?>"
                                          class="w-full border rounded px-3 py-2">
                                  </div>
                                  <div>
                                      <label>Email</label>
                                      <input type="email" name="email" value="<?php echo e($user->email); ?>"
                                          class="w-full border rounded px-3 py-2">
                                  </div>
                                  <div>
                                      <label>Password <small>(Kosongkan jika tidak diubah)</small></label>
                                      <input type="password" name="password" class="w-full border rounded px-3 py-2">
                                  </div>
                                  <div>
                                      <label>Role</label>
                                      <select name="role_id" class="w-full border rounded px-3 py-2">
                                          <option value="">- Pilih Role -</option>
                                          <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                              <option value="<?php echo e($role->id); ?>"
                                                  <?php echo e($user->role_id == $role->id ? 'selected' : ''); ?>>
                                                  <?php echo e($role->name); ?>

                                              </option>
                                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                      </select>
                                  </div>
                              </div>
                              <div class="mt-4">
                                  <button class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
                              </div>
                          </form>
                      </td>
                  </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
      </table>
  </div>

  
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
      $(document).ready(function() {
          $('.select-employee').select2({
              placeholder: "Search employee...",
              allowClear: true
          });
      });
  </script>

  <script>
      document.getElementById('employeeSelect').addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const name = selectedOption.getAttribute('data-nama');
          const email = selectedOption.getAttribute('data-email');

          document.getElementById('nameInput').value = name || '';
          document.getElementById('emailInput').value = email || '';
      });
  </script>
<?php /**PATH C:\laragon\www\rca\resources\views/users/_users.blade.php ENDPATH**/ ?>
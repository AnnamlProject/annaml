<div class="bg-white p-4 shadow rounded">
    <h3 class="text-xl font-semibold mb-4">Manajemen Role</h3>

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <!-- Form Tambah Role -->
    <form action="{{ route('roles.store') }}" method="POST" class="mb-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label>Nama Role</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label>Jenis Pengguna</label>
                <input type="text" name="guard_name" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <!-- Checkbox Permission -->
        <div class="mt-4">
            <label class="block font-semibold mb-2">Permissions</label>
            <label class="flex items-center mb-2 space-x-2">
                <input type="checkbox" id="select-all-create">
                <span><strong>Pilih Semua</strong></span>
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach ($permissions as $permission)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="permission-create" name="permissions[]"
                            value="{{ $permission->name }}">
                        <span>{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>

    <!-- Tabel Role -->
    <table class="w-full table-auto text-left">
        <thead>
            <tr>
                <th class="border-b py-2">Nama</th>
                <th class="border-b py-2">Deskripsi</th>
                <th class="border-b py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td class="py-2">{{ $role->name }}</td>
                    <td class="py-2">{{ $role->guard_name }}</td>
                    <td class="py-2 space-x-2">
                        <button @click="document.getElementById('edit-{{ $role->id }}').classList.toggle('hidden')"
                            class="text-blue-500">Edit</button>

                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500">Hapus</button>
                        </form>
                    </td>
                </tr>

                <!-- Form Edit Role -->
                <tr id="edit-{{ $role->id }}" class="hidden">
                    <td colspan="3">
                        <form action="{{ route('roles.update', $role->id) }}" method="POST"
                            class="mt-2 bg-gray-100 p-4 rounded">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label>Nama</label>
                                    <input type="text" name="name" value="{{ $role->name }}"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label>Jenis Pengguna</label>
                                    <input type="text" name="guard_name" value="{{ $role->guard_name }}"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                            </div>

                            <!-- Checkbox Permission -->
                            <div class="mt-4">
                                <label class="block font-semibold mb-2">Permissions</label>
                                <label class="flex items-center mb-2 space-x-2">
                                    <input type="checkbox" class="select-all-edit" data-id="{{ $role->id }}">
                                    <span><strong>Pilih Semua</strong></span>
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach ($permissions as $permission)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" class="permission-edit-{{ $role->id }}"
                                                name="permissions[]" value="{{ $permission->name }}"
                                                {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                            <span>{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Script Checkbox Pilih Semua -->
<script>
    // Tambah Role
    document.getElementById('select-all-create').addEventListener('change', function() {
        document.querySelectorAll('.permission-create').forEach(el => el.checked = this.checked);
    });

    // Edit Role (Multiple)
    document.querySelectorAll('.select-all-edit').forEach(selectAll => {
        selectAll.addEventListener('change', function() {
            let roleId = this.dataset.id;
            document.querySelectorAll(`.permission-edit-${roleId}`).forEach(el => el.checked = this
                .checked);
        });
    });
</script>

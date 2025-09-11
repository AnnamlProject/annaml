@extends('layouts.app')

@section('content')

    <div class="max-w-full mx-auto bg-white shadow-md rounded-xl p-8 mt-6">
        <form method="POST"
            action="{{ isset($shift_karyawan) ? route('shift_karyawan.update', $shift_karyawan->id) : route('shift_karyawan.store') }}">
            @csrf
            @if (isset($shift_karyawan))
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-4 gap-4 text-sm">
                <div class="mb-2">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" id="employee_id"
                        class="select2 w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Employee --</option>
                        @foreach ($karyawan as $g)
                            <option value="{{ $g->id }}"
                                {{ isset($shift_karyawan) && $shift_karyawan->employee_id == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label for="unit_kerja_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
                    <select name="unit_kerja_id" id="unit_kerja_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach ($unitKerja as $g)
                            <option value="{{ $g->id }}"
                                {{ isset($target_unit) && $target_unit->unit_kerja_id == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label for="wahana_id" class="block text-sm font-medium text-gray-700 mb-1">Wahana</label>
                    <select name="wahana_id" id="wahana_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Wahana --</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                    </label>
                    <input type="date" name="tanggal"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-2">
                    <label for="jenis_hari_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Hari</label>
                    <select name="jenis_hari_id" id="jenis_hari_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Jenis Hari --</option>
                        @foreach ($jenisHari as $g)
                            <option value="{{ $g->id }}"
                                {{ isset($shift_karyawan) && $shift_karyawan->jenis_hari_id == $g->id ? 'selected' : '' }}>
                                {{ $g->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-2">
                    <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai
                    </label>
                    <input type="time" name="jam_mulai"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div class="mb-2">
                    <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai
                    </label>
                    <input type="time" name="jam_selesai"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div class="mb-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih--</option>
                        <option value="Penetapan"
                            {{ old('status', $shift_karyawan->status ?? '') == 'Penetapan' ? 'selected' : '' }}>
                            Penetapan</option>
                        <option value="Perubahan"
                            {{ old('status', $shift_karyawan->status ?? '') == 'Perubahan' ? 'selected' : '' }}>
                            Perubahan</option>
                        <option value="Tambahan"
                            {{ old('status', $shift_karyawan->status ?? '') == 'Tambahan' ? 'selected' : '' }}>
                            Tambahan</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="posisi" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                    <select name="posisi" id="posisi" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih--</option>
                        <option value="petugas_1"
                            {{ old('posisi', $shift_karyawan->posisi ?? '') == 'petugas_1' ? 'selected' : '' }}>
                            Petugas 1</option>
                        <option value="petugas_2"
                            {{ old('posisi', $shift_karyawan->posisi ?? '') == 'petugas_2' ? 'selected' : '' }}>
                            Petugas 2</option>
                        <option value="petugas_3"
                            {{ old('posisi', $shift_karyawan->posisi ?? '') == 'petugas_3' ? 'selected' : '' }}>
                            Petugas 3</option>
                        <option value="petugas_4"
                            {{ old('posisi', $shift_karyawan->posisi ?? '') == 'petugas_4' ? 'selected' : '' }}>
                            Petugas 4</option>
                        <option value="pengganti"
                            {{ old('posisi', $shift_karyawan->posisi ?? '') == 'pengganti' ? 'selected' : '' }}>
                            Pengganti</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            {{-- Tombol --}}
            <div class="flex justify-end">
                <a href="{{ route('shift_karyawan.index') }}"
                    class="mr-3 inline-block px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit"
                    class="inline-block px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    {{ isset($shift_karyawan) ? '💾 Update' : '✅ Simpan' }}
                </button>
            </div>
        </form>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatRupiah(inputId) {
            const input = document.getElementById(inputId);
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = new Intl.NumberFormat('id-ID').format(value);
                e.target.value = value;
            });

            const form = input.closest('form');
            form.addEventListener('submit', function() {
                input.value = input.value.replace(/\./g, '');
            });
        }

        formatRupiah('shift_karyawan');
        formatRupiah('max_penghasilan');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit_kerja_id');
        const wahanaSelect = document.getElementById('wahana_id');

        if (unitSelect && wahanaSelect) {
            unitSelect.addEventListener('change', function() {
                let unitId = this.value;
                wahanaSelect.innerHTML = '<option value="">Loading...</option>';

                if (unitId) {
                    fetch('/wahana-by-unit/' + unitId)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Respon:", data); // debug isi respon
                            let options =
                                '<option value="">-- Pilih Wahana  --</option>';
                            if (data.length === 0) {
                                options =
                                    '<option value="">Tidak ada wahana untuk unit ini</option>';
                            } else {
                                data.forEach(function(item) {
                                    options +=
                                        `<option value="${item.id}">${item.nama_wahana}</option>`;
                                });
                            }
                            wahanaSelect.innerHTML = options;
                        })
                        .catch(err => console.error("Fetch error:", err));
                } else {
                    wahanaSelect.innerHTML =
                        '<option value="">-- Pilih Unit kerja Dulu --</option>';
                }
            });
        }
    });
</script>

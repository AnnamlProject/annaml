@extends('layouts.app')

@section('content')

    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($data) ? route('komponen_penghasilan.update', $data->id) : route('komponen_penghasilan.store') }}">

                    @csrf
                    @if (isset($data))
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level Kepegawaian</label>
                            <select name="level_karyawan_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Level Kepegawaian --</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('level_karyawan_id', $data->level_karyawan_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Nama komponen -->
                        <div class="mb-4">
                            <label for="nama_komponen" class="block text-gray-700 font-medium mb-1">Nama
                                Komponen</label>
                            <input type="text" id="name" name="nama_komponen" required
                                value="{{ old('nama_komponen', $data->nama_komponen ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nama_komponen')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tipe" class="block text-gray-700 font-medium mb-1">Tipe</label>
                            <select name="tipe" id="tipe" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Penambah Gaji Bruto"
                                    {{ old('tipe', $data->tipe ?? '') == 'Penambah Gaji Bruto' ? 'selected' : '' }}>
                                    Penambah Gaji Bruto</option>
                                <option value="Penambah Penghasilan Bruto"
                                    {{ old('tipe', $data->tipe ?? '') == 'Penambah Penghasilan Bruto' ? 'selected' : '' }}>
                                    Penambah Penghasilan Bruto</option>
                                <option value="Pengurang Penghasilan Bruto"
                                    {{ old('tipe', $data->tipe ?? '') == 'Pengurang Penghasilan Bruto' ? 'selected' : '' }}>
                                    Pengurang Penghasilan Bruto</option>
                            </select>
                            @error('tipe')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kategori" class="block text-gray-700 font-medium mb-1">Deskripsi</label>
                            <input type="text" id="name" name="kategori" required
                                value="{{ old('kategori', $data->kategori ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kategori')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="sifat" class="block text-gray-700 font-medium mb-1">sifat</label>
                            <select name="sifat" id="sifat" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Tunai" {{ old('sifat', $data->sifat ?? '') == 'Tunai' ? 'selected' : '' }}>
                                    Tunai</option>
                                <option value="Non Tunai"
                                    {{ old('sifat', $data->sifat ?? '') == 'Non Tunai' ? 'selected' : '' }}>Non Tunai
                                </option>
                            </select>
                            @error('sifat')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="periode_perhitungan" class="block text-gray-700 font-medium mb-1">Periode
                                Perhitungan</label>
                            <select name="periode_perhitungan" id="periode_perhitungan" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih--</option>
                                <option value="Jam"
                                    {{ old('periode_perhitungan', $data->periode_perhitungan ?? '') == 'Jam' ? 'selected' : '' }}>
                                    Jam</option>
                                <option value="Harian"
                                    {{ old('periode_perhitungan', $data->periode_perhitungan ?? '') == 'Harian' ? 'selected' : '' }}>
                                    Harian</option>
                                <option value="Mingguan"
                                    {{ old('periode_perhitungan', $data->periode_perhitungan ?? '') == 'Mingguan' ? 'selected' : '' }}>
                                    Mingguan</option>
                                <option value="Bulanan"
                                    {{ old('periode_perhitungan', $data->periode_perhitungan ?? '') == 'Bulanan' ? 'selected' : '' }}>
                                    Bulanan</option>
                            </select>
                            @error('periode_perhitungan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status_komponen" class="block text-gray-700 font-medium mb-1">Status
                                Komponen</label>
                            <select name="status_komponen" id="status_komponen" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Aktif"
                                    {{ old('status_komponen', $data->status_komponen ?? '') == 'Aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="Non Aktif"
                                    {{ old('status_komponen', $data->status_komponen ?? '') == 'Non Aktif' ? 'selected' : '' }}>
                                    Non Aktif</option>
                            </select>
                            @error('status_komponen')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_kehadiran" value="1" class="form-check-input"
                                id="isKehadiran">
                            <label class="form-check-label" for="isKehadiran">
                                Apakah komponen ini untuk Kehadiran?
                            </label>
                            <br>
                            <small class="form-text text-muted">
                                Centang jika komponen ini digunakan untuk perhitungan otomatis berdasarkan jumlah hadir
                                karyawan.
                            </small>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($data) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('komponen_penghasilan.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function toggleAutoGenerate() {
            const checkbox = document.getElementById('auto_generate');
            const manualField = document.getElementById('manual_kd');

            if (checkbox.checked) {
                manualField.style.display = 'none';
            } else {
                manualField.style.display = 'block';
            }
        }

        // Inisialisasi saat halaman pertama kali dimuat
        window.onload = toggleAutoGenerate;
    </script>
@endsection

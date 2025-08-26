@extends('layouts.app')

@section('content')
    <div class="py-10 max-w-full mx-auto px-6">
        <form action="{{ route('employee.store') }}" enctype="multipart/form-data" method="POST"
            class="bg-white p-6 rounded-xl shadow-sm space-y-6">

            @csrf
            @if ($errors->any())
                <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Data Pribadi --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label>Kode Karyawan</label>
                    <input type="text" name="kode_karyawan" class="w-full border p-2" required>
                </div>
                <div>
                    <label>Nama Karyawan</label>
                    <input type="text" name="nama_karyawan" class="w-full border p-2" required>
                </div>
                <div>
                    <label>NIK</label>
                    <input type="text" name="nik" class="w-full border p-2" required>
                </div>
                <div>
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="w-full border p-2">
                </div>
                <div>
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="w-full border p-2">
                </div>
                <div>
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border p-2">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label>Golongan Darah</label>
                    <input type="text" name="golongan_darah" class="w-full border p-2">
                </div>
                <div>
                    <label>Tinggi Badan</label>
                    <input type="text" name="tinggi_badan" class="w-full border p-2">
                </div>
            </div>

            {{-- Kontak --}}
            <div class="mb-4">
                <label>Alamat</label>
                <textarea name="alamat" class="w-full border p-2"></textarea>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label>Telepon</label>
                    <input type="text" name="telepon" class="w-full border p-2">
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" class="w-full border p-2">
                </div>
            </div>

            {{-- Lain-lain --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label>Agama</label>
                    <select name="agama" id="agama" class="w-full border p-2">
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Kristen Protestan">Kristen Protestan</option>
                        <option value="Konghucu">Konghucu</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                    </select>
                </div>
                <div>
                    <label>Kewarganegaraan</label>
                    <select name="kewarganegaraan" id="kewarganegaraan" class="w-full border p-2">
                        <option value="wni">WNI</option>
                        <option value="wna">WNA</option>
                    </select>
                </div>
                <div>
                    <label>Status Pernikahan</label>
                    <input type="text" name="status_pernikahan" class="w-full border p-2">
                </div>

                <div>
                    <label>PTKP</label>
                    <select name="ptkp_id" class="w-full border p-2">
                        @foreach ($ptkps as $ptkp)
                            <option value="{{ $ptkp->id }}">{{ $ptkp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Jabatan</label>
                    <select name="jabatan_id" class="w-full border p-2">
                        @foreach ($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Level Kepegawaian</label>
                    <select name="level_kepegawaian_id" class="w-full border p-2">
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->nama_level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Unit Kerja</label>
                    <select name="unit_kerja_id" class="w-full border p-2">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="w-full border p-2">
                </div>
                <div>
                    <label>Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" class="w-full border p-2">
                </div>
                <div>
                    <label>Status Pegawai</label>
                    <input type="text" name="status_pegawai" class="w-full border p-2">
                </div>
                <div>
                    <label>Sertifikat</label>
                    <input type="text" name="sertifikat" class="w-full border p-2">
                </div>
                <div>
                    <label>Foto</label>
                    <input type="file" name="photo" class="w-full border p-2">
                    @error('photo')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                </div>
                <div>
                    <label>Foto KTP</label>
                    <input type="file" name="foto_ktp" class="w-full border p-2">
                    @error('foto_ktp')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror

                </div>
                <div>
                    <label for="">Tempelkan Kartu RFID</label>
                    <input type="text" name="rfid_code" class="w-full border p-2">
                    @error('rfid_code')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="text-right">
                <a href="{{ route('employee.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rfidInput = document.getElementById("rfid");
            let buffer = "";

            // Tangkap inputan keyboard
            document.addEventListener("keypress", function(e) {
                const char = String.fromCharCode(e.which || e.keyCode);

                if (e.key === "Enter") {
                    // Ketika RFID selesai dikirim (biasanya diakhiri ENTER)
                    if (buffer.length >= 8) {
                        rfidInput.value = buffer.trim();
                        buffer = "";
                    }
                    e.preventDefault(); // mencegah form terkirim otomatis
                } else {
                    buffer += char;
                }
            });
        });
    </script>

@endsection

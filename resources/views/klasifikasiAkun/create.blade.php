@extends('layouts.app')

@section('content')
    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-full mx-auto">
        <div class="bg-white shadow-lg rounded-2xl p-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Tambah Klasifikasi Akun</h2>
            <form action="{{ route('klasifikasiAkun.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Kode Klasifikasi</label>
                    <input type="text" name="kode_klasifikasi"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Nama Klasifikasi</label>
                    <input type="text" name="nama_klasifikasi"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Grup Numbering Akun</label>
                    <select name="numbering_account_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Grup --</option>
                        @foreach ($numberingAccounts as $group)
                            <option value="{{ $group->id }}">
                                {{ $group->nama_grup }} ({{ $group->nomor_akun_awal }} - {{ $group->nomor_akun_akhir }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Aktif?</label>
                    <select name="aktif"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="1" selected>Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('klasifikasiAkun.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

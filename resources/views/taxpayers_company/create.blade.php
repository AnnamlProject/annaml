@extends('layouts.app')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($taxpayers) ? 'Edit Taxpayer' : 'Create Taxpayer' }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($taxpayers) ? route('taxpayers_company.update', $taxpayers->id) : route('taxpayers_company.store') }}">

                    @csrf
                    @if (isset($taxpayers))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        <!-- Nama jabatan -->
                        <div class="mb-4">
                            <label for="nama_perusahaan" class="block text-gray-700 font-medium mb-1">Nama Wajib
                                Pajak</label>
                            <input type="text" id="name" name="nama_perusahaan" required
                                value="{{ old('nama_perusahaan', $taxpayers->nama_perusahaan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nama_perusahaan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- deskripsi jabatan -->
                        <div class="mb-4 md:col-span-2">
                            <label for="jalan" class="block text-gray-700 font-medium mb-1">Jalan</label>
                            <textarea id="jalan" name="jalan" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('jalan', $taxpayers->jalan ?? '') }}</textarea>
                            @error('jalan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kelurahan" class="block text-gray-700 font-medium mb-1">kelurahan</label>
                            <input type="text" id="name" name="kelurahan" required
                                value="{{ old('kelurahan', $taxpayers->kelurahan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kelurahan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kecamatan" class="block text-gray-700 font-medium mb-1">Kecamatan</label>
                            <input type="text" id="name" name="kecamatan" required
                                value="{{ old('kecamatan', $taxpayers->kecamatan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kecamatan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kota" class="block text-gray-700 font-medium mb-1">Kota</label>
                            <input type="text" id="name" name="kota" required
                                value="{{ old('kota', $taxpayers->kota ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kota')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="provinsi" class="block text-gray-700 font-medium mb-1">Provinsi</label>
                            <input type="text" id="name" name="provinsi" required
                                value="{{ old('provinsi', $taxpayers->provinsi ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('provinsi')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kode_pos" class="block text-gray-700 font-medium mb-1">Kode Pos</label>
                            <input type="number" id="name" name="kode_pos" required
                                value="{{ old('kode_pos', $taxpayers->kode_pos ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kode_pos')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="phone_number" class="block text-gray-700 font-medium mb-1">No.Tlp/Hp (Tercatat
                                di
                                Coretax)</label>
                            <input type="number" id="name" name="phone_number" required
                                value="{{ old('phone_number', $taxpayers->phone_number ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-1">Email (Tercatat di
                                Coretax)</label>
                            <input type="email" id="name" name="email" required
                                value="{{ old('email', $taxpayers->email ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="bentuk_badan_hukum" class="block text-gray-700 font-medium mb-1">Bentuk Badan
                                Hukum</label>
                            <input type="text" id="bentuk_badan_hukum" name="bentuk_badan_hukum" required
                                value="{{ old('bentuk_badan_hukum', $taxpayers->bentuk_badan_hukum ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('bentuk_badan_hukum')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="npwp" class="block text-gray-700 font-medium mb-1">NPWP</label>
                            <input type="text" id="npwp" name="npwp" required
                                value="{{ old('npwp', $taxpayers->npwp ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('npwp')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="klu_code" class="block text-gray-700 font-medium mb-1">KLU Code</label>
                            <input type="text" id="klu_code" name="klu_code" required
                                value="{{ old('klu_code', $taxpayers->klu_code ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('klu_code')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 md:col-span-2">
                            <label for="klu_description" class="block text-gray-700 font-medium mb-1">KLU
                                Description</label>
                            <textarea id="klu_description" name="klu_description" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('klu_description', $taxpayers->klu_description ?? '') }}</textarea>
                            @error('klu_description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="tax_office" class="block text-gray-700 font-medium mb-1">Tax Office</label>
                            <input type="text" id="tax_office" name="tax_office" required
                                value="{{ old('tax_office', $taxpayers->tax_office ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('tax_office')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="font-weight-bold">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                name="logo">

                            <!-- error message untuk title -->
                            @error('logo')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($taxpayers) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('taxpayers_company.index') }}"
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

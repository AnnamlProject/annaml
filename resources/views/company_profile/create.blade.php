@extends('layouts.app')


@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            {{-- Tab Navigation --}}
            <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                <li><a href="#company_data" class="tab-link">Company Data</a></li>
                <li><a href="#legal_document" class="tab-link">Legal Document</a></li>
            </ul>
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($company_profile) ? route('company_profile.update', $company_profile->id) : route('company_profile.store') }}">

                    @csrf
                    @if (isset($company_profile))
                        @method('PUT')
                    @endif

                    <div class=" tab-content grid grid-cols-1 md:grid-cols-2 gap-6" id="company_data">


                        <!-- Nama jabatan -->
                        <div class="mb-4">
                            <label for="nama_perusahaan" class="block text-gray-700 font-medium mb-1">Nama
                                Perusahaan</label>
                            <input type="text" id="name" name="nama_perusahaan" required
                                value="{{ old('nama_perusahaan', $company_profile->nama_perusahaan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nama_perusahaan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- deskripsi jabatan -->
                        <div class="mb-4 md:col-span-2">
                            <label for="jalan" class="block text-gray-700 font-medium mb-1">Jalan</label>
                            <textarea id="jalan" name="jalan" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('jalan', $company_profile->jalan ?? '') }}</textarea>
                            @error('jalan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kelurahan" class="block text-gray-700 font-medium mb-1">kelurahan</label>
                            <input type="text" id="name" name="kelurahan" required
                                value="{{ old('kelurahan', $company_profile->kelurahan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kelurahan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kecamatan" class="block text-gray-700 font-medium mb-1">Kecamatan</label>
                            <input type="text" id="name" name="kecamatan" required
                                value="{{ old('kecamatan', $company_profile->kecamatan ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kecamatan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kota" class="block text-gray-700 font-medium mb-1">Kota</label>
                            <input type="text" id="name" name="kota" required
                                value="{{ old('kota', $company_profile->kota ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kota')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="provinsi" class="block text-gray-700 font-medium mb-1">Provinsi</label>
                            <input type="text" id="name" name="provinsi" required
                                value="{{ old('provinsi', $company_profile->provinsi ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('provinsi')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kode_pos" class="block text-gray-700 font-medium mb-1">Kode Pos</label>
                            <input type="number" id="name" name="kode_pos" required
                                value="{{ old('kode_pos', $company_profile->kode_pos ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('kode_pos')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="phone_number" class="block text-gray-700 font-medium mb-1">Nomor
                                Handphone</label>
                            <input type="number" id="name" name="phone_number" required
                                value="{{ old('phone_number', $company_profile->phone_number ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                            <input type="email" id="name" name="email" required
                                value="{{ old('email', $company_profile->email ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="font-weight-bold">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo">

                            <!-- error message untuk title -->
                            @error('logo')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>



                    </div>


                    <div class="tab-content hidden grid-cols-1 md:grid-cols-2 gap-6" id="legal_document">


                        <!-- Nama jabatan -->
                        <div class="mb-4">
                            <label for="akte_pendirian" class="block text-gray-700 font-medium mb-1">Data Pendirian
                            </label>
                            <input type="file" id="name" name="akte_pendirian" accept="application/pdf" required
                                value="{{ old('akte_pendirian', $company_profile->akte_pendirian ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('akte_pendirian')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="akte_perubahan_terakhir" class="block text-gray-700 font-medium mb-1">Akte
                                Perubahan Terakhir

                            </label>
                            <input type="file" id="name" name="akte_perubahan_terakhir" accept="application/pdf"
                                required
                                value="{{ old('akte_perubahan_terakhir', $company_profile->akte_perubahan_terakhir ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('akte_perubahan_terakhir')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nib" class="block text-gray-700 font-medium mb-1">Nib


                            </label>
                            <input type="file" id="name" name="nib" accept="application/pdf" required
                                value="{{ old('nib', $company_profile->nib ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nib')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($company_profile) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('company_profile.index') }}"
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
    {{-- Script Tab Navigation --}}
    <script>
        const links = document.querySelectorAll('.tab-link');
        const tabs = document.querySelectorAll('.tab-content');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(tab => tab.classList.add('hidden'));
                const id = this.getAttribute('href');
                document.querySelector(id).classList.remove('hidden');
            });
        });
    </script>
@endsection

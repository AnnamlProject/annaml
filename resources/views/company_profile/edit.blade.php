@extends('layouts.app')


@section('content')
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                    <li><a href="#company_data" class="tab-link">Company Data</a></li>
                    <li><a href="#legal_document" class="tab-link">Legal Document</a></li>
                </ul>
                <form action="{{ route('company_profile.update', $informasiPerusahaan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="tab-content grid grid-cols-1 md:grid-cols-2 gap-6" id="company_data">
                        <div>
                            <label for="nama_perusahaan" class="block font-medium">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                                value="{{ old('nama_perusahaan', $informasiPerusahaan->nama_perusahaan) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('nama_perusahaan') border-red-500 @enderror">
                            @error('nama_perusahaan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="desc_jabatan" class="block font-medium">Jalan</label>
                            <input type="text" name="jalan" id="jalan"
                                value="{{ old('jalan', $informasiPerusahaan->jalan) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('jalan') border-red-500 @enderror"
                                required>
                            @error('jalan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kelurahan" class="block font-medium">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan"
                                value="{{ old('kelurahan', $informasiPerusahaan->kelurahan) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('kelurahan') border-red-500 @enderror"
                                required>
                            @error('kelurahan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kecamatan" class="block font-medium">kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan"
                                value="{{ old('kecamatan', $informasiPerusahaan->kecamatan) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('kecamatan') border-red-500 @enderror"
                                required>
                            @error('kecamatan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kota" class="block font-medium">kota</label>
                            <input type="text" name="kota" id="kota"
                                value="{{ old('kota', $informasiPerusahaan->kota) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('kota') border-red-500 @enderror"
                                required>
                            @error('kota')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="provinsi" class="block font-medium">provinsi</label>
                            <input type="text" name="provinsi" id="provinsi"
                                value="{{ old('provinsi', $informasiPerusahaan->provinsi) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('provinsi') border-red-500 @enderror"
                                required>
                            @error('provinsi')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kode_pos" class="block font-medium">kode_pos</label>
                            <input type="text" name="kode_pos" id="kode_pos"
                                value="{{ old('kode_pos', $informasiPerusahaan->kode_pos) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('kode_pos') border-red-500 @enderror"
                                required>
                            @error('kode_pos')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone_number" class="block font-medium">phone_number</label>
                            <input type="number" name="phone_number" id="phone_number"
                                value="{{ old('phone_number', $informasiPerusahaan->phone_number) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('phone_number') border-red-500 @enderror"
                                required>
                            @error('phone_number')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block font-medium">email</label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $informasiPerusahaan->email) }}"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm 
                                          focus:border-indigo-500 focus:ring-indigo-500 
                                          @error('email') border-red-500 @enderror"
                                required>
                            @error('email')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="tab-content hidden grid-cols-1 md:grid-cols-2 gap-6" id="legal_document">
                        @php
                            $documents = $informasiPerusahaan->legalDocuments->keyBy('jenis_dokumen');
                        @endphp
                        <div class="mb-3">
                            <label class="block font-medium">Akte Pendirian</label>
                            @if ($documents->has('Akte Pendirian'))
                                <p class="text-sm mb-1">
                                    📄 <a href="{{ asset('storage/' . $documents['Akte Pendirian']->file_path) }}"
                                        target="_blank" class="text-blue-600 underline">Lihat</a>
                                    | <a href="{{ asset('storage/' . $documents['Akte Pendirian']->file_path) }}" download
                                        class="text-green-600 underline">Download</a>
                                </p>
                            @endif
                            <input type="file" name="akte_pendirian" accept="application/pdf"
                                class="w-full border px-3 py-2 rounded">
                        </div>


                        <div class="mb-3">
                            <label class="block font-medium">Akte Perubahan Terakhir</label>
                            @if ($documents->has('Akta Perubahan Terakhir'))
                                <p class="text-sm mb-1">
                                    📄 <a
                                        href="{{ asset('storage/' . $documents['Akta Perubahan Terakhir']->file_path) }}"
                                        target="_blank" class="text-blue-600 underline">Lihat</a>
                                    | <a href="{{ asset('storage/' . $documents['Akta Perubahan Terakhir']->file_path) }}"
                                        download class="text-green-600 underline">Download</a>
                                </p>
                            @endif
                            <input type="file" name="akta_perubahan_terakhir" accept="application/pdf"
                                class="w-full border px-3 py-2 rounded">
                        </div>
                        <div class="mb-3">
                            <label class="block font-medium">NIB</label>
                            @if ($documents->has('NIB'))
                                <p class="text-sm mb-1">
                                    📄 <a href="{{ asset('storage/' . $documents['NIB']->file_path) }}" target="_blank"
                                        class="text-blue-600 underline">Lihat</a>
                                    | <a href="{{ asset('storage/' . $documents['NIB']->file_path) }}" download
                                        class="text-green-600 underline">Download</a>
                                </p>
                            @endif
                            <input type="file" name="nib" accept="application/pdf"
                                class="w-full border px-3 py-2 rounded">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('company_profile.show', $informasiPerusahaan->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

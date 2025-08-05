@extends('layouts.app')


@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('taxpayers_company.update', $taxpayers->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_perusahaan" class="block font-medium">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                                value="{{ old('nama_perusahaan', $taxpayers->nama_perusahaan) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500
                                @error('nama_perusahaan') border-red-500 @enderror">
                            @error('nama_perusahaan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="desc_jabatan" class="block font-medium">Jalan</label>
                            <input type="text" name="jalan" id="jalan"
                                value="{{ old('jalan', $taxpayers->jalan) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('jalan') border-red-500 @enderror"
                                required>
                            @error('jalan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kelurahan" class="block font-medium">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan"
                                value="{{ old('kelurahan', $taxpayers->kelurahan) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('kelurahan') border-red-500 @enderror"
                                required>
                            @error('kelurahan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kecamatan" class="block font-medium">kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan"
                                value="{{ old('kecamatan', $taxpayers->kecamatan) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('kecamatan') border-red-500 @enderror"
                                required>
                            @error('kecamatan')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kota" class="block font-medium">kota</label>
                            <input type="text" name="kota" id="kota" value="{{ old('kota', $taxpayers->kota) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('kota') border-red-500 @enderror"
                                required>
                            @error('kota')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="provinsi" class="block font-medium">provinsi</label>
                            <input type="text" name="provinsi" id="provinsi"
                                value="{{ old('provinsi', $taxpayers->provinsi) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('provinsi') border-red-500 @enderror"
                                required>
                            @error('provinsi')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kode_pos" class="block font-medium">kode_pos</label>
                            <input type="text" name="kode_pos" id="kode_pos"
                                value="{{ old('kode_pos', $taxpayers->kode_pos) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('kode_pos') border-red-500 @enderror"
                                required>
                            @error('kode_pos')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone_number" class="block font-medium">phone_number</label>
                            <input type="number" name="phone_number" id="phone_number"
                                value="{{ old('phone_number', $taxpayers->phone_number) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500
                                @error('phone_number') border-red-500 @enderror" required>
                            @error('phone_number')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block font-medium">email</label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $taxpayers->email) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                                required>
                            @error('email')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="bentuk_badan_hukum" class="block font-medium">bentuk_badan_hukum</label>
                            <input type="text" name="bentuk_badan_hukum" id="bentuk_badan_hukum"
                                value="{{ old('bentuk_badan_hukum', $taxpayers->bentuk_badan_hukum) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500
                                @error('bentuk_badan_hukum') border-red-500 @enderror" required>
                            @error('bentuk_badan_hukum')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="npwp" class="block font-medium">npwp</label>
                            <input type="text" name="npwp" id="npwp" value="{{ old('npwp', $taxpayers->npwp) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('npwp') border-red-500 @enderror"
                                required>
                            @error('npwp')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="klu_code" class="block font-medium">klu_code</label>
                            <input type="text" name="klu_code" id="klu_code"
                                value="{{ old('klu_code', $taxpayers->klu_code) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500 @error('klu_code') border-red-500 @enderror"
                                required>
                            @error('klu_code')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="klu_description" class="block font-medium">klu_description</label>
                            <input type="text" name="klu_description" id="klu_description"
                                value="{{ old('klu_description', $taxpayers->klu_description) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500
                                @error('klu_description') border-red-500 @enderror" required>
                            @error('klu_description')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tax_office" class="block font-medium">tax_office</label>
                            <input type="text" name="tax_office" id="tax_office"
                                value="{{ old('tax_office', $taxpayers->tax_office) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                focus:border-indigo-500 focus:ring-indigo-500
                                @error('tax_office') border-red-500 @enderror" required>
                            @error('tax_office')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('taxpayers_company.show', $taxpayers->id) }}"
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
@endsection

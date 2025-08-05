@extends('layouts.app')


@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('unit_kerja.update', $unit_kerja->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        <div>
                            <label for="nama_klasifikasi" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" name="nama_unit" id="nama_klasifikasi"
                                value="{{ old('nama_unit', $unit_kerja->nama_unit) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nama_level')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $unit_kerja->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('unit_kerja.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('jenis_hari.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama" class="block font-medium">Nama Lokasi</label>
                            <input type="text" name="nama" id="nama" value="{{ old('nama', $data->nama) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none
                                focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror"
                                required>
                            @error('nama')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="deskripsi" class="block font-medium">Deskripsi</label>
                            <input type="text" name="deskripsi" id="deskripsi"
                                value="{{ old('deskripsi', $data->deskripsi) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                          @error('deskripsi') border-red-500 @enderror">
                            @error('deskripsi')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('jenis_hari.index') }}"
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

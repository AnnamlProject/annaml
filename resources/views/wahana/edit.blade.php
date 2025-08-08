@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('wahana.update', $wahana->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="kode_wahana" class="block font-medium">Kode wahana</label>
                            <input type="text" name="kode_wahana" id="kode_wahana"
                                value="{{ old('kd_wahana', $wahana->kode_wahana) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                          @error('kode_wahana') border-red-500 @enderror">
                            @error('kode_wahana')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nama_wahana" class="block font-medium">Nama wahana</label>
                            <input type="text" name="nama_wahana" id="nama_wahana"
                                value="{{ old('nama_wahana', $wahana->nama_wahana) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                          @error('nama_wahana') border-red-500 @enderror"
                                required>
                            @error('nama_wahana')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label for="unit_kerja_id" class="block text-sm font-medium text-gray-700 mb-1">Golongan
                                PTKP</label>
                            <select name="unit_kerja_id" id="unit_kerja_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Golongan --</option>
                                @foreach ($unit as $g)
                                    <option value="{{ $g->id }}"
                                        {{ isset($wahana) && $wahana->unit_kerja_id == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="kategori" class="block font-medium">Contact Person</label>
                            <input type="text" name="kategori" id="kategori"
                                value="{{ old('kategori', $wahana->kategori) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                          @error('kategori') border-red-500 @enderror"
                                required>
                            @error('kategori')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kapasitas" class="block font-medium">kapasitas wahana</label>
                            <input type="text" name="kapasitas" id="kapasitas"
                                value="{{ old('kapasitas', $wahana->kapasitas) }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 
                                          @error('kapasitas') border-red-500 @enderror"
                                required>
                            @error('kapasitas')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Tipe -->
                        <div class="mb-4">
                            <label for="status" class="block text-gray-700 font-medium mb-1">status</label>
                            <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Aktif" {{ old('status', $wahana->status) == 'Aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="Non Aktif"
                                    {{ old('status', $wahana->status) == 'Non Aktif' ? 'selected' : '' }}>
                                    Non Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('wahana.index') }}"
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

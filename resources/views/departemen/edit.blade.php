@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('departemen.update', $departemen->id) }}" method="POST">
                    @csrf
                    @method('PUT')



                    <!-- Form Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode</label>
                            <input type="text" name="kode"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('kode', $departemen->kode) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <input type="text" name="deskripsi"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('deskripsi', $departemen->deskripsi) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ $departemen->status == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $departemen->status == '0' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>


                    <!-- Tombol -->
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('departemen.index') }}"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                            Kembali
                        </a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

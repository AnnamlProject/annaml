@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($jenis_hari) ? route('jenis_hari.update', $jenis_hari->id) : route('jenis_hari.store') }}">
                    @csrf
                    @if (isset($jenis_hari))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        <!-- Nama -->
                        <div class="mb-4">
                            <label for="nama" class="block text-gray-700 font-medium mb-1">Nama
                            </label>
                            <input type="text" id="name" name="nama" required
                                value="{{ old('nama', $jenis_hari->nama ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('nama')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                    <!-- deskripsi jenis_hari_asset -->
                    <div class="mb-4 md:col-span-2">
                        <label for="deskripsi" class="block text-gray-700 font-medium mb-1">Deksripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi', $jenis_hari->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="mb-2">
                            <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai
                            </label>
                            <input type="time" name="jam_mulai"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div class="mb-2">
                            <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai
                            </label>
                            <input type="time" name="jam_selesai"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>



                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($jenis_hari) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('jenis_hari.index') }}"
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

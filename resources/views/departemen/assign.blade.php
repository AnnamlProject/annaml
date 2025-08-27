@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 text-green-600 font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Form pilih departemen --}}
                <form method="GET" action="{{ route('departemen.assign') }}">
                    <label class="block mb-2 font-medium">Pilih Departemen</label>
                    <select name="departemen_id" class="w-full border rounded p-2 mb-4" onchange="this.form.submit()">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departemenList as $dep)
                            <option value="{{ $dep->id }}"
                                {{ isset($departemenId) && $departemenId == $dep->id ? 'selected' : '' }}>
                                {{ $dep->deskripsi }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if ($departemenId)
                    <form method="POST" action="{{ route('departemen.assign.store') }}">
                        @csrf
                        <input type="hidden" name="departemen_id" value="{{ $departemenId }}">

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Akun yang Belum Diassign --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold">Akun Non-Department</h3>
                                    <div class="space-x-2">
                                        <button type="button" onclick="selectAll()"
                                            class="text-sm text-blue-600 hover:underline">Select All</button>
                                        <button type="button" onclick="deselectAll()"
                                            class="text-sm text-red-600 hover:underline">Remove All</button>
                                    </div>
                                </div>
                                <ul class="border p-2 rounded h-64 overflow-y-auto" id="checkbox-container">
                                    @forelse ($nonDepartemenAccounts as $akun)
                                        <li>
                                            <label>
                                                <input type="checkbox" name="akun_ids[]" value="{{ $akun->id }}">
                                                {{ $akun->kode_akun }}
                                                {{ $akun->nama_akun }}
                                            </label>
                                        </li>
                                    @empty
                                        <li class="text-gray-400">Semua akun sudah didepartemenkan ke departemen ini</li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- Akun yang Sudah Diassign --}}
                            {{-- Akun yang Sudah Diassign --}}
                            <div>
                                <h3 class="font-semibold mb-2">Akun Department</h3>
                                <ul class="border p-2 rounded h-64 overflow-y-auto">
                                    @forelse ($departemenAccounts as $entry)
                                        <li class="flex justify-between items-center">
                                            <div>
                                                {{ $entry->chartOfAccount->kode_akun ?? '-' }}
                                                {{ $entry->chartOfAccount->nama_akun ?? '-' }}
                                                - <span class="text-sm text-gray-500">
                                                    {{ $entry->departemen->deskripsi ?? '-' }}
                                                </span>
                                            </div>

                                            {{-- Tombol Hapus --}}
                                            <form method="POST"
                                                action="{{ route('departemen.assign.destroy', $entry->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 text-sm hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        </li>
                                    @empty
                                        <li class="text-gray-400">Belum ada akun ditempatkan ke departemen ini</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <a href="{{ route('departemen.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md">
                            Batal
                        </a>
                        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Assign
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Script Select All / Remove All --}}
    <script>
        function selectAll() {
            document.querySelectorAll('#checkbox-container input[type="checkbox"]').forEach(cb => cb.checked = true);
        }

        function deselectAll() {
            document.querySelectorAll('#checkbox-container input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
    </script>
@endsection

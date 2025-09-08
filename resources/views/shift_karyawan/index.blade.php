@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                <!-- Sticky Card Header -->
                @php
                    $themeColor = \App\Setting::get('theme_color', '#4F46E5');
                @endphp

                <div class="sticky top-0 z-20 px-6 py-5 border-b border-gray-100 flex justify-between items-center"
                    style="background: {{ $themeColor }};">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3 text-white text-xl"></i>
                        Scheduling Employee
                    </h3>
                    <div class="flex flex-wrap gap-2">

                        <button onclick="document.getElementById('filterPanel').classList.toggle('hidden')"
                            class="inline-flex items-center px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-filter text-gray-500 mr-2"></i> Filter
                        </button>
                        <a href="{{ route('shift_karyawan.create') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition-all">
                            <i class="fas fa-plus mr-2"></i> Add Scheduling Employee
                        </a>
                    </div>
                </div>
            </div>
            <div id="filterPanel"
                class="{{ request('search') || request('filter_tipe') ? '' : 'hidden' }} px-6 py-4 border-b border-gray-100 bg-gray-50">
                <form method="GET" action="{{ route('shift_karyawan.index') }}">
                    <div class="flex flex-wrap gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari unit,wahana, dan jenis hari"
                                class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm w-64 shadow-sm" />
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Filter Tipe Akun -->
                        <select name="filter_tipe" class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                            <option value="">Semua Unit Kerja</option>
                            @foreach ($unitkerja as $tipe)
                                <option value="{{ $tipe }}" {{ request('filter_tipe') == $tipe ? 'selected' : '' }}>
                                    {{ $tipe }}
                                </option>
                            @endforeach
                        </select>
                        <select name="filter_wahana" class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                            <option value="">Semua wahana </option>
                            @foreach ($wahana as $whn)
                                <option value="{{ $whn }}" {{ request('filter_tipe') == $whn ? 'selected' : '' }}>
                                    {{ $whn }}
                                </option>
                            @endforeach
                        </select>
                        <select name="filter_jenis_hari"
                            class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                            <option value="">Semua jenis hari </option>
                            @foreach ($jenis_hari as $js)
                                <option value="{{ $js }}" {{ request('filter_tipe') == $js ? 'selected' : '' }}>
                                    {{ $js }}
                                </option>
                            @endforeach
                        </select>

                        <select name="filter_status" class="py-2 px-3 border rounded-lg text-sm border-gray-300 shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="Penetapan" {{ request('filter_status') == 'Penetapan' ? 'selected' : '' }}>
                                Penetapan
                            </option>
                            <option value="Perubahan" {{ request('filter_status') == 'Perubahan' ? 'selected' : '' }}>
                                Perubahan
                            </option>
                            <option value="Tambahan" {{ request('filter_status') == 'Tambahan' ? 'selected' : '' }}>
                                Tambahan
                            </option>
                        </select>


                        <!-- Tombol Filter -->
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded shadow-sm hover:bg-blue-600 text-sm">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>

                        <!-- Tombol Reset -->
                        <a href="{{ route('shift_karyawan.index') }}"
                            class="inline-flex items-center px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-1 text-gray-400"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="relative overflow-x-auto" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">#
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Karyawan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Kerja</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Level Karyawan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Wahana</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Hari</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Mulai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lama Jam</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Persentase Jam</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($data as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-2  text-center text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->karyawan->nama_karyawan }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->unitKerja->nama_unit }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->karyawan->levelKaryawan->nama_level }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->wahana->nama_wahana }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->tanggal ?? '-' }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->jenisHari->nama }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->jam_mulai ?? '-' }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->jam_selesai }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->lama_jam }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->persentase_jam ?? 'Tidak Ada' }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->status ?? 'Tidak Ada' }}</td>
                                <td class="px-4 py-2 text-center ">{{ $item->keterangan ?? 'Tidak Ada' }}</td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('shift_karyawan.show', $item->id) }}"
                                            class="text-blue-500 hover:text-blue-700 p-2 rounded-full hover:bg-blue-50 transition-colors"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('shift_karyawan.edit', $item->id) }}"
                                            class="text-yellow-500 hover:text-yellow-700 p-2 rounded-full hover:bg-yellow-50 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('shift_karyawan.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button" onclick="confirmDelete({{ $item->id }})"
                                            class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">Belum ada Scheduling Employee</p>
                                        <a href="{{ route('shift_karyawan.create') }}"
                                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                            <i class="fas fa-plus mr-2"></i> Tambah
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    </div>

    <style>
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.9);
        }

        table thead {
            position: sticky;
            top: 68px;
            z-index: 10;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        tr {
            transition: background-color 0.2s ease;
        }
    </style>
@endsection

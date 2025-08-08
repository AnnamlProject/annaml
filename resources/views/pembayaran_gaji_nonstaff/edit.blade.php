@extends('layouts.app')
@section('content')

    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" action="{{ route('pembayaran_gaji_nonstaff.update', $pembayaran->id) }}">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" value="{{ $pembayaran->employee->nama_karyawan }}" disabled
                                class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100">
                            <input type="hidden" name="kode_karyawan" value="{{ $pembayaran->kode_karyawan }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_pembayaran" value="{{ $pembayaran->tanggal_pembayaran }}"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Awal</label>
                            <input type="date" name="periode_awal" value="{{ $pembayaran->periode_awal }}"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akhir</label>
                            <input type="date" name="periode_akhir" value="{{ $pembayaran->periode_akhir }}"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Detail Komponen Gaji</h3>
                        <table class="min-w-full table-auto border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr class="text-left">
                                    <th class="px-4 py-2 border">Nama Komponen</th>
                                    <th class="px-4 py-2 border">Nilai</th>
                                    <th class="px-4 py-2 border">Jumlah Hari</th>
                                    <th class="px-4 py-2 border">Potongan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembayaran->details as $index => $detail)
                                    <tr>
                                        <td class="px-4 py-2 border">
                                            {{ $detail->komponen->nama_komponen }}
                                            <input type="hidden" name="komponen[{{ $index }}][id]"
                                                value="{{ $detail->id }}">
                                            <input type="hidden" name="komponen[{{ $index }}][kode_komponen]"
                                                value="{{ $detail->kode_komponen }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" step="any"
                                                name="komponen[{{ $index }}][nilai]"
                                                class="w-full border rounded p-1" value="{{ $detail->nilai }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" name="komponen[{{ $index }}][jumlah_hari]"
                                                class="w-full border rounded p-1" value="{{ $detail->jumlah_hari }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" step="any"
                                                name="komponen[{{ $index }}][potongan]"
                                                class="w-full border rounded p-1" value="{{ $detail->potongan }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            Update
                        </button>
                        <a href="{{ route('pembayaran_gaji_nonstaff.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

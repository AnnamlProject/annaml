@extends('layouts.app')


@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Informasi Transaksi</h2>
            </div>
            <div class="px-6 py-4">
                <table class="w-full text-sm text-left text-gray-700">
                    <tbody>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Wahana</th>
                            <td class="py-2">{{ $transaksi_wahana->wahana->nama_wahana }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Unit Kerja</th>
                            <td class="py-2">{{ $transaksi_wahana->unitKerja->nama_unit }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Tanggal</th>
                            <td class="py-2">{{ $transaksi_wahana->tanggal }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Pendapatan</th>
                            <td class="py-2">{{ number_format($transaksi_wahana->realisasi) }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Jumlah Pengunjung</th>
                            <td class="py-2">{{ $transaksi_wahana->jumlah_pengunjung }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                <a href="{{ route('transaksi_wahana.edit', $transaksi_wahana->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('transaksi_wahana.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
@endsection

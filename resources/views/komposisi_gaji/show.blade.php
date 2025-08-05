@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                    <input type="text" value="{{ $komposisi->employee->nama_karyawan }}" readonly
                        class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Komponen</th>
                                <th class="px-4 py-2 border">Nilai</th>
                                <th class="px-4 py-2 border">Jumlah Hari</th>
                                <th class="px-4 py-2 border">Potongan</th>
                                <th class="px-4 py-2 border">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $index => $detail)
                                <tr>
                                    <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $detail->komponen->nama_komponen }}</td>
                                    <td class="px-4 py-2 border text-right">{{ number_format($detail->nilai) }}</td>
                                    <td class="px-4 py-2 border text-center">{{ $detail->jumlah_hari }}</td>
                                    <td class="px-4 py-2 border text-right">{{ number_format($detail->potongan) }}</td>
                                    <td class="px-4 py-2 border text-right">
                                        {{ number_format($detail->nilai * $detail->jumlah_hari + $detail->potongan * $detail->jumlah_hari) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <a href="{{ route('komposisi_gaji.index') }}" class="text-blue-600 hover:underline">← Kembali ke
                        Daftar</a>
                </div>

            </div>
        </div>
    </div>
@endsection

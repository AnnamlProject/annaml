@extends('layouts.app')


@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Informasi Target unit</h2>
            </div>
            <div class="px-6 py-4">
                <table class="w-full text-sm text-left text-gray-700">
                    <tbody>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Unit Kerja</th>
                            <td class="py-2">{{ $target_unit->unit->nama_unit }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Level Karyawan</th>
                            <td class="py-2">{{ $target_unit->levelKaryawan->nama_level }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Komponen</th>
                            <td class="py-2">{{ $target_unit->levelKaryawan->nama_level }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Target Harian </th>
                            <td class="py-2">{{ number_format($target_unit->target_bulanan) }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Bulan</th>
                            <td class="py-2">{{ $target_unit->bulan }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Tahun</th>
                            <td class="py-2">{{ $target_unit->tahun }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Besaran Nominal</th>
                            <td class="py-2">{{ number_format($target_unit->besaran_nominal) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                <a href="{{ route('target_unit.edit', $target_unit->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('target_unit.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
@endsection

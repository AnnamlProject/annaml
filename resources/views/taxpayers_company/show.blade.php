@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl font-semibold text-gray-800">Taxpayers</h2>
            </div>
            <div class="px-6 py-4">
                <table class="w-full text-sm text-left text-gray-700">
                    <tbody>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Nama Perusahaan</th>
                            <td class="py-2">{{ $taxpayers->nama_perusahaan }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Jalan</th>
                            <td class="py-2">{{ $taxpayers->nama_jabatan }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Kelurahan</th>
                            <td class="py-2">{{ $taxpayers->kelurahan }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Kecamatan</th>
                            <td class="py-2">{{ $taxpayers->kecamatan }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">Kota</th>
                            <td class="py-2">{{ $taxpayers->kota }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Provinsi</th>
                            <td class="py-2">{{ $taxpayers->provinsi }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Kode Pos</th>
                            <td class="py-2">{{ $taxpayers->kode_pos }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">phone_number</th>
                            <td class="py-2">{{ $taxpayers->phone_number }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">email</th>
                            <td class="py-2">{{ $taxpayers->email }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Bentuk Badan Hukum</th>
                            <td class="py-2">{{ $taxpayers->bentuk_badan_hukum }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">NPWP</th>
                            <td class="py-2">{{ $taxpayers->npwp }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 font-medium">klu code</th>
                            <td class="py-2">{{ $taxpayers->klu_code }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">klu deskripsi</th>
                            <td class="py-2">{{ $taxpayers->klu_description }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">tax office</th>
                            <td class="py-2">{{ $taxpayers->tax_office }}</td>
                        </tr>
                        <tr class="border-t">
                            <th class="py-2 w-1/3 font-medium">Logo</th>
                            <td class="py-2"> <img src="{{ asset('storage/informasi_perusahaan/' . $taxpayers->logo) }}"
                                    class="w-100 rounded"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                <a href="{{ route('taxpayers_company.edit', $taxpayers->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('komposisi_gaji.update', $komposisi->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <select name="kode_karyawan" class="w-full rounded-md border-gray-300 shadow-sm" disabled>
                            @foreach ($karyawan as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ $emp->id == $komposisi->kode_karyawan ? 'selected' : '' }}>
                                    {{ $emp->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 table-auto">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Nama Komponen</th>
                                    <th class="px-4 py-2 border">Nilai</th>
                                    <th class="px-4 py-2 border">Jumlah Hari</th>
                                    <th class="px-4 py-2 border">Potongan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $index => $detail)
                                    <tr>
                                        <td class="px-4 py-2 border">
                                            {{ $detail->komponen->nama_komponen }}
                                            <input type="hidden" name="komponen[{{ $index }}][id_detail]"
                                                value="{{ $detail->id }}">
                                            <input type="hidden" name="komponen[{{ $index }}][kode_komponen]"
                                                value="{{ $detail->kode_komponen }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" name="komponen[{{ $index }}][nilai]"
                                                class="w-full border p-1 rounded" value="{{ $detail->nilai }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" name="komponen[{{ $index }}][jumlah_hari]"
                                                class="w-full border p-1 rounded" value="{{ $detail->jumlah_hari }}">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" name="komponen[{{ $index }}][potongan]"
                                                class="w-full border p-1 rounded" value="{{ $detail->potongan }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($komponenBaru->count())
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-semibold mb-4">Tambah Komponen Penghasilan</h3>
                            <table class="min-w-full border border-gray-200 table-auto">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">Nama Komponen</th>
                                        <th class="px-4 py-2 border">Nilai</th>
                                        <th class="px-4 py-2 border">Jumlah Hari</th>
                                        <th class="px-4 py-2 border">Potongan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($komponenBaru as $i => $k)
                                        <tr>
                                            <td class="px-4 py-2 border">
                                                {{ $k->nama_komponen }}
                                                <input type="hidden" name="baru[{{ $i }}][kode_komponen]"
                                                    value="{{ $k->id }}">
                                            </td>
                                            <td class="px-4 py-2 border">
                                                <input type="number" name="baru[{{ $i }}][nilai]"
                                                    class="w-full border p-1 rounded">
                                            </td>
                                            <td class="px-4 py-2 border">
                                                <input type="number" name="baru[{{ $i }}][jumlah_hari]"
                                                    class="w-full border p-1 rounded">
                                            </td>
                                            <td class="px-4 py-2 border">
                                                <input type="number" name="baru[{{ $i }}][potongan]"
                                                    class="w-full border p-1 rounded">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif


                    <div class="mt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Update
                        </button>
                        <a href="{{ route('komposisi_gaji.index') }}" class="ml-2 text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

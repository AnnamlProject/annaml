@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($wahana) ? route('wahana.update', $wahana->id) : route('wahana.store') }}">
                    @csrf
                    @if (isset($wahana))
                        @method('PUT')
                    @endif

                    <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <table style="width: 100%; border-collapse: collapse;" id="tabel-wahana">
                            <thead>
                                <tr style="background-color: #f5f5f5; font-weight: bold;">
                                    <th style="padding: 12px; border: 1px solid #ddd; width: 50px;">No.</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Nama Wahana</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Unit Kerja</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Kategori</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Status</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Kapasitas</th>
                                    <th style="padding: 12px; border: 1px solid #ddd;">Urutan</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; width: 70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-wahana">
                                <tr>
                                    <td style="padding: 12px; border: 1px solid #ddd;">1</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <input type="hidden" name="kode_wahana[]">
                                        <input type="text" name="nama_wahana[]"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <select name="unit_kerja_id[]" id="unit_kerja_id"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Golongan --</option>
                                            @foreach ($unit_kerja as $g)
                                                <option value="{{ $g->id }}"
                                                    {{ isset($wahana) && $wahana->unit_kerja_id == $g->id ? 'selected' : '' }}>
                                                    {{ $g->nama_unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <input type="text" name="kategori[]"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <select
                                            name="status[]"class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="Aktif">Aktif</option>
                                            <option value="Non Aktif">Non Aktif</option>
                                        </select>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <input type="number" name="kapasitas[]"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        <input type="number" name="urutan[]"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Masukkan urutan prioritas tampilan">
                                    </td>
                                    <td style="text-align: center; border: 1px solid #ddd;">
                                        <button type="button" onclick="hapusBaris(this)"
                                            style="color: red; border: none; background: none; font-size: 18px;">🗑️</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Tombol Tambah Baris -->
                        <div style="margin: 20px;">
                            <button type="button" onclick="tambahBaris()" class="btn elevation-2"
                                style="background-color: green; color: white; padding: 10px 16px; border: none; border-radius: 6px; font-size: 14px;">+
                                Tambah Baris</button>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($wahana) ? 'Update' : 'Create' }} wahana
                        </button>
                        <a href="{{ route('wahana.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function tambahBaris() {
            let tbody = document.getElementById('tbody-wahana');
            let rowCount = tbody.rows.length;
            let row = tbody.insertRow();

            row.innerHTML = `
        <td style="padding: 12px; border: 1px solid #ddd;"></td>
        <td style="padding: 12px; border: 1px solid #ddd;">
            <input type="hidden" name="kode_wahana[]">
            <input type="text" name="nama_wahana[]" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td style="padding: 12px; border: 1px solid #ddd;">
           <select name="unit_kerja_id[]" id="unit_kerja_id"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Golongan --</option>
                                            @foreach ($unit_kerja as $g)
                                                <option value="{{ $g->id }}"
                                                    {{ isset($wahana) && $wahana->unit_kerja_id == $g->id ? 'selected' : '' }}>
                                                    {{ $g->nama_unit }}
                                                </option>
                                            @endforeach
                                        </select>
        </td>
        <td style="padding: 12px; border: 1px solid #ddd;">
            <input type="text" name="kategori[]" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td style="padding: 12px; border: 1px solid #ddd;">
            <select name="status[]" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Aktif">Aktif</option>
                <option value="Non Aktif">Non Aktif</option>
            </select>
        </td>
        <td style="padding: 12px; border: 1px solid #ddd;">
            <input type="number" name="kapasitas[]" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td style="padding: 12px; border: 1px solid #ddd;">
            <input type="number" name="urutan[]" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td style="text-align: center; border: 1px solid #ddd;">
            <button type="button" onclick="hapusBaris(this)" style="color: red; border: none; background: none;">🗑️</button>
        </td>
    `;

            perbaruiNomor();
        }

        function hapusBaris(button) {
            let row = button.closest('tr');
            let tbody = row.parentNode;
            tbody.removeChild(row);
            perbaruiNomor();
        }

        function perbaruiNomor() {
            let tbody = document.getElementById('tbody-wahana');
            let rows = tbody.querySelectorAll('tr');
            rows.forEach((tr, index) => {
                tr.cells[0].innerText = index + 1;
            });
        }
    </script>
    <script>
        function formatRibuan(input) {
            let value = input.value;
            // Hilangkan semua karakter selain angka
            value = value.replace(/[^\d]/g, "");
            // Format angka jadi ribuan
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            input.value = value;
        }
    </script>
@endsection

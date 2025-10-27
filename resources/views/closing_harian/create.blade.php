@extends('layouts.app')

@section('content')

    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @php
                $themeColor = \App\Setting::get('theme_color', '#4F46E5');
            @endphp
            <div class="bg-white shadow-lg rounded-xl p-6 border-t-4" style="border-color:{{ $themeColor }}">
                <h4 class="font-semibold text-lg text-gray-800 mt-8 mb-4 border-l-4 border-blue-500 pl-2">
                    Closing Harian Create
                </h4>
                <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($data) ? route('komponen_penghasilan.update', $data->id) : route('komposisi_gaji.store') }}">

                    @csrf
                    @if (isset($data))
                        @method('PUT')
                    @endif


                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" class="w-full px-4 py-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
                            <select id="unit_kerja_id" name="unit_kerja_id"
                                class="w-full rounded-md bg-blue-200 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach ($unitKerja as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('unit_kerja_id', $data->unit_kerja_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tabel Komponen Penghasilan Berdasarkan Level -->
                    <div id="komponenContainer" class="mt-8 hidden">
                        <h3 class="text-lg font-semibold mb-4">Closing Harian</h3>
                        <div id="komponenTable"></div>
                    </div>


                    <!-- Buttons -->
                    <div class="mt-6 justify-end  flex space-x-4">
                        <a href="{{ route('komposisi_gaji.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition">
                            {{ isset($data) ? 'Update' : 'Process' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit_kerja_id');
        const komponenContainer = document.getElementById('komponenContainer');
        const komponenTable = document.getElementById('komponenTable');

        // Fungsi format angka Indonesia
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        // Fungsi konversi string format ID ke angka float
        function parseFormattedNumber(str) {
            return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
        }

        unitSelect.addEventListener('change', function() {
            let unitId = this.value;
            let unitName = unitSelect.options[unitSelect.selectedIndex].text;
            komponenTable.innerHTML = '';
            komponenContainer.classList.add('hidden');

            if (unitId) {
                fetch('/wahana-by-unit/' + unitId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let tableHTML = `
                              <table class="border-collapse border border-black text-center text-sm w-full">
                                        <thead>
                                            <tr>
                                            <th class="bg-yellow-200 border-black border-2 text-black p-2">Harga</th>
                                            <th class="bg-yellow-200 border-black border-2 text-black p-2"> 70.000 
                                            </th>
                                            <th class="bg-yellow-200 border-black border-2 text-black p-2">50.000</th>
                                            <th class="bg-yellow-200 border-black border-2 text-black p-2">100.000</th>
                                            <th class="bg-yellow-200 border-black border-2 text-black p-2">100.000</th>
                                            <th colspan="8" class="bg-green-600 text-white p-2"></th>
                                            </tr>
                                            <tr>
                                            <th rowspan="4" class="bg-green-600 text-black border-black border-2 p-2">Wahana</th>
                                            <th rowspan="4"  class="border-black border-2 bg-green-600 text-black p-1">4R</th>
                                            <th rowspan="4" class="border-black border-2 bg-green-600 text-black p-1">4R SD</th>
                                            <th rowspan="4"  class="border-black border-2 bg-green-600 text-black p-1">6R</th>
                                            <th rowspan="4"  class="border-black border-2 bg-green-600 text-black p-1">6R KEDUA</th>
                                            <th rowspan="2" class="bg-green-600 text-black border-black border-2 p-2">Total Omset</th>
                                            <th colspan="2" class="bg-green-600 text-black border-black border-2 p-2">Payment Type<br>(Type Pembayaran Diterima)</th>
                                            <th colspan="2" class="bg-green-600 text-black border-black border-2 p-2">PEMBAGIAN<br>(SHARING OMSET)</th>
                                            <th colspan="1" class="bg-green-600 text-black border-black border-2 p-2">TITIPAN OMSET<br>(Disetor Tunai)</th>
                                            <th rowspan="2" class="bg-green-600 text-black border-black border-2 p-2">Kekurangan<br>Cash untuk Setor Tunai ke Merchandise</th>
                                            </tr>
                                            <tr>
                                            <th class="border-black border-2 bg-green-600 text-black p-1">QRIS</th>
                                            <th class="border-black border-2 bg-green-600 text-black p-1">CASH</th>
                                            <th class="border-black border-2 bg-green-600 text-black p-1">Merchandise</th>
                                            <th class="border-black border-2 bg-green-600 text-black p-1">RCA</th>
                                            <th class="border-black border-2 bg-green-600 text-black p-1">${unitName}</th>
                                            </tr>
                                        </thead>
                                    <tbody>
                            `;

                            data.forEach((item, index) => {
                                tableHTML += `
                                    <tr class="border-2 border-black">
                                        <td class="px-2 py-1 border-black border-2">${item.nama_wahana}</td>
                                        <input type="hidden" name="komponen[${index}][kode_komponen]" value="${item.id}">
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                        <td class="px-2 py-1 border-black border-2"></td>
                                       
                                    </tr>
                                `;
                            });

                            tableHTML += `
                                            </tbody>
                                            <tfoot>
                                            <tr class="bg-green-600 border-black border-2 font-semibold">
                                                <td  class="px-4 py-2 border-black border-2 text-right">Total Keseluruhan:</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                                <td class="px-4 py-2 border-black border-2 text-right" id="grandTotal">0,00</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        `;
                            komponenTable.innerHTML = tableHTML;
                            komponenContainer.classList.remove('hidden');

                            // Fungsi perhitungan ulang total
                            function calculate(index) {
                                const nilai = parseFormattedNumber(document.querySelector(
                                    `input[name="komponen[${index}][nilai]"]`).value);
                                const jumlahHari = parseFloat(document.querySelector(
                                    `input[name="komponen[${index}][jumlah_hari]"]`).value) || 0;
                                const potongan = parseFormattedNumber(document.querySelector(
                                    `input[name="komponen[${index}][potongan]"]`).value);

                                const totalNilai = nilai * jumlahHari;
                                const totalPotongan = potongan * jumlahHari;
                                const totalKeseluruhan = totalNilai - totalPotongan;

                                document.querySelector(`input[name="komponen[${index}][total]"]`)
                                    .value = formatNumber(totalKeseluruhan);
                            }

                            function updateGrandTotal() {
                                let sum = 0;
                                komponenTable.querySelectorAll('.total').forEach(input => {
                                    sum += parseFormattedNumber(input.value);
                                });
                                document.getElementById('grandTotal').textContent = formatNumber(
                                    sum);
                            }


                            // Event handler untuk semua input
                            komponenTable.querySelectorAll('.nilai, .jumlah-hari, .potongan')
                                .forEach(input => {
                                    input.addEventListener('input', function() {
                                        const index = this.dataset.index;
                                        calculate(index);
                                        updateGrandTotal();
                                    });
                                });

                            // Format nilai dan potongan saat blur
                            komponenTable.querySelectorAll('.nilai, .potongan').forEach(input => {
                                input.addEventListener('blur', function() {
                                    const raw = parseFormattedNumber(this.value);
                                    this.value = formatNumber(raw);
                                });
                            });
                        }
                    });
            }
        });
    });
</script>

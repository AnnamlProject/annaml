@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div>
                    <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600">
                        <li>
                            {{-- <a
                                href="{{ route('buku_besar.export', [
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'selected_accounts' => request('selected_accounts'),
                                    'format' => 'excel',
                                ]) }}">
                                Export to Excel
                            </a> --}}
                        </li>
                        <li>
                            {{-- <a
                                href="{{ route('buku_besar.export', [
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'selected_accounts' => request('selected_accounts'),
                                    'format' => 'pdf',
                                ]) }}">
                                Export to PDF
                            </a> --}}
                        </li>
                        {{-- <li><a href="#pricing" class="tab-link">Print</a></li> --}}
                        <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')"
                                class="tab-link">Modify</a></li>
                        <li><a href="#linked" class="tab-link"></a></li>
                    </ul>
                </div>
                <div id="fileModify"
                    class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
                    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-7xl">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
                        </h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <form method="GET" action="{{ route('buku_besar.buku_besar_report') }}"
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="periode" class="block text-sm font-semibold text-gray-700 mb-1">Periode
                                        Buku</label>
                                    <select name="periode_buku" id="periode"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">---Pilih---</option>
                                        {{-- @foreach ($tahun_buku as $item)
                                                <option value="{{ $item->id }}" data-tahun="{{ trim($item->tahun) }}"
                                                    {{ request('periode_buku') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->tahun }}
                                                </option>
                                            @endforeach --}}
                                    </select>
                                </div>

                                {{-- Tanggal Awal --}}
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Awal</label>
                                    <input type="date" id="start_date" name="start_date"
                                        value="{{ request('start_date', \Carbon\Carbon::parse($tanggalAwal)->format('Y-m-d')) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                {{-- Tanggal Akhir --}}
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                        Akhir</label>
                                    <input type="date" id="end_date" name="end_date"
                                        value="{{ request('end_date', \Carbon\Carbon::parse($tanggalAkhir)->format('Y-m-d')) }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                {{-- Select Account --}}
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Account</label>
                                    <input type="hidden" name="selected_accounts" id="selected_accounts"
                                        value="{{ request('selected_accounts') }}">

                                    <input type="text" id="search-account" placeholder="Cari akun..."
                                        class="border p-2 rounded mb-3 w-full" />

                                    <div class="border rounded shadow-sm max-h-60 overflow-y-auto">
                                        <table class="min-w-full text-sm text-left text-gray-700" id="account-table">
                                            <thead class="bg-gray-100 sticky top-0">
                                                <tr>
                                                    <th class="px-2 py-1">
                                                        <input type="checkbox" id="select-all" class="form-checkbox">
                                                    </th>
                                                    <th class="px-2 py-1">Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $selectedAccounts = explode(',', request('selected_accounts', ''));
                                                @endphp
                                                {{-- @foreach ($accounts as $akun)
                                                    <tr class="hover:bg-gray-50" data-level="{{ $akun->level_akun }}"
                                                        data-tipe="{{ strtolower($akun->tipe_akun) }}">
                                                        <td class="px-2 py-1">
                                                            <input type="checkbox" class="account-checkbox form-checkbox"
                                                                value="{{ $akun->kode_akun }} - {{ $akun->nama_akun }}"
                                                                {{ in_array($akun->kode_akun . ' - ' . $akun->nama_akun, $selectedAccounts) ? 'checked' : '' }}>
                                                        </td>
                                                        <td class="px-2 py-1">
                                                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                                        </td>
                                                    </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Tombol Filter --}}
                                <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                        <i class="fas fa-filter mr-2"></i> Ok
                                    </button>
                                    <a href=""
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 font-medium text-sm rounded-md hover:bg-gray-200">
                                        <i class="fas fa-undo mr-2"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="mt-4 text-right">
                            <button onclick="document.getElementById('fileModify').classList.add('hidden')"
                                class="text-sm text-gray-500 hover:text-gray-700">Tutup</button>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-4">LAPORAN CASH FLOW</h3>
                <div class="mb-4">
                    <p class="text-sm">
                        <span class="font-semibold">Periode:</span>
                        {{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
                    </p>
                </div>
                <table class="min-w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">Tanggal</th>
                            <th class="border p-2">Source</th>
                            <th class="border p-2">Akun Kas/Bank</th>
                            <th class="border p-2">Lawan Akun</th>
                            <th class="border p-2">Keterangan</th>
                            <th class="border p-2 text-right">Cash In</th>
                            <th class="border p-2 text-right">Cash Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td class="border p-2">{{ \Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y') }}</td>
                                <td class="border p-2">{{ $r['source'] }}</td>
                                <td class="border p-2">{{ $r['akun_kas'] }}</td>
                                <td class="border p-2">{{ $r['lawan_akun'] }}</td>
                                <td class="border p-2">{{ $r['keterangan'] }}</td>
                                <td class="border p-2 text-right">{{ number_format($r['cash_in'], 2) }}</td>
                                <td class="border p-2 text-right">{{ number_format($r['cash_out'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const periodeSelect = document.getElementById('periode');
            const tanggalInput = document.getElementById('start_date');
            const tanggalAkhir = document.getElementById('end_date');

            // helper untuk ganti tahun tapi pertahankan bulan/hari
            function gantiTahun(dateStr, tahunBaru) {
                if (!dateStr) return '';
                const parts = dateStr.split('-'); // [YYYY, MM, DD]
                return `${tahunBaru}-${parts[1]}-${parts[2]}`;
            }

            function setRangeFromOption(option) {
                const tahun = option?.getAttribute('data-tahun')?.trim();
                if (/^\d{4}$/.test(tahun)) {
                    tanggalInput.min = `${tahun}-01-01`;
                    tanggalInput.max = `${tahun}-12-31`;
                    tanggalAkhir.min = `${tahun}-01-01`;
                    tanggalAkhir.max = `${tahun}-12-31`;

                    // Ganti tahun saja, bulan & tanggal tetap
                    tanggalInput.value = gantiTahun(tanggalInput.value, tahun) || `${tahun}-01-01`;
                    tanggalAkhir.value = gantiTahun(tanggalAkhir.value, tahun) || `${tahun}-12-31`;
                } else {
                    tanggalInput.min = tanggalInput.max = '';
                    tanggalAkhir.min = tanggalAkhir.max = '';
                    tanggalInput.value = '';
                    tanggalAkhir.value = '';
                }
            }

            // Saat select berubah
            periodeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                setRangeFromOption(selectedOption);
            });

            // Saat pertama kali load
            const selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                setRangeFromOption(selectedOption);
            }
        });
    </script>
    <script>
        document.getElementById('menu-button').addEventListener('click', function() {
            document.getElementById('dropdown-menu').classList.toggle('hidden');
        });

        window.addEventListener('click', function(e) {
            const button = document.getElementById('menu-button');
            const menu = document.getElementById('dropdown-menu');
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.account-checkbox');
            const hiddenInput = document.getElementById('selected_accounts');

            // Toggle semua checkbox
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedAccounts();
            });

            // Saat checkbox akun diklik
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const row = cb.closest('tr');
                    const level = row.dataset.level;
                    const tipe = row.dataset.tipe;

                    // Kalau akun ini level Header → toggle semua akun dengan tipe sama
                    if (level && level.toLowerCase() === 'header') {
                        const allSameType = document.querySelectorAll(
                            `#account-table tbody tr[data-tipe="${tipe}"] .account-checkbox`
                        );
                        allSameType.forEach(cb2 => {
                            cb2.checked = cb.checked;
                        });
                    }

                    updateSelectedAccounts();
                });
            });

            function updateSelectedAccounts() {
                const selected = [];
                checkboxes.forEach(cb => {
                    if (cb.checked) selected.push(cb.value);
                });
                hiddenInput.value = selected.join(',');
            }
        });

        // Search/filter functionality
        document.getElementById('search-account').addEventListener('keyup', function() {
            var keyword = this.value.toLowerCase();
            var rows = document.querySelectorAll('#account-table tbody tr');

            rows.forEach(function(row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    </script>
@endsection

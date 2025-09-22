@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div>
            <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600">
                <li><a
                        href="{{ route('income_statement.export', ['start_date' => $tanggalAwal, 'end_date' => $tanggalAkhir, 'format' => 'excel']) }}">Export
                        to Excel</a></li>
                <li><a
                        href="{{ route('income_statement.export', ['start_date' => $tanggalAwal, 'end_date' => $tanggalAkhir, 'format' => 'pdf']) }}">Export
                        to PDF</a></li>
                {{-- <li><a href="#pricing" class="tab-link">Print</a></li> --}}
                <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')" class="tab-link">Modify</a>
                </li>
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
                    <form method="GET" action="{{ route('income_statement.income_statement_report') }}"
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                        {{-- tanggal awal --}}
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                Awal</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        {{-- Tanggal Akhir --}}
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                required>
                        </div>

                        {{-- Tombol Filter --}}
                        <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                <i class="fas fa-filter mr-2"></i> Filter
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
        <h1 class="text-xl font-bold mb-2">
            {{ $siteTitle }}
        </h1>
        <h3 class="text-xl font-bold mb-4">LAPORAN LABA RUGI</h3>
        <p class="text-gray-600 mb-6">
            Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') }} -
            {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
        </p>

        {{-- =======================
         Bagian PENDAPATAN
    ======================== --}}
        @if (!empty($groupsPendapatan))
            <h2 class="text-xl font-semibold text-gray-900 mb-3">PENDAPATAN</h2>
            @foreach ($groupsPendapatan as $group)
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">{{ $group['group'] }}</h3>
                    <table class="w-full text-xs">
                        <tbody>
                            @foreach ($group['akun'] as $akun)
                                <tr>
                                    <td class="pl-4 pr-4 py-1 text-gray-700">
                                        <div class="flex justify-between">
                                            <span><a
                                                    href="{{ route('buku_besar.buku_besar_report', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'selected_accounts' => $akun['kode_akun']]) }}">{{ $akun['kode_akun'] }}
                                                    - {{ $akun['nama_akun'] }}</a></span>
                                            <span>{{ number_format($akun['saldo'], 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-t border-gray-300 font-bold">
                                <td class="pl-4 pr-4 py-2 text-gray-900">
                                    <div class="flex justify-between">
                                        <span>SUBTOTAL {{ $group['group'] }}</span>
                                        <span>{{ number_format($group['saldo_group'], 2, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach

            {{-- Total global Pendapatan (semua grup pendapatan) --}}
            <div class="flex justify-between font-bold text-gray-900 text-base mt-2 mb-8 border-t pt-2">
                <span>TOTAL PENDAPATAN</span>
                <span>{{ number_format($totalPendapatan, 2, ',', '.') }}</span>
            </div>
        @endif

        {{-- ===================
         Bagian BEBAN
    ==================== --}}
        @if (!empty($groupsBeban))
            <h2 class="text-xl font-semibold text-gray-900 mb-3">BEBAN</h2>
            @foreach ($groupsBeban as $group)
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">{{ $group['group'] }}</h3>
                    <table class="w-full text-xs">
                        <tbody>
                            @foreach ($group['akun'] as $akun)
                                <tr>
                                    <td class="pl-4 pr-4 py-1 text-gray-700">
                                        <div class="flex justify-between">
                                            <span>{{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}</span>
                                            <span>{{ number_format($akun['saldo'], 2, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-t border-gray-300 font-bold">
                                <td class="pl-4 pr-4 py-2 text-gray-900">
                                    <div class="flex justify-between">
                                        <span>SUBTOTAL {{ $group['group'] }}</span>
                                        <span>{{ number_format($group['saldo_group'], 2, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach

            {{-- Total global Beban (semua grup beban) --}}
            <div class="flex justify-between font-bold text-gray-900 text-base mt-2 mb-8 border-t pt-2">
                <span>TOTAL BEBAN</span>
                <span>{{ number_format($totalBeban, 2, ',', '.') }}</span>
            </div>
        @endif

        {{-- ==========================
         RINGKASAN AKHIR
    =========================== --}}
        <div class="mt-4 border-t pt-4">
            <div class="flex justify-between font-bold text-gray-900 text-sm mt-2">
                <span>LABA SEBELUM PAJAK PENGHASILAN</span>
                <span>{{ number_format($labaSebelumPajak, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-sm mt-4">
                <span>BEBAN PAJAK PENGHASILAN</span>
                <span>{{ number_format($bebanPajak, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-sm mt-4">
                <span>LABA BERSIH SETELAH PAJAK PENGHASILAN</span>
                <span>{{ number_format($labaSetelahPajak, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

@endsection

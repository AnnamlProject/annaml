@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6 flex justify-end gap-2">
            <a href="{{ route('income_statement.export', ['start_date' => $tanggalAwal, 'end_date' => $tanggalAkhir, 'format' => 'excel']) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <a href="{{ route('income_statement.export', ['start_date' => $tanggalAwal, 'end_date' => $tanggalAkhir, 'format' => 'pdf']) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
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
            <div class="flex justify-between font-bold text-gray-900 text-lg mt-2">
                <span>LABA SEBELUM PAJAK PENGHASILAN</span>
                <span>{{ number_format($labaSebelumPajak, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-lg mt-4">
                <span>BEBAN PAJAK PENGHASILAN</span>
                <span>{{ number_format($bebanPajak, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-lg mt-4">
                <span>LABA BERSIH SETELAH PAJAK PENGHASILAN</span>
                <span>{{ number_format($labaSetelahPajak, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
@endsection

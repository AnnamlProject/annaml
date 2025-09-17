@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-xl font-bold mb-2">
            {{ $siteTitle }}
        </h1>
        <h3 class="text-xl font-bold mb-4">LAPORAN LABA RUGI</h3>
        <p class="text-gray-600 mb-6">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} -
            {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
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

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-xl font-bold mb-4">Laporan Laba Rugi</h1>
        <p class="text-gray-600 mb-6">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} -
            {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
        </p>

        @foreach ($incomeStatement as $group)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $group['group'] }}</h2>
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
                        <tr class="border-t border-gray-300 font-semibold">
                            <td class="pl-4 pr-4 py-2 text-gray-800">
                                <div class="flex justify-between">
                                    <span>Subtotal {{ $group['group'] }}</span>
                                    <span>{{ number_format($group['saldo_group'], 2, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="mt-8 border-t pt-4">
            <div class="flex justify-between font-bold text-gray-800">
                <span>Total Pendapatan</span>
                <span>{{ number_format($totalPendapatan, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 mt-1">
                <span>Total Beban</span>
                <span>{{ number_format($totalBeban, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-lg mt-4">
                <span>Laba Bersih</span>
                <span>{{ number_format($labaBersih, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
@endsection

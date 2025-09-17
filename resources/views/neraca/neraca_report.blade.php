@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-xl font-bold mb-2">
            {{ $siteTitle }}
        </h1>
        <h5 class="text-xl font-bold mb-2">
            Neraca / Balance Sheet
        </h5>
        <p class="text-gray-600 mb-6">
            Per {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
        </p>

        {{-- Looping untuk tiap kelompok (Aset, Kewajiban, Ekuitas) --}}
        @foreach (['Aset', 'Kewajiban', 'Ekuitas'] as $tipe)
            @if (!empty($neraca[$tipe]))
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $tipe }}</h2>
                    <div class="space-y-1 text-sm">
                        @php $total = 0; @endphp
                        @foreach ($neraca[$tipe] as $akun)
                            <div class="flex items-center">
                                <span class="flex-1 font-{{ $akun['level_akun'] === 'HEADER' ? 'semibold' : 'normal' }}">
                                    @if ($showAccountNumber)
                                        {{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}
                                    @else
                                        {{ $akun['nama_akun'] }}
                                    @endif
                                    @if ($akun['level_akun'] === 'X')
                                        <span class="text-xs text-gray-500">(Laba Tahun Berjalan)</span>
                                    @endif
                                </span>

                                {{-- Nominal hanya untuk akun non-header --}}
                                @if ($akun['level_akun'] !== 'HEADER')
                                    <span class="w-32 text-right font-mono">
                                        {{ number_format($akun['saldo'], 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="w-32 text-right font-mono">&nbsp;</span>
                                @endif
                            </div>

                            @php
                                // Jumlahkan hanya kalau bukan header
                                if ($akun['level_akun'] !== 'HEADER') {
                                    $total += $akun['saldo'];
                                }
                            @endphp
                        @endforeach



                        <div class="flex items-center font-bold border-t pt-2 mt-2">
                            <span class="flex-1">TOTAL {{ $tipe }}</span>
                            <span class="w-32 text-right font-mono">
                                {{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="flex-1">TOTAL KEWAJIBAN DAN EKUITAS</span>
                            <span class="w-32 text-right font-mono">
                                {{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Ringkasan Balance --}}
    </div>
@endsection

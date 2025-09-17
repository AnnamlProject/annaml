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

        {{-- Tombol Export --}}
        <div class="mb-6 flex gap-2">
            <a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'excel']) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'pdf']) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>

        {{-- Looping tiap kelompok akun --}}
        @foreach (['Aset', 'Kewajiban', 'Ekuitas'] as $tipe)
            @if (!empty($neraca[$tipe]))
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ strtoupper($tipe) }}</h2>
                    <div class="space-y-1 text-sm">
                        @php
                            $total = 0;
                            $currentGroup = null;
                            $groupTotal = null;
                        @endphp

                        @foreach ($neraca[$tipe] as $akun)
                            {{-- HEADER --}}
                            @if ($akun['level_akun'] === 'HEADER')
                                <div class="mt-4 font-bold text-gray-900">
                                    {{ $akun['nama_akun'] }}
                                </div>

                                {{-- GROUP ACCOUNT --}}
                            @elseif ($akun['level_akun'] === 'GROUP ACCOUNT')
                                {{-- Tutup SUBTOTAL grup sebelumnya --}}
                                @if ($currentGroup && $groupTotal !== null)
                                    <div class="flex items-center font-semibold border-t pt-2 mt-1">
                                        <span class="flex-1">SUBTOTAL {{ $currentGroup }}</span>
                                        <span class="w-32 text-right font-arial">
                                            {{ number_format($groupTotal, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                @php
                                    $currentGroup = $akun['nama_akun'];
                                    $groupTotal = 0;
                                @endphp
                                <div class="mt-2 font-semibold text-gray-700">
                                    {{ $akun['nama_akun'] }}
                                </div>

                                {{-- ACCOUNT / LABA BERJALAN --}}
                            @else
                                <div class="flex items-center">
                                    <span class="flex-1">
                                        @if ($showAccountNumber)
                                            {{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}
                                        @else
                                            {{ $akun['nama_akun'] }}
                                        @endif
                                        @if ($akun['level_akun'] === 'X')
                                            <span class="text-xs text-gray-500"></span>
                                        @endif
                                    </span>
                                    <span class="w-32 text-right font-arial">
                                        {{ number_format($akun['saldo'], 2, ',', '.') }}
                                    </span>
                                </div>

                                @php
                                    $total += $akun['saldo'];
                                    if ($groupTotal !== null) {
                                        $groupTotal += $akun['saldo'];
                                    }
                                @endphp
                            @endif
                        @endforeach

                        {{-- Tutup subtotal grup terakhir --}}
                        @if ($currentGroup && $groupTotal !== null)
                            <div class="flex items-center font-semibold border-t pt-2 mt-1">
                                <span class="flex-1">SUBTOTAL {{ $currentGroup }}</span>
                                <span class="w-32 text-right font-arial">
                                    {{ number_format($groupTotal, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        {{-- Total tipe akun --}}
                        <div class="flex items-center font-bold border-t pt-2 mt-2">
                            <span class="flex-1">TOTAL {{ strtoupper($tipe) }}</span>
                            <span class="w-32 text-right font-arial">
                                {{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Ringkasan Balance --}}
        <div class="mt-10 border-t pt-4">
            {{-- <h2 class="text-lg font-bold text-gray-800 mb-3">Ringkasan Neraca</h2> --}}
            {{-- <div class="flex items-center mb-1">
                <span class="flex-1">Total Aset</span>
                <span class="w-32 text-right font-arial">
                    {{ number_format($grandTotalAset, 2, ',', '.') }}
                </span>
            </div> --}}
            <div class="flex items-center mb-1">
                <span class="flex-1">TOTAL KEWAJIBAN DAN EKUITAS</span>
                <span class="w-32 text-right font-arial">
                    {{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.') }}
                </span>
            </div>
            {{-- 
            <div
                class="mt-3 font-semibold
            @if ($grandTotalAset == $grandTotalKewajiban + $grandTotalEkuitas) text-green-600
            @else text-red-600 @endif">
                @if ($grandTotalAset == $grandTotalKewajiban + $grandTotalEkuitas)
                    ✅ Neraca seimbang
                @else
                    ⚠️ Neraca tidak seimbang
                @endif
            </div> --}}
        </div>
    </div>
@endsection

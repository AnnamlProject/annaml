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
                                <span class="flex-1">
                                    {{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}
                                    @if ($akun['level_akun'] === 'X')
                                        <span class="text-xs text-gray-500"></span>
                                    @endif
                                </span>
                                <span class="w-32 text-right font-mono">
                                    {{ number_format($akun['saldo'], 2, ',', '.') }}
                                </span>
                            </div>
                            @php $total += $akun['saldo']; @endphp
                        @endforeach

                        <div class="flex items-center font-bold border-t pt-2 mt-2">
                            <span class="flex-1">Total {{ $tipe }}</span>
                            <span class="w-32 text-right font-mono">
                                {{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Ringkasan Balance --}}
        <div class="mt-10 p-4 border-t">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Ringkasan Neraca</h2>
            <div class="flex items-center mb-1">
                <span class="flex-1">Total Aset</span>
                <span class="w-32 text-right font-mono">
                    {{ number_format($grandTotalAset, 2, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center mb-1">
                <span class="flex-1">Total Kewajiban + Ekuitas</span>
                <span class="w-32 text-right font-mono">
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

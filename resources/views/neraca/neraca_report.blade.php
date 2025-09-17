{{-- resources/views/neraca/report.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        {{-- Tombol Export --}}
        <div class="mb-6 flex justify-end gap-2">
            <a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'excel']) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'pdf']) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>

        {{-- Logo + Judul --}}
        <div class="flex items-center gap-4 mb-4">
            <img src="{{ asset('storage/' . \App\Setting::get('logo', 'logo.jpg')) }}" alt="Logo" class="h-12">
            <h1 class="text-xl mt-5 font-bold uppercase">{{ $siteTitle }}</h1>
        </div>

        <h5 class="text-xl font-bold mb-2">NERACA</h5>
        <p class="text-gray-600 mb-6 uppercase">
            PER {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
        </p>

        {{-- Loop tipe akun --}}
        @foreach (['Aset', 'Kewajiban', 'Ekuitas'] as $tipe)
            @if (!empty($neraca[$tipe]))
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 uppercase mb-2">{{ strtoupper($tipe) }}</h2>

                    <div class="space-y-1 text-sm">
                        @php
                            $total = 0;
                            $currentGroup = null;
                            $groupTotal = null;
                        @endphp

                        @foreach ($neraca[$tipe] as $akun)
                            {{-- HEADER --}}
                            @if ($akun['level_akun'] === 'HEADER')
                                <div class="mt-4 font-bold text-gray-900">{{ $akun['nama_akun'] }}</div>

                                {{-- GROUP ACCOUNT --}}
                            @elseif ($akun['level_akun'] === 'GROUP ACCOUNT')
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
                                <div class="mt-2 font-semibold text-gray-700">{{ $akun['nama_akun'] }}</div>

                                {{-- ACCOUNT (parent) --}}
                            @elseif ($akun['level_akun'] === 'ACCOUNT')
                                @php
                                    // --- BEGIN PATCH: hitung prefix parent ---
                                    $parentCode = (string) $akun['kode_akun'];
                                    $parentPrefix = rtrim($parentCode, '0'); // buang trailing zero, contoh: '1101000' -> '1101'

                                    // Normalisasi pembanding level_akun agar tahan spasi/case
                                    $norm = function ($v) {
                                        return strtoupper(trim((string) $v));
                                    };

                                    $hasChild = false;
                                    foreach ($neraca[$tipe] as $sub) {
                                        if ($norm($sub['level_akun']) !== 'SUB ACCOUNT') {
                                            continue;
                                        }

                                        $subCode = (string) $sub['kode_akun'];
                                        if (\Illuminate\Support\Str::startsWith($subCode, $parentPrefix)) {
                                            $hasChild = true;
                                            break;
                                        }
                                    }
                                    // --- END PATCH ---
                                @endphp

                                <div class="border-b pb-1">
                                    <div class="flex items-center">
                                        {{-- Tombol expand hanya muncul jika punya anak --}}
                                        @if ($hasChild)
                                            <button type="button" class="mr-2 text-xs text-blue-600 w-6 toggle-btn"
                                                data-target="sub-{{ $akun['kode_akun'] }}">
                                                [+]
                                            </button>
                                        @else
                                            <span class="mr-2 w-6"></span>
                                        @endif

                                        {{-- Nama akun --}}
                                        <span class="flex-1">
                                            @if ($showAccountNumber)
                                                {{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}
                                            @else
                                                {{ $akun['nama_akun'] }}
                                            @endif
                                        </span>

                                        {{-- Saldo --}}
                                        <span class="w-32 text-right font-arial">
                                            {{ number_format($akun['saldo'], 2, ',', '.') }}
                                        </span>
                                    </div>

                                    <div id="sub-{{ $akun['kode_akun'] }}"
                                        class="pl-6 mt-1 space-y-1 text-gray-600 hidden sub-account" style="display:none">
                                        @foreach ($neraca[$tipe] as $sub)
                                            @if (
                                                $norm($sub['level_akun']) === 'SUB ACCOUNT' &&
                                                    \Illuminate\Support\Str::startsWith((string) $sub['kode_akun'], $parentPrefix))
                                                <div class="flex items-center">
                                                    <span class="flex-1 pl-4"> {{-- indent tambahan untuk teks sub account --}}
                                                        @if ($showAccountNumber)
                                                            {{ $sub['kode_akun'] }} - {{ $sub['nama_akun'] }}
                                                        @else
                                                            {{ $sub['nama_akun'] }}
                                                        @endif
                                                    </span>
                                                    <span class="w-32 text-right font-arial">
                                                        {{ number_format($sub['saldo'], 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>


                                </div>

                                @php
                                    // Agregasi hanya pada parent (SUB ACCOUNT tidak ditotal ulang di loop utama)
                                    $total += $akun['saldo'];
                                    if ($groupTotal !== null) {
                                        $groupTotal += $akun['saldo'];
                                    }
                                @endphp

                                {{-- SUB ACCOUNT: di-skip di loop utama supaya tidak tampil dari awal --}}
                            @elseif ($akun['level_akun'] === 'SUB ACCOUNT')
                                @continue

                                {{-- Pos lain (mis. laba berjalan / X) --}}
                            @else
                                <div class="flex items-center">
                                    <span class="flex-1">
                                        {{ $akun['nama_akun'] }}
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

                        {{-- Subtotal terakhir pada group --}}
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

        {{-- Ringkasan --}}
        <div class="mt-10 border-t pt-4">
            <div class="flex items-center mb-1">
                <span class="flex-1 font-bold">TOTAL KEWAJIBAN DAN EKUITAS</span>
                <span class="w-32 text-right font-arial">
                    {{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".toggle-btn");
            // console.log("🔍 Jumlah tombol expand ditemukan:", buttons.length);

            buttons.forEach(function(btn) {
                btn.addEventListener("click", function() {
                    const targetId = this.dataset.target;
                    const target = document.getElementById(targetId);

                    // console.log("Klik tombol untuk:", targetId);

                    // if (!target) {
                    //     console.warn("⚠️ Tidak ada elemen dengan ID:", targetId);
                    //     return;
                    // }

                    // Toggle visibilitas
                    if (target.classList.contains("hidden")) {
                        target.classList.remove("hidden");
                        target.style.display = "";
                        this.textContent = "[-]";
                    } else {
                        target.classList.add("hidden");
                        target.style.display = "none";
                        this.textContent = "[+]";
                    }
                });
            });
        });
    </script>
@endpush

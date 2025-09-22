{{-- resources/views/neraca/report.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        {{-- Tombol Export --}}
        <div>
            <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600">
                <li><a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'excel']) }}">Export
                        to Excel</a></li>
                <li><a href="{{ route('neraca.export', ['end_date' => $tanggalAkhir, 'format' => 'pdf']) }}">Export
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
                    <form method="GET" action="{{ route('neraca.neraca_report') }}"
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                        {{-- Tanggal Akhir --}}
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Show Account Number</label>
                            <div class="space-y-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="show_account_number" value="show_account_number"
                                        class="text-blue-600 focus:ring-blue-500"
                                        {{ request('show_account_number') == 'show_account_number' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">Show Account Number</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hide Accounts With Zero Balance
                            </label>
                            <div class="space-y-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hide_account_with_zero" value="hide_account_with_zero"
                                        class="text-blue-600 focus:ring-blue-500"
                                        {{ request('hide_account_with_zero') == 'hide_account_with_zero' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">Hide Accounts With Zero Balance</span>
                                </label>
                            </div>
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
                            $total = 0; // total per tipe (hanya dari ACCOUNT + pos lain non-struktur)
                            $currentGroup = null; // nama group aktif (GROUP ACCOUNT)
                            $groupTotal = null; // subtotal untuk group aktif
                            $norm = fn($v) => strtoupper(trim((string) $v));
                        @endphp

                        @foreach ($neraca[$tipe] as $akun)
                            {{-- HEADER: hanya label --}}
                            @if ($akun['level_akun'] === 'HEADER')
                                <div class="mt-4 font-bold text-gray-900">{{ $akun['nama_akun'] }}</div>

                                {{-- GROUP ACCOUNT: mulai subtotal baru --}}
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

                                {{-- ACCOUNT (parent, saldo sudah agregat dari controller) --}}
                            @elseif ($akun['level_akun'] === 'ACCOUNT')
                                @php
                                    $parentSaldo = $akun['saldo'] ?? 0;
                                    $parentCode = (string) $akun['kode_akun'];
                                    $parentPrefix = rtrim($parentCode, '0');

                                    // Cari anak dari koleksi neraca tipe ini (level SUB ACCOUNT + prefix cocok)
                                    $childAccounts = collect($neraca[$tipe])->filter(
                                        fn($sub) => $norm($sub['level_akun']) === 'SUB ACCOUNT' &&
                                            \Illuminate\Support\Str::startsWith(
                                                (string) $sub['kode_akun'],
                                                $parentPrefix,
                                            ),
                                    );
                                    $childCodes = $childAccounts->pluck('kode_akun')->all();
                                    $hasChild = $childAccounts->isNotEmpty();

                                    // Hide-zero sudah ditangani di controller; di sini aman.
                                    $allAccounts = implode(',', array_merge([$akun['kode_akun']], $childCodes));
                                @endphp

                                <div class="border-b pb-1">
                                    <div class="flex items-center">
                                        {{-- Toggle anak --}}
                                        @if ($hasChild)
                                            <button type="button" class="mr-2 text-xs text-blue-600 w-6 toggle-btn"
                                                data-target="sub-{{ $akun['kode_akun'] }}">[+]</button>
                                        @else
                                            <span class="mr-2 w-6"></span>
                                        @endif

                                        {{-- Nama akun --}}
                                        <span class="flex-1">
                                            <a
                                                href="{{ route('buku_besar.buku_besar_report', [
                                                    'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                    'end_date' => request('end_date') ?? $tanggalAkhir,
                                                    'selected_accounts' => $allAccounts, // parent + seluruh anak
                                                ]) }}">
                                                {{ $showAccountNumber ? $akun['kode_akun'] . ' - ' . $akun['nama_akun'] : $akun['nama_akun'] }}
                                            </a>
                                        </span>

                                        {{-- Saldo parent (agregat) --}}
                                        <span class="w-32 text-right font-arial">
                                            {{ number_format($parentSaldo, 2, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- Daftar anak --}}
                                    <div id="sub-{{ $akun['kode_akun'] }}"
                                        class="pl-6 mt-1 space-y-1 text-gray-600 hidden sub-account" style="display:none">
                                        @foreach ($childAccounts as $sub)
                                            @php
                                                // Untuk anak, tampilkan jika tidak hide-zero atau saldonya != 0
                                                $showChild = !($hideAccountWithZero && ($sub['saldo_self'] ?? 0) == 0);
                                            @endphp
                                            @if ($showChild)
                                                <div class="flex items-center">
                                                    <span class="flex-1 pl-4">
                                                        <a
                                                            href="{{ route('buku_besar.buku_besar_report', [
                                                                'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                                'end_date' => request('end_date') ?? $tanggalAkhir,
                                                                'selected_accounts' => $sub['kode_akun'],
                                                            ]) }}">
                                                            {{ $showAccountNumber ? $sub['kode_akun'] . ' - ' . $sub['nama_akun'] : $sub['nama_akun'] }}
                                                        </a>
                                                    </span>
                                                    <span class="w-32 text-right font-arial">
                                                        {{ number_format($sub['saldo_self'] ?? ($sub['saldo'] ?? 0), 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                @php
                                    // Hanya parent (agregat) yang masuk subtotal/total
                                    $total += $parentSaldo;
                                    if (!is_null($groupTotal)) {
                                        $groupTotal += $parentSaldo;
                                    }
                                @endphp

                                {{-- SUB ACCOUNT (di-skip pada loop utama → hanya tampil di bawah parent) --}}
                            @elseif ($akun['level_akun'] === 'SUB ACCOUNT')
                                @continue

                                {{-- Pos lain (mis. X/laba berjalan) --}}
                            @else
                                <div class="flex items-center">
                                    <span class="flex-1">{{ $akun['nama_akun'] }}</span>
                                    <span class="w-32 text-right font-arial">
                                        {{ number_format($akun['saldo'] ?? 0, 2, ',', '.') }}
                                    </span>
                                </div>
                                @php
                                    $total += $akun['saldo'] ?? 0;
                                    if ($groupTotal !== null) {
                                        $groupTotal += $akun['saldo'] ?? 0;
                                    }
                                @endphp
                            @endif
                        @endforeach

                        {{-- Subtotal terakhir pada group --}}
                        @if ($currentGroup && $groupTotal !== null)
                            <div class="flex items-center font-semibold border-t pt-2 mt-1">
                                <span class="flex-1">SUBTOTAL {{ $currentGroup }}</span>
                                <span class="w-32 text-right font-arial">~
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
        ~

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
@endpush

{{-- resources/views/neraca/neraca_report.blade.php --}}
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
                <li><a onclick="document.getElementById('fileModify').classList.toggle('hidden')" class="tab-link cursor-pointer">Modify</a>
                </li>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hide Accounts With Zero Balance</label>
                            <div class="space-y-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hide_account_with_zero" value="hide_account_with_zero"
                                        class="text-blue-600 focus:ring-blue-500"
                                        {{ request('hide_account_with_zero') == 'hide_account_with_zero' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">Hide Accounts With Zero Balance</span>
                                </label>
                            </div>
                        </div>
                        <div class="sm:col-span-3 flex items-center gap-2 mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-md shadow hover:bg-blue-700">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            <a href="{{ route('neraca.neraca_report') }}"
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

        {{-- Tabel Neraca dengan Hierarki --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
            <table class="w-full text-sm" id="neracaTable">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-700 to-slate-800 text-white">
                        <th class="text-left py-3 px-4 font-semibold w-1/2">KETERANGAN</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">SUB ACCOUNT</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">ACCOUNT</th>
                        <th class="text-right py-3 px-4 font-semibold w-1/6">GROUP ACCOUNT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // Grayscale color scheme for professional black-and-white look
                        $tipeConfig = [
                            'Aset' => ['headerBg' => 'bg-gray-800', 'groupBg' => 'bg-gray-100', 'groupBorder' => 'border-gray-600', 'textColor' => 'text-gray-900', 'totalBg' => 'bg-gray-900'],
                            'Kewajiban' => ['headerBg' => 'bg-gray-800', 'groupBg' => 'bg-gray-100', 'groupBorder' => 'border-gray-600', 'textColor' => 'text-gray-900', 'totalBg' => 'bg-gray-900'],
                            'Ekuitas' => ['headerBg' => 'bg-gray-800', 'groupBg' => 'bg-gray-100', 'groupBorder' => 'border-gray-600', 'textColor' => 'text-gray-900', 'totalBg' => 'bg-gray-900'],
                        ];
                        $norm = fn($v) => strtoupper(trim((string) $v));
                    @endphp

                    @foreach (['Aset', 'Kewajiban', 'Ekuitas'] as $tipeIndex => $tipe)
                        @if (!empty($neraca[$tipe]))
                            @php
                                $config = $tipeConfig[$tipe];
                                $total = 0;
                                $currentGroupName = null;
                                $currentGroupTotal = 0;
                                $groupIndex = 0;
                            @endphp

                            {{-- HEADER TIPE (ASET/KEWAJIBAN/EKUITAS) --}}
                            <tr class="{{ $config['headerBg'] }} text-white">
                                <td colspan="4" class="py-2.5 px-4 font-bold text-base tracking-wide">{{ strtoupper($tipe) }}</td>
                            </tr>

                            @foreach ($neraca[$tipe] as $akunIndex => $akun)
                                {{-- HEADER --}}
                                @if ($akun['level_akun'] === 'HEADER')
                                    {{-- Tutup group sebelumnya jika ada --}}
                                    @if ($currentGroupName && $currentGroupTotal != 0)
                                        <tr class="{{ $config['groupBg'] }} border-t">
                                            <td class="py-2 px-4 font-semibold {{ $config['textColor'] }}">Subtotal {{ $currentGroupName }}</td>
                                            <td></td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-bold {{ $config['textColor'] }}">{{ number_format($currentGroupTotal, 2, ',', '.') }}</td>
                                        </tr>
                                        @php $currentGroupName = null; $currentGroupTotal = 0; @endphp
                                    @endif
                                    <tr class="bg-gray-100">
                                        <td colspan="4" class="py-2 px-4 font-bold text-gray-900">{{ $akun['nama_akun'] }}</td>
                                    </tr>

                                {{-- GROUP ACCOUNT --}}
                                @elseif ($akun['level_akun'] === 'GROUP ACCOUNT')
                                    {{-- Tutup group sebelumnya jika ada --}}
                                    @if ($currentGroupName && $currentGroupTotal != 0)
                                        <tr class="{{ $config['groupBg'] }} border-t">
                                            <td class="py-2 px-4 font-semibold {{ $config['textColor'] }}">Subtotal {{ $currentGroupName }}</td>
                                            <td></td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-bold {{ $config['textColor'] }}">{{ number_format($currentGroupTotal, 2, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $currentGroupName = $akun['nama_akun'];
                                        $currentGroupTotal = 0;
                                        $groupIndex++;
                                    @endphp
                                    {{-- Group Account Row - Collapsible --}}
                                    <tr class="{{ $config['groupBg'] }} hover:bg-opacity-70 transition-colors cursor-pointer group-toggle border-l-4 {{ $config['groupBorder'] }}"
                                        data-target="{{ $tipe }}-group-{{ $groupIndex }}">
                                        <td class="py-2.5 px-4">
                                            <span class="flex items-center font-semibold {{ $config['textColor'] }}">
                                                <span class="toggle-icon mr-2 w-5 h-5 flex items-center justify-center {{ str_replace('bg-', 'bg-', $config['groupBg']) }} rounded text-xs font-bold">+</span>
                                                {{ $akun['nama_akun'] }}
                                            </span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td class="py-2.5 px-4 text-right font-bold {{ $config['textColor'] }} group-total" id="{{ $tipe }}-group-{{ $groupIndex }}-total"></td>
                                    </tr>

                                {{-- ACCOUNT --}}
                                @elseif ($akun['level_akun'] === 'ACCOUNT')
                                    @php
                                        $parentSaldo = $akun['saldo'] ?? 0;
                                        $parentCode = (string) $akun['kode_akun'];
                                        $parentPrefix = rtrim($parentCode, '0');

                                        $childAccounts = collect($neraca[$tipe])->filter(
                                            fn($sub) => $norm($sub['level_akun']) === 'SUB ACCOUNT' &&
                                                \Illuminate\Support\Str::startsWith((string) $sub['kode_akun'], $parentPrefix)
                                        );
                                        $hasChild = $childAccounts->isNotEmpty();
                                        // Only send account codes (not names) to keep URL short
                                        $allAccountCodes = implode(',', array_merge([$akun['kode_akun']], $childAccounts->pluck('kode_akun')->all()));
                                        
                                        $total += $parentSaldo;
                                        $currentGroupTotal += $parentSaldo;
                                    @endphp

                                    @if ($hasChild)
                                        {{-- ACCOUNT dengan SUB ACCOUNTS --}}
                                        <tr class="hidden {{ $tipe }}-group-{{ $groupIndex }} bg-white hover:bg-gray-50 transition-colors cursor-pointer account-toggle border-l-4 {{ str_replace('border-', 'border-', $config['groupBorder']) }} border-opacity-50"
                                            data-target="{{ $tipe }}-account-{{ $akunIndex }}">
                                            <td class="py-2 px-4 pl-10">
                                                <span class="flex items-center text-gray-700">
                                                    <span class="toggle-icon mr-2 text-gray-400 w-4 h-4 flex items-center justify-center bg-gray-100 rounded text-xs">+</span>
                                                    {{ $showAccountNumber ? $akun['kode_akun'] . ' - ' . $akun['nama_akun'] : $akun['nama_akun'] }}
                                                </span>
                                            </td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-medium text-gray-700">{{ number_format($parentSaldo, 2, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                        {{-- SUB ACCOUNTS --}}
                                        @foreach ($childAccounts as $sub)
                                            @php
                                                $showChild = !($hideAccountWithZero && ($sub['saldo_self'] ?? 0) == 0);
                                            @endphp
                                            @if ($showChild)
                                                <tr class="hidden {{ $tipe }}-account-{{ $akunIndex }} bg-gray-50 hover:bg-gray-100 transition-colors border-l-4 border-gray-200">
                                                    <td class="py-1.5 px-4 pl-16">
                                                        <a href="{{ route('buku_besar.buku_besar_report', [
                                                            'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                            'end_date' => request('end_date') ?? $tanggalAkhir,
                                                            'selected_accounts' => $sub['kode_akun'],
                                                        ]) }}"
                                                           class="text-gray-600 hover:text-blue-600 hover:underline">
                                                            {{ $showAccountNumber ? $sub['kode_akun'] . ' - ' . $sub['nama_akun'] : $sub['nama_akun'] }}
                                                        </a>
                                                    </td>
                                                    <td class="py-1.5 px-4 text-right text-gray-600">{{ number_format($sub['saldo_self'] ?? ($sub['saldo'] ?? 0), 2, ',', '.') }}</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        {{-- ACCOUNT tanpa SUB ACCOUNTS --}}
                                        <tr class="hidden {{ $tipe }}-group-{{ $groupIndex }} bg-white hover:bg-gray-50 transition-colors border-l-4 {{ str_replace('border-', 'border-', $config['groupBorder']) }} border-opacity-50">
                                            <td class="py-2 px-4 pl-10">
                                                <a href="{{ route('buku_besar.buku_besar_report', [
                                                    'start_date' => request('start_date') ?? \Carbon\Carbon::parse($tanggalAkhir)->startOfYear()->toDateString(),
                                                    'end_date' => request('end_date') ?? $tanggalAkhir,
                                                    'selected_accounts' => $allAccountCodes,
                                                ]) }}"
                                                   class="text-gray-700 hover:text-blue-600 hover:underline">
                                                    {{ $showAccountNumber ? $akun['kode_akun'] . ' - ' . $akun['nama_akun'] : $akun['nama_akun'] }}
                                                </a>
                                            </td>
                                            <td></td>
                                            <td class="py-2 px-4 text-right font-medium text-gray-700">{{ number_format($parentSaldo, 2, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                    @endif

                                {{-- SUB ACCOUNT - skip karena sudah ditampilkan di atas --}}
                                @elseif ($akun['level_akun'] === 'SUB ACCOUNT')
                                    @continue

                                {{-- Pos lain (X = Laba Tahun Berjalan) --}}
                                @else
                                    @php
                                        $total += $akun['saldo'] ?? 0;
                                        $currentGroupTotal += $akun['saldo'] ?? 0;
                                    @endphp
                                    <tr class="hidden {{ $tipe }}-group-{{ $groupIndex }} bg-white hover:bg-gray-50">
                                        <td class="py-2 px-4 pl-10 italic text-gray-700">{{ $akun['nama_akun'] }}</td>
                                        <td></td>
                                        <td class="py-2 px-4 text-right font-medium text-gray-700">{{ number_format($akun['saldo'] ?? 0, 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach

                            {{-- Subtotal group terakhir --}}
                            @if ($currentGroupName && $currentGroupTotal != 0)
                                <tr class="{{ $config['groupBg'] }} border-t">
                                    <td class="py-2 px-4 font-semibold {{ $config['textColor'] }}">Subtotal {{ $currentGroupName }}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="py-2 px-4 text-right font-bold {{ $config['textColor'] }}">{{ number_format($currentGroupTotal, 2, ',', '.') }}</td>
                                </tr>
                            @endif

                            {{-- TOTAL per tipe --}}
                            <tr class="{{ $config['totalBg'] }} text-white">
                                <td class="py-3 px-4 font-bold">TOTAL {{ strtoupper($tipe) }}</td>
                                <td></td>
                                <td></td>
                                <td class="py-3 px-4 text-right font-bold">
                                    @if ($tipe === 'Aset')
                                        {{ number_format($grandTotalAset, 2, ',', '.') }}
                                    @elseif ($tipe === 'Kewajiban')
                                        {{ number_format($grandTotalKewajiban, 2, ',', '.') }}
                                    @else
                                        {{ number_format($grandTotalEkuitas, 2, ',', '.') }}
                                    @endif
                                </td>
                            </tr>

                            {{-- Spacer --}}
                            <tr class="h-4 bg-gray-100"><td colspan="4"></td></tr>
                        @endif
                    @endforeach

                    {{-- RINGKASAN --}}
                    <tr class="bg-gradient-to-r from-gray-800 to-black text-white">
                        <td class="py-4 px-4 font-bold">TOTAL KEWAJIBAN DAN EKUITAS</td>
                        <td></td>
                        <td></td>
                        <td class="py-4 px-4 text-right font-bold">{{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
            <span class="flex items-center"><span class="w-3 h-3 bg-gray-800 rounded mr-1"></span> Aset</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-gray-600 rounded mr-1"></span> Kewajiban</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-gray-400 rounded mr-1"></span> Ekuitas</span>
            <span class="flex items-center"><i class="fas fa-plus-square text-gray-400 mr-1"></i> Klik untuk expand/collapse</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle GROUP
            document.querySelectorAll('.group-toggle').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return;
                    
                    const targetClass = this.getAttribute('data-target');
                    const targets = document.querySelectorAll('.' + targetClass);
                    const icon = this.querySelector('.toggle-icon');
                    
                    targets.forEach(function(target) {
                        if (target.classList.contains('hidden')) {
                            target.classList.remove('hidden');
                        } else {
                            target.classList.add('hidden');
                            // Also hide sub-accounts
                            const subTarget = target.getAttribute('data-target');
                            if (subTarget) {
                                document.querySelectorAll('.' + subTarget).forEach(function(sub) {
                                    sub.classList.add('hidden');
                                });
                                const subIcon = target.querySelector('.toggle-icon');
                                if (subIcon) subIcon.textContent = '+';
                            }
                        }
                    });
                    
                    icon.textContent = icon.textContent === '+' ? '-' : '+';
                });
            });

            // Toggle ACCOUNT
            document.querySelectorAll('.account-toggle').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return;
                    
                    const targetClass = this.getAttribute('data-target');
                    const targets = document.querySelectorAll('.' + targetClass);
                    const icon = this.querySelector('.toggle-icon');
                    
                    targets.forEach(function(target) {
                        target.classList.toggle('hidden');
                    });
                    
                    icon.textContent = icon.textContent === '+' ? '-' : '+';
                });
            });
        });
    </script>
@endpush

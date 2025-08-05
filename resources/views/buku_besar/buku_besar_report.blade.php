@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="mb-4">
                    <p class="text-sm">
                        <span class="font-semibold">Periode:</span>
                        {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                    </p>
                </div>

                @if ($rows->count() > 0)
                    @php
                        $grouped = $rows->groupBy('chartOfAccount.nama_akun');
                    @endphp

                    @foreach ($grouped as $namaAkun => $akunRows)
                        @php
                            $totalDebit = $akunRows->sum('debits');
                            $totalKredit = $akunRows->sum('credits');
                        @endphp


                        <div class="mb-6">
                            @php
                                $akunPertama = $akunRows->first();
                                $kodeAkun = $akunPertama->chartOfAccount->kode_akun ?? '-';
                                $namaAkun = $akunPertama->chartOfAccount->nama_akun ?? '-';
                                $saldoBerjalan = $startingBalances[$kodeAkun] ?? 0;
                            @endphp

                            <h3 class="text-lg font-bold mb-2">
                                {{ $kodeAkun }} - {{ $namaAkun }}
                            </h3>

                            <div class="overflow-x-auto">
                                <table style="table-layout: fixed; width: 100%;">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="w-20 px-2 py-1 border">Tanggal</th>
                                            <th class="w-50 px-2 py-1 text-center border">Comment</th>
                                            <th class="w-32 px-2 py-1 text-center border">Source</th>
                                            <th class="w-24 px-2 py-1 border text-right">Debits</th>
                                            <th class="w-24 px-2 py-1 border text-right">Credits</th>
                                            <th class="w-24 px-2 py-1 border text-right">Balance</th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        {{-- Baris saldo awal --}}
                                        <tr class="bg-gray-50 ">
                                            <td class="px-3 py-2 border text-center" colspan="3"></td>
                                            <td class="px-3 py-2 border tex-center" colspan="2"></td>
                                            <td class="px-3 py-2 border text-right">
                                                {{ number_format($saldoBerjalan, 2, ',', '.') }}
                                            </td>
                                        </tr>

                                        {{-- Transaksi --}}
                                        @php
                                            $saldoBerjalan = $startingBalances[$kodeAkun] ?? 0;
                                            $tipeAkun = strtolower($akunPertama->chartOfAccount->tipe_akun ?? '');

                                        @endphp

                                        @foreach ($akunRows as $row)
                                            @php
                                                $debit = $row->debits;
                                                $kredit = $row->credits;

                                                // Rumus saldo berjalan berdasarkan tipe akun
                                                if (in_array($tipeAkun, ['aset', 'beban'])) {
                                                    $saldoBerjalan += $debit - $kredit;
                                                } else {
                                                    $saldoBerjalan += $kredit - $debit;
                                                }
                                            @endphp
                                            <tr>
                                                <td class="px-3 py-2 border">{{ optional($row->journalEntry)->tanggal }}
                                                </td>
                                                <td class="px-3 py-2 border">
                                                    @if ($showComment == 'transaction_comment')
                                                        {{ optional($row->journalEntry)->comment ?? '-' }}
                                                    @else
                                                        {{ $row->comment ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 border">
                                                    {{ optional($row->journalEntry)->source ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 border text-right">
                                                    {{ number_format($debit, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 border text-right">
                                                    {{ number_format($kredit, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 border text-right">
                                                    {{ number_format($saldoBerjalan, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach


                                        <tr class="bg-gray-100 font-semibold">
                                            <td colspan="3" class="px-3 py-2 text-right">Total</td>
                                            <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 2, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-2 text-right">{{ number_format($totalKredit, 2, ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Tidak ada data untuk ditampilkan.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

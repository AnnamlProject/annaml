<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Departemen;
use App\Departement;
use App\JournalEntryDetail;
use Illuminate\Http\Request;

class IncomeStatementController extends Controller
{
    //
    public function incomeStatementFilter()
    {
        return view('income_statement.filter_income_statement');
    }


    public function incomeStatementReport(Request $request)
    {
        $tanggalAwal  = $request->start_date;
        $tanggalAkhir = $request->end_date;

        // Ambil semua akun Pendapatan & Beban, urut kode_akun
        $accounts = ChartOfAccount::whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun')
            ->get();

        $groups = []; // array kumpulan grup {group, tipe, akun[], saldo_group}
        $currentGroup = null;

        foreach ($accounts as $account) {
            // Mulai grup baru saat ketemu level GROUP ACCOUNT
            if ($account->level_akun === 'GROUP ACCOUNT') {
                // Push grup sebelumnya
                if ($currentGroup && !empty($currentGroup['akun'])) {
                    $currentGroup['saldo_group'] = array_sum(array_column($currentGroup['akun'], 'saldo'));
                    $groups[] = $currentGroup;
                }

                $currentGroup = [
                    'group' => $account->nama_akun,
                    'tipe'  => strtolower($account->tipe_akun), // 'pendapatan' atau 'beban'
                    'akun'  => [],
                    'saldo_group' => 0,
                ];
                continue;
            }

            // Lewati HEADER
            if ($account->level_akun === 'HEADER') {
                continue;
            }

            // Jika belum ada grup tapi ada akun, masukkan ke "Tanpa Grup"
            if (!$currentGroup) {
                $currentGroup = [
                    'group' => 'Tanpa Grup',
                    'tipe'  => strtolower($account->tipe_akun),
                    'akun'  => [],
                    'saldo_group' => 0,
                ];
            }

            // Hitung saldo akun pada periode
            $entries = JournalEntryDetail::with('journalEntry')
                ->where('kode_akun', $account->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            $totalDebit  = $entries->sum('debits');
            $totalKredit = $entries->sum('credits');

            $tipe  = strtolower($account->tipe_akun);
            $saldo = ($tipe === 'pendapatan')
                ? ($totalKredit - $totalDebit)   // Pendapatan: normal kredit
                : ($totalDebit  - $totalKredit); // Beban:    normal debit

            if ($saldo != 0) {
                $currentGroup['akun'][] = [
                    'kode_akun'  => $account->kode_akun,
                    'nama_akun'  => $account->nama_akun,
                    'tipe_akun'  => $account->tipe_akun,
                    'level_akun' => $account->level_akun,
                    'saldo'      => $saldo,
                ];
            }
        }

        // Push grup terakhir
        if ($currentGroup && !empty($currentGroup['akun'])) {
            $currentGroup['saldo_group'] = array_sum(array_column($currentGroup['akun'], 'saldo'));
            $groups[] = $currentGroup;
        }

        // Pisahkan menjadi bagian Pendapatan vs Beban
        $groupsPendapatan = array_values(array_filter($groups, fn($g) => $g['tipe'] === 'pendapatan'));
        $groupsBeban      = array_values(array_filter($groups, fn($g) => $g['tipe'] === 'beban'));

        // Hitung total global per tipe (BUKAN per grup)
        $totalPendapatan = array_sum(array_column($groupsPendapatan, 'saldo_group'));
        $totalBeban      = array_sum(array_column($groupsBeban, 'saldo_group'));

        // Laba sebelum pajak
        $labaSebelumPajak = $totalPendapatan - $totalBeban;

        // Beban Pajak Penghasilan: akun bernama persis "Pajak Penghasilan Badan"
        $akunPajak  = ChartOfAccount::where('is_income_tax', 1)->first();
        $bebanPajak = 0;

        if ($akunPajak) {
            $entriesPajak = JournalEntryDetail::with('journalEntry')
                ->where('kode_akun', $akunPajak->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            // Pajak adalah beban → normal debit
            $bebanPajak = $entriesPajak->sum('debits') - $entriesPajak->sum('credits');
        }

        $labaSetelahPajak = $labaSebelumPajak - $bebanPajak;

        return view('income_statement.income_statement_report', [
            'groupsPendapatan' => $groupsPendapatan,
            'groupsBeban'      => $groupsBeban,
            'totalPendapatan'  => $totalPendapatan,
            'totalBeban'       => $totalBeban,
            'labaSebelumPajak' => $labaSebelumPajak,
            'bebanPajak'       => $bebanPajak,
            'labaSetelahPajak' => $labaSetelahPajak,
            'start_date'       => $tanggalAwal,
            'end_date'         => $tanggalAkhir,
        ]);
    }

    public function incomeStatementFilterDepartement()
    {
        $departemens = Departement::all();
        $account = chartOfAccount::all();

        return view('income_statement.filter_income_statement_departement', compact('departemens', 'account'));
    }
    public function incomeStatementDepartement(Request $request)
    {
        // Ambil semua departemen induk
        $departemens = Departement::select('id', 'deskripsi')->get();

        $tanggalAwal = $request->start_date;
        $tanggalAkhir = $request->end_date;
        $selectedAccounts = $request->selected_accounts;

        // Ambil departemen terpilih dari request (kalau ada)
        $selectedDepartemens = $request->selected_departemens ? explode(',', $request->selected_departemens) : [];

        if (!empty($selectedDepartemens)) {
            // Filter hanya departemen yang dipilih
            $departemens = $departemens->whereIn('deskripsi', $selectedDepartemens);
        }

        // Proses akun terpilih
        $kodeAkunTerpilih = [];
        if (!empty($selectedAccounts)) {
            $kodeAkunTerpilih = explode(',', $selectedAccounts);
            $kodeAkunTerpilih = array_map(function ($akun) {
                return trim(explode(' - ', $akun)[0]);
            }, $kodeAkunTerpilih);
        }

        // Ambil akun-akun pendapatan dan beban
        $accountsQuery = ChartOfAccount::whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun');

        if (!empty($kodeAkunTerpilih)) {
            $accountsQuery->whereIn('kode_akun', $kodeAkunTerpilih);
        }

        $accounts = $accountsQuery->get();

        $incomeStatement = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($accounts as $account) {
            // Ambil semua jurnal detail untuk akun & periode ini
            $entries = JournalEntryDetail::where('kode_akun', $account->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            $totalDebit = $entries->sum('debits');
            $totalKredit = $entries->sum('credits');

            // Hitung saldo utama akun
            $saldoUtama = 0;
            if (strtolower($account->tipe_akun) === 'pendapatan') {
                $saldoUtama = $totalKredit - $totalDebit;
                $totalPendapatan += $saldoUtama;
            } else {
                $saldoUtama = $totalDebit - $totalKredit;
                $totalBeban += $saldoUtama;
            }

            // Hitung per departemen induk
            $perDepartemen = [];
            foreach ($departemens as $departemen) {
                $departemenEntries = JournalEntryDetail::where('kode_akun', $account->kode_akun)
                    ->whereHas('departemenAkun', function ($q) use ($departemen) {
                        // relasi departemenAkun -> departemen
                        $q->where('departemen_id', $departemen->id);
                    })
                    ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                        $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                    })
                    ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_kredit')
                    ->first();

                $nilai = 0;
                if (strtolower($account->tipe_akun) === 'pendapatan') {
                    $nilai = ($departemenEntries->total_kredit ?? 0) - ($departemenEntries->total_debit ?? 0);
                } else {
                    $nilai = ($departemenEntries->total_debit ?? 0) - ($departemenEntries->total_kredit ?? 0);
                }

                $perDepartemen[$departemen->deskripsi] = $nilai;
            }

            if ($saldoUtama != 0) {
                $incomeStatement[] = [
                    'kode_akun'     => $account->kode_akun,
                    'nama_akun'     => $account->nama_akun,
                    'tipe_akun'     => $account->tipe_akun,
                    'saldo'         => $saldoUtama,
                    'per_departemen' => $perDepartemen,
                ];
            }
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        return view('income_statement.income_statement_departement', [
            'incomeStatement' => $incomeStatement,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban'      => $totalBeban,
            'labaBersih'      => $labaBersih,
            'start_date'      => $tanggalAwal,
            'end_date'        => $tanggalAkhir,
            'departemens'     => $departemens->pluck('deskripsi'),
        ]);
    }
}

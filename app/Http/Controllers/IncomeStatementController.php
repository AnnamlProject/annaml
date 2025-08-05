<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
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
        $tanggalAwal = $request->start_date;
        $tanggalAkhir = $request->end_date;

        // Ambil akun bertipe Pendapatan dan Beban, urutkan berdasarkan kode
        $accounts = chartOfAccount::whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun')
            ->get();

        $incomeStatement = [];
        $groupName = null;
        $groupList = [];

        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($accounts as $account) {
            // Jika akun level-nya adalah GROUP ACCOUNT, berarti ini awal grup baru
            if ($account->level_akun === 'GROUP ACCOUNT') {
                // Simpan grup sebelumnya jika ada isinya
                if ($groupName && !empty($groupList)) {
                    $groupSaldo = array_sum(array_column($groupList, 'saldo'));
                    $incomeStatement[] = [
                        'group' => $groupName,
                        'saldo_group' => $groupSaldo,
                        'akun' => $groupList
                    ];
                }

                // Inisialisasi grup baru
                $groupName = $account->nama_akun;
                $groupList = [];
                continue;
            }

            // Lewati akun yang levelnya HEADER
            if ($account->level_akun === 'HEADER') continue;

            // Ambil detail jurnal untuk akun ini dalam rentang tanggal
            $entries = JournalEntryDetail::with('journalEntry')
                ->where('kode_akun', $account->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            $totalDebit = $entries->sum('debits');
            $totalKredit = $entries->sum('credits');

            $tipe = strtolower($account->tipe_akun);
            $saldo = 0;

            if ($tipe === 'pendapatan') {
                $saldo = $totalKredit - $totalDebit;
                $totalPendapatan += $saldo;
            } else { // beban
                $saldo = $totalDebit - $totalKredit;
                $totalBeban += $saldo;
            }

            if ($saldo != 0) {
                $groupList[] = [
                    'kode_akun' => $account->kode_akun,
                    'nama_akun' => $account->nama_akun,
                    'tipe_akun' => $account->tipe_akun,
                    'level_akun' => $account->level_akun,
                    'saldo' => $saldo,
                ];
            }
        }

        // Simpan grup terakhir
        if ($groupName && !empty($groupList)) {
            $groupSaldo = array_sum(array_column($groupList, 'saldo'));
            $incomeStatement[] = [
                'group' => $groupName,
                'saldo_group' => $groupSaldo,
                'akun' => $groupList
            ];
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        return view('income_statement.income_statement_report', [
            'incomeStatement' => $incomeStatement,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,
            'start_date' => $tanggalAwal,
            'end_date' => $tanggalAkhir,
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
        // Ambil data departemen (ID dan nama)
        $departemens = Departement::select('id', 'deskripsi')->get();

        $tanggalAwal = $request->start_date;
        $tanggalAkhir = $request->end_date;
        $selectedAccounts = $request->selected_accounts;

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
            // Ambil semua jurnal detail untuk akun dan periode ini
            $entries = JournalEntryDetail::where('kode_akun', $account->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            $totalDebit = $entries->sum('debits');
            $totalKredit = $entries->sum('credits');

            // Hitung saldo utama
            $saldoUtama = 0;
            if (strtolower($account->tipe_akun) === 'pendapatan') {
                $saldoUtama = $totalKredit - $totalDebit;
                $totalPendapatan += $saldoUtama;
            } else {
                $saldoUtama = $totalDebit - $totalKredit;
                $totalBeban += $saldoUtama;
            }

            // Hitung per departemen
            $perDepartemen = [];
            foreach ($departemens as $departemen) {
                $departemenEntries = JournalEntryDetail::where('kode_akun', $account->kode_akun)
                    ->where('departemen_akun_id', $departemen->id)
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
                    'kode_akun' => $account->kode_akun,
                    'nama_akun' => $account->nama_akun,
                    'tipe_akun' => $account->tipe_akun,
                    'saldo' => $saldoUtama,
                    'per_departemen' => $perDepartemen,
                ];
            }
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        return view('income_statement.income_statement_departement', [
            'incomeStatement' => $incomeStatement,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,
            'start_date' => $tanggalAwal,
            'end_date' => $tanggalAkhir,
            'departemens' => $departemens->pluck('deskripsi'),
        ]);
    }
}

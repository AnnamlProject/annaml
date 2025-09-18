<?php

namespace App\Exports;

use App\ChartOfAccount;
use App\JournalEntryDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class IncomeStatementExport implements FromView
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
        $this->tanggalAwal  = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function view(): View
    {
        // 🔹 Ambil data sekali saja
        $entries = JournalEntryDetail::select(
            'journal_entry_details.kode_akun',
            DB::raw('SUM(journal_entry_details.debits) as total_debit'),
            DB::raw('SUM(journal_entry_details.credits) as total_kredit')
        )
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->whereBetween('journal_entries.tanggal', [$this->tanggalAwal, $this->tanggalAkhir])
            ->groupBy('journal_entry_details.kode_akun')
            ->get()
            ->keyBy('kode_akun');

        // 🔹 Ambil akun pendapatan & beban (supaya tetap urut)
        $accounts = ChartOfAccount::whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun')
            ->get();

        $groups = [];
        $currentGroup = null;

        foreach ($accounts as $account) {
            if ($account->level_akun === 'GROUP ACCOUNT') {
                if ($currentGroup && !empty($currentGroup['akun'])) {
                    $currentGroup['saldo_group'] = array_sum(array_column($currentGroup['akun'], 'saldo'));
                    $groups[] = $currentGroup;
                }

                $currentGroup = [
                    'group'       => $account->nama_akun,
                    'tipe'        => strtolower($account->tipe_akun),
                    'akun'        => [],
                    'saldo_group' => 0,
                ];
                continue;
            }

            if ($account->level_akun === 'HEADER') {
                continue;
            }

            if (!$currentGroup) {
                $currentGroup = [
                    'group'       => 'Tanpa Grup',
                    'tipe'        => strtolower($account->tipe_akun),
                    'akun'        => [],
                    'saldo_group' => 0,
                ];
            }

            // 🔹 Ambil saldo dari hasil query (bukan query ulang)
            $entry = $entries->get($account->kode_akun);
            $totalDebit  = $entry->total_debit ?? 0;
            $totalKredit = $entry->total_kredit ?? 0;

            $tipe  = strtolower($account->tipe_akun);
            if (strtolower($account->tipe_akun) === 'pendapatan') {
                $saldo = $totalKredit;   // pendapatan → total kredit
            } else {
                $saldo = $totalDebit;    // beban → total debit
            }

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

        if ($currentGroup && !empty($currentGroup['akun'])) {
            $currentGroup['saldo_group'] = array_sum(array_column($currentGroup['akun'], 'saldo'));
            $groups[] = $currentGroup;
        }

        // 🔹 Pisahkan pendapatan & beban
        $groupsPendapatan = array_values(array_filter($groups, fn($g) => $g['tipe'] === 'pendapatan'));
        $groupsBeban      = array_values(array_filter($groups, fn($g) => $g['tipe'] === 'beban'));

        $totalPendapatan = array_sum(array_column($groupsPendapatan, 'saldo_group'));
        $totalBeban      = array_sum(array_column($groupsBeban, 'saldo_group'));

        $labaSebelumPajak = $totalPendapatan - $totalBeban;

        // 🔹 Pajak (opsional: flag is_income_tax di chart_of_accounts)
        $akunPajak  = ChartOfAccount::where('is_income_tax', 1)->first();
        $bebanPajak = 0;

        if ($akunPajak) {
            $entryPajak = $entries->get($akunPajak->kode_akun);
            if ($entryPajak) {
                $bebanPajak = ($entryPajak->total_debit ?? 0) - ($entryPajak->total_kredit ?? 0);
            }
        }

        $labaSetelahPajak = $labaSebelumPajak - $bebanPajak;

        return view('income_statement.excel', [
            'groupsPendapatan' => $groupsPendapatan,
            'groupsBeban'      => $groupsBeban,
            'totalPendapatan'  => $totalPendapatan,
            'totalBeban'       => $totalBeban,
            'labaSebelumPajak' => $labaSebelumPajak,
            'bebanPajak'       => $bebanPajak,
            'labaSetelahPajak' => $labaSetelahPajak,
            'start_date'       => $this->tanggalAwal,
            'end_date'         => $this->tanggalAkhir,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\JournalEntry;
use App\JournalEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuBesarController extends Controller
{
    //

    public function bukuBesarFilter(Request $request)
    {
        $account = ChartOfAccount::all();

        return view('buku_besar.filter_buku_besar', compact('account'));
    }
    public function bukuBesarReport(Request $request)
    {
        $accounts = ChartOfAccount::all();

        // Ambil kode akun dari checkbox input (dalam format: "kode - nama")
        $selectedAccountsRaw = explode(',', $request->selected_accounts ?? '');
        $selectedAccountCodes = [];

        foreach ($selectedAccountsRaw as $item) {
            $parts = explode(' - ', $item);
            if (count($parts) > 0) {
                $selectedAccountCodes[] = trim($parts[0]);
            }
        }

        // ======================
        // 1. Query utama (periode) dengan PAGINATION
        // ======================
        $rows = JournalEntryDetail::select(
            'journal_entry_details.id',
            'journal_entry_details.kode_akun',
            'journal_entry_details.debits',
            'journal_entry_details.credits',
            'journal_entry_details.comment',
            'journal_entry_details.journal_entry_id',
            'journal_entries.tanggal'
        )
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->with('chartOfAccount')
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('journal_entries.tanggal', [$request->start_date, $request->end_date]);
            })
            ->when(!empty($selectedAccountCodes), function ($q) use ($selectedAccountCodes) {
                $q->whereIn('journal_entry_details.kode_akun', $selectedAccountCodes);
            })
            ->orderBy('journal_entries.tanggal', 'asc')
            ->paginate(100); // <=== ini pagination

        // ======================
        // 2. Query saldo awal (sebelum start_date)
        // ======================
        $saldoAwalPerAkun = [];

        if ($request->filled('start_date')) {
            $saldoAwal = JournalEntryDetail::select(
                'journal_entry_details.kode_akun',
                DB::raw('SUM(journal_entry_details.debits) as total_debit'),
                DB::raw('SUM(journal_entry_details.credits) as total_kredit')
            )
                ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entries.tanggal', '<', $request->start_date)
                ->when(!empty($selectedAccountCodes), function ($q) use ($selectedAccountCodes) {
                    $q->whereIn('journal_entry_details.kode_akun', $selectedAccountCodes);
                })
                ->groupBy('journal_entry_details.kode_akun')
                ->get()
                ->keyBy('kode_akun');

            foreach ($saldoAwal as $kodeAkun => $saldo) {
                $akun = $accounts->firstWhere('kode_akun', $kodeAkun);
                if (!$akun) continue;

                $tipe = strtolower($akun->tipe_akun);
                if (in_array($tipe, ['kewajiban', 'ekuitas', 'pendapatan'])) {
                    $saldoAwalPerAkun[$kodeAkun] = ($saldo->total_kredit ?? 0) - ($saldo->total_debit ?? 0);
                } else {
                    $saldoAwalPerAkun[$kodeAkun] = ($saldo->total_debit ?? 0) - ($saldo->total_kredit ?? 0);
                }
            }
        }

        // ======================
        // 3. Grouping by account
        // ======================
        $groupedByAccount = $rows->getCollection()->groupBy(function ($item) {
            return $item->chartOfAccount->nama_akun ?? 'Tanpa Akun';
        });

        // ======================
        // 4. Hitung total per tipe akun
        // ======================
        $totalByType = [
            'pendapatan' => 0,
            'kewajiban'  => 0,
            'ekuitas'    => 0,
        ];

        foreach ($rows as $row) {
            $tipe = strtolower($row->chartOfAccount->tipe_akun ?? '');
            $debit = $row->debits;
            $kredit = $row->credits;

            if (in_array($tipe, ['pendapatan', 'kewajiban', 'ekuitas'])) {
                $totalByType[$tipe] += $kredit - $debit;
            }
        }

        return view('buku_besar.buku_besar_report', [
            'accounts'          => $accounts,
            'details'           => $rows,
            'rows'              => $rows,
            'showComment'       => $request->show_comment ?? 'transaction_comment',
            'groupedByAccount'  => $groupedByAccount,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'startingBalances'  => $saldoAwalPerAkun,
            'totalByType'       => $totalByType,
        ]);
    }
}

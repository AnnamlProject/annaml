<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\JournalEntry;
use App\JournalEntryDetail;
use Illuminate\Http\Request;

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

        // Ambil semua detail jurnal yang relevan
        $query = JournalEntryDetail::with(['journalEntry', 'chartOfAccount']);

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            });
        }

        // Ambil kode akun dari checkbox input (dalam format: "kode - nama")
        $selectedAccountsRaw = explode(',', $request->selected_accounts ?? '');
        $selectedAccountCodes = [];

        foreach ($selectedAccountsRaw as $item) {
            $parts = explode(' - ', $item);
            if (count($parts) > 0) {
                $selectedAccountCodes[] = trim($parts[0]);
            }
        }

        // Jika ada akun yang dipilih, filter berdasarkan itu
        if (!empty($selectedAccountCodes)) {
            $query->whereIn('kode_akun', $selectedAccountCodes);
        }

        // Join ke journal_entries untuk akses tanggal
        $query->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.tanggal', 'asc')
            ->select('journal_entry_details.*');

        $rows = $query->get();

        // Group berdasarkan nama akun
        $groupedByAccount = $rows->groupBy(function ($item) {
            return $item->chartOfAccount->nama_akun ?? 'Tanpa Akun';
        });

        // Hitung saldo awal untuk tiap akun
        $saldoAwalPerAkun = [];

        foreach ($selectedAccountCodes as $kodeAkun) {
            $akun = ChartOfAccount::where('kode_akun', $kodeAkun)->first();
            if (!$akun) {
                logger("Akun tidak ditemukan untuk kode: " . $kodeAkun);
                continue;
            }

            $tipe = strtolower($akun->tipe_akun); // Misal: aset, kewajiban, beban, dll
            // echo $akun->tipe_akun;
            // echo $kodeAkun;

            // Ambil transaksi sebelum start_date
            $saldoAwal = JournalEntryDetail::with('journalEntry')
                ->where('kode_akun', $kodeAkun)
                ->whereHas('journalEntry', function ($q) use ($request) {
                    $q->where('tanggal', '<', $request->start_date);
                })
                ->get();

            $totalDebit = $saldoAwal->sum('debits');
            $totalKredit = $saldoAwal->sum('credits');

            // Penentuan saldo awal berdasarkan tipe akun
            if (in_array($tipe, ['kewajiban', 'ekuitas', 'pendapatan'])) {
                $saldo = $totalKredit - $totalDebit; // Normalnya kredit
            } else {
                $saldo = $totalDebit - $totalKredit; // Normalnya debit
            }

            $saldoAwalPerAkun[$kodeAkun] = $saldo;
        }

        return view('buku_besar.buku_besar_report', [
            'accounts' => $accounts,
            'details' => $rows,
            'rows' => $rows,
            'showComment' => $request->show_comment ?? 'transaction_comment',
            'groupedByAccount' => $groupedByAccount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'startingBalances' => $saldoAwalPerAkun,
        ]);
    }
}

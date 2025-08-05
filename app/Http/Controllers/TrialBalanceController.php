<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\JournalEntryDetail;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    //
    public function trialBalanceFilter()
    {
        return view('trial_balance.filter_trial_balance');
    }
    public function trialBalanceReport(Request $request)
    {

        $tanggalAkhir = $request->end_date;

        // Ambil semua akun dari chart of accounts
        $accounts = chartOfAccount::all();

        $trialBalances = [];

        foreach ($accounts as $account) {
            // Ambil semua jurnal untuk akun ini sampai tanggal akhir
            $entries = JournalEntryDetail::with('journalEntry')
                ->where('kode_akun', $account->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    $q->where('tanggal', '<=', $tanggalAkhir);
                })
                ->get();

            $totalDebit = $entries->sum('debits');
            $totalKredit = $entries->sum('credits');

            // Tentukan tipe akun (debit/kredit normal)
            $tipe = strtolower($account->tipe_akun);

            if (in_array($tipe, ['kewajiban', 'ekuitas', 'pendapatan'])) {
                $saldo = $totalKredit - $totalDebit; // Saldo normal kredit
            } else {
                $saldo = $totalDebit - $totalKredit; // Saldo normal debit
            }

            // Masukkan ke dalam array hasil jika saldonya tidak 0
            if ($saldo != 0) {
                $trialBalances[] = [
                    'kode_akun' => $account->kode_akun,
                    'nama_akun' => $account->nama_akun,
                    'tipe_akun' => $account->tipe_akun,
                    'saldo_debit' => $saldo > 0 && !in_array($tipe, ['kewajiban', 'ekuitas', 'pendapatan']) ? $saldo : 0,
                    'saldo_kredit' => $saldo > 0 && in_array($tipe, ['kewajiban', 'ekuitas', 'pendapatan']) ? $saldo : ($saldo < 0 ? abs($saldo) : 0),
                ];
            }
        }

        return view('trial_balance.trial_balance_report', [
            'trialBalances' => $trialBalances,
            'end_date' => $tanggalAkhir,
        ]);
    }
}

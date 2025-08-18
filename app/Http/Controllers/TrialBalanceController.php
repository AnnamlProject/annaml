<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
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

            $tipe = strtolower($account->tipe_akun);

            $saldo_debit = 0;
            $saldo_kredit = 0;

            if (in_array($tipe, ['kewajiban', 'Ekuitas', 'Pendapatan'])) {
                // saldo normal kredit
                $saldo = $totalKredit - $totalDebit;
                if ($saldo > 0) {
                    $saldo_kredit = $saldo;
                } else {
                    $saldo_debit = abs($saldo);
                }
            } else {
                // saldo normal debit
                $saldo = $totalDebit - $totalKredit;
                if ($saldo > 0) {
                    $saldo_debit = $saldo;
                } else {
                    $saldo_kredit = abs($saldo);
                }
            }

            // Masukkan ke hasil jika saldo ada (debit/kredit)
            if ($saldo_debit != 0 || $saldo_kredit != 0) {
                $trialBalances[] = [
                    'kode_akun' => $account->kode_akun,
                    'nama_akun' => $account->nama_akun,
                    'tipe_akun' => $account->tipe_akun,
                    'saldo_debit' => $saldo_debit,
                    'saldo_kredit' => $saldo_kredit,
                ];
            }
        }

        return view('trial_balance.trial_balance_report', [
            'trialBalances' => $trialBalances,
            'end_date' => $tanggalAkhir,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NeracaController extends Controller
{
    //
    public function neracaFilter()
    {
        return view('neraca.filter_neraca');
    }
    public function neracaReport(Request $request)
    {
        $tanggalAkhir = $request->end_date;
        $siteTitle = \App\Setting::where('key', 'site_title')->value('value');

        // Ambil semua akun COA yang masuk ke neraca
        $accounts = \App\ChartOfAccount::whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas'])
            ->get();

        $neraca = [];

        foreach ($accounts as $akun) {
            // Hitung saldo dari jurnal detail
            $saldo = \App\JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $totalDebit = $saldo->total_debit ?? 0;
            $totalCredit = $saldo->total_credit ?? 0;

            // Saldo normal tergantung tipe akun
            if ($akun->tipe_akun === 'Aset') {
                $endingBalance = $totalDebit - $totalCredit;
            } else { // Kewajiban & Ekuitas
                $endingBalance = $totalCredit - $totalDebit;
            }

            $neraca[$akun->tipe_akun][] = [
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'saldo'     => $endingBalance,
            ];
        }

        return view('neraca.neraca_report', [
            'neraca' => $neraca,
            'tanggalAkhir' => $tanggalAkhir,
            'siteTitle' => $siteTitle,
        ]);
    }
}

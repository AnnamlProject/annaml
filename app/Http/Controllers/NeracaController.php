<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\JournalEntryDetail;
use App\Setting;
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
        $siteTitle = Setting::where('key', 'site_title')->value('value');
        $showAccountNumber = $request->has('show_account_number'); // ⬅️ tangkap checkbox

        $accounts = ChartOfAccount::whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas'])
            ->where('level_akun', '!=', 'X')
            ->get();

        $neraca = [];

        foreach ($accounts as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $totalDebit = $saldo->total_debit ?? 0;
            $totalCredit = $saldo->total_credit ?? 0;

            if ($akun->tipe_akun === 'Aset') {
                $endingBalance = $totalDebit - $totalCredit;
            } else {
                $endingBalance = $totalCredit - $totalDebit;
            }

            $neraca[$akun->tipe_akun][] = [
                'kode_akun'  => $akun->kode_akun,
                'nama_akun'  => $akun->nama_akun,
                'level_akun' => $akun->level_akun,
                'saldo'      => $endingBalance,
            ];
        }

        $grandTotalAset = collect($neraca['Aset'] ?? [])->sum('saldo');
        $grandTotalKewajiban = collect($neraca['Kewajiban'] ?? [])->sum('saldo');
        $grandTotalEkuitas = collect($neraca['Ekuitas'] ?? [])->sum('saldo');

        // hitung laba tahun berjalan
        $akunPendapatan = ChartOfAccount::where('tipe_akun', 'Pendapatan')->get();
        $akunBeban = ChartOfAccount::where('tipe_akun', 'Beban')
            ->where('is_income_tax', '!=', 1)
            ->get();
        $akunPajak = ChartOfAccount::where('is_income_tax', 1)->get();

        $totalPendapatan = 0;
        foreach ($akunPendapatan as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $totalPendapatan += ($saldo->total_credit ?? 0) - ($saldo->total_debit ?? 0);
        }

        $totalBeban = 0;
        foreach ($akunBeban as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $totalBeban += ($saldo->total_debit ?? 0) - ($saldo->total_credit ?? 0);
        }

        $totalPajak = 0;
        foreach ($akunPajak as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $totalPajak += ($saldo->total_debit ?? 0) - ($saldo->total_credit ?? 0);
        }

        $labaSebelumPajak = $totalPendapatan - $totalBeban;
        $labaTahunBerjalan = $labaSebelumPajak - $totalPajak;

        $akunLaba = ChartOfAccount::where('level_akun', 'X')->first();
        if ($akunLaba) {
            $neraca['Ekuitas'][] = [
                'kode_akun'  => $akunLaba->kode_akun,
                'nama_akun'  => $akunLaba->nama_akun,
                'level_akun' => $akunLaba->level_akun,
                'saldo'      => $labaTahunBerjalan,
            ];
            $grandTotalEkuitas += $labaTahunBerjalan;
        }

        return view('neraca.neraca_report', [
            'neraca' => $neraca,
            'tanggalAkhir' => $tanggalAkhir,
            'siteTitle' => $siteTitle,
            'grandTotalAset' => $grandTotalAset,
            'grandTotalKewajiban' => $grandTotalKewajiban,
            'grandTotalEkuitas' => $grandTotalEkuitas,
            'labaSebelumPajak' => $labaSebelumPajak,
            'totalPajak' => $totalPajak,
            'labaTahunBerjalan' => $labaTahunBerjalan,
            'showAccountNumber' => $showAccountNumber, // ⬅️ kirim ke view
        ]);
    }
}

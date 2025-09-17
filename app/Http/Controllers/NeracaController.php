<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Exports\NeracaExport;
use App\JournalEntryDetail;
use App\Setting;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $siteLogo  = Setting::where('key', 'logo')->value('value');

        $showAccountNumber   = $request->has('show_account_number');   // checkbox show account
        $hideAccountWithZero = $request->has('hide_account_with_zero'); // checkbox hide zero balance

        // Ambil akun utama (Aset, Kewajiban, Ekuitas, kecuali level X)
        $accounts = ChartOfAccount::whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas'])
            ->where('level_akun', '!=', 'X')
            ->orderBy('kode_akun')
            ->get();

        $neraca = [];

        foreach ($accounts as $akun) {
            $endingBalance = $this->getSaldo($akun, $tanggalAkhir);

            // Skip saldo nol kalau dicentang
            if ($hideAccountWithZero && $endingBalance == 0) {
                continue;
            }

            $neraca[$akun->tipe_akun][] = [
                'kode_akun'  => $akun->kode_akun,
                'nama_akun'  => $akun->nama_akun,
                'level_akun' => $akun->level_akun,
                'saldo'      => $endingBalance,
            ];
        }

        // Hitung grand total
        $grandTotalAset      = collect($neraca['Aset'] ?? [])->sum('saldo');
        $grandTotalKewajiban = collect($neraca['Kewajiban'] ?? [])->sum('saldo');
        $grandTotalEkuitas   = collect($neraca['Ekuitas'] ?? [])->sum('saldo');

        // 🔹 Hitung laba tahun berjalan
        $totalPendapatan = $this->getTotalByTipe('Pendapatan', $tanggalAkhir, 'credit');
        $totalBeban      = $this->getTotalByTipe('Beban', $tanggalAkhir, 'debit', false);
        $totalPajak      = $this->getTotalByPajak($tanggalAkhir);

        $labaSebelumPajak  = $totalPendapatan - $totalBeban;
        $labaTahunBerjalan = $labaSebelumPajak - $totalPajak;

        // Tambahkan akun laba tahun berjalan
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

        return view('neraca.neraca_report', compact(
            'neraca',
            'tanggalAkhir',
            'siteTitle',
            'siteLogo',
            'grandTotalAset',
            'grandTotalKewajiban',
            'grandTotalEkuitas',
            'labaSebelumPajak',
            'totalPajak',
            'labaTahunBerjalan',
            'showAccountNumber',
            'hideAccountWithZero'
        ));
    }

    /**
     * Hitung saldo per akun
     */
    private function getSaldo($akun, $tanggalAkhir)
    {
        $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
            ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                if ($tanggalAkhir) {
                    $q->where('tanggal', '<=', $tanggalAkhir);
                }
            })
            ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
            ->first();

        $totalDebit  = $saldo->total_debit ?? 0;
        $totalCredit = $saldo->total_credit ?? 0;

        return $akun->tipe_akun === 'Aset'
            ? $totalDebit - $totalCredit   // normal debit
            : $totalCredit - $totalDebit;  // normal kredit
    }

    /**
     * Hitung total saldo per tipe akun
     */
    private function getTotalByTipe($tipe, $tanggalAkhir, $normal = 'credit', $excludePajak = true)
    {
        $query = ChartOfAccount::where('tipe_akun', $tipe);
        if ($excludePajak) {
            $query->where('is_income_tax', '!=', 1);
        }
        $akuns = $query->get();

        $total = 0;
        foreach ($akuns as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            if ($normal === 'credit') {
                $total += ($saldo->total_credit ?? 0) - ($saldo->total_debit ?? 0);
            } else {
                $total += ($saldo->total_debit ?? 0) - ($saldo->total_credit ?? 0);
            }
        }
        return $total;
    }

    /**
     * Hitung total pajak (is_income_tax = 1)
     */
    private function getTotalByPajak($tanggalAkhir)
    {
        $akuns = ChartOfAccount::where('is_income_tax', 1)->get();

        $total = 0;
        foreach ($akuns as $akun) {
            $saldo = JournalEntryDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('journalEntry', function ($q) use ($tanggalAkhir) {
                    if ($tanggalAkhir) {
                        $q->where('tanggal', '<=', $tanggalAkhir);
                    }
                })
                ->selectRaw('SUM(debits) as total_debit, SUM(credits) as total_credit')
                ->first();

            $total += ($saldo->total_debit ?? 0) - ($saldo->total_credit ?? 0);
        }
        return $total;
    }


    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $tanggalAkhir = $request->get('end_date');

        if ($format === 'excel') {
            return Excel::download(new NeracaExport($tanggalAkhir), 'neraca.xlsx');
        } elseif ($format === 'pdf') {
            $pdf = PDF::loadView('neraca.export_pdf', [
                // data neraca sama dengan view biasa
            ]);
            return $pdf->download('neraca.pdf');
        }

        return back()->with('error', 'Format export tidak dikenali.');
    }
}

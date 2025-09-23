<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Exports\BukuBesarExport;
use App\JournalEntryDetail;
use App\StartNewYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuBesarController extends Controller
{
    public function bukuBesarFilter(Request $request)
    {
        $account = ChartOfAccount::all();
        $tahun_buku = StartNewYear::all();
        return view('buku_besar.filter_buku_besar', compact('account', 'tahun_buku'));
    }

    private function getBukuBesarData(Request $request)
    {
        $accounts = ChartOfAccount::all();
        $tahun_buku = StartNewYear::all();

        // Ambil kode akun dari input
        $selectedAccountsRaw = explode(',', $request->selected_accounts ?? '');
        $selectedAccountCodes = [];
        foreach ($selectedAccountsRaw as $item) {
            $parts = explode(' - ', $item);
            if (count($parts) > 0) {
                $selectedAccountCodes[] = trim($parts[0]);
            }
        }

        // 1. Query utama
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
                $q->whereDate('journal_entries.tanggal', '>=', $request->start_date)
                    ->whereDate('journal_entries.tanggal', '<=', $request->end_date);
            })
            ->when(!empty($selectedAccountCodes), function ($q) use ($selectedAccountCodes) {
                $q->whereIn('journal_entry_details.kode_akun', $selectedAccountCodes);
            })
            ->orderBy('journal_entry_details.kode_akun', 'asc')
            ->get();

        // 2. Hitung saldo awal
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

        // 3. Grouping by account
        $groupedByAccount = $rows->groupBy(function ($item) {
            return $item->chartOfAccount->nama_akun ?? 'Tanpa Akun';
        });

        // 4. Hitung total per tipe akun
        $totalByType = [
            'pendapatan' => 0,
            'kewajiban'  => 0,
            'ekuitas'    => 0,
        ];
        foreach ($rows as $row) {
            $tipe = strtolower($row->chartOfAccount->tipe_akun ?? '');
            if (in_array($tipe, ['pendapatan', 'kewajiban', 'ekuitas'])) {
                $totalByType[$tipe] += $row->credits - $row->debits;
            }
        }

        return [
            'accounts'         => $accounts,
            'rows'             => $rows,
            'groupedByAccount' => $groupedByAccount,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'saldoAwalPerAkun' => $saldoAwalPerAkun,
            'totalByType'      => $totalByType,
            'tahun_buku' => $tahun_buku,
            'showComment'      => $request->show_comment ?? 'transaction_comment', // ✅ tambahkan lagi
        ];
    }

    public function bukuBesarReport(Request $request)
    {
        return view('buku_besar.buku_besar_report', $this->getBukuBesarData($request));
    }
    public function export(Request $request)
    {
        $data = $this->getBukuBesarData($request);

        // Format tanggal untuk filename (misalnya 2024-12-31 → 31-12-2024)
        $start = \Carbon\Carbon::parse($data['start_date'])->format('d-m-Y');
        $end   = \Carbon\Carbon::parse($data['end_date'])->format('d-m-Y');

        $filename = "buku_besar_{$start}_sd_{$end}";

        if ($request->format === 'excel') {
            return Excel::download(
                new BukuBesarExport(
                    $data['rows'],
                    $data['saldoAwalPerAkun'],
                    $data['groupedByAccount'],
                    $data['totalByType'],
                    $data['start_date'],
                    $data['end_date']
                ),
                $filename . '.xlsx'
            );
        } elseif ($request->format === 'pdf') {
            $pdf = Pdf::loadView('buku_besar.export_pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        return back();
    }
}

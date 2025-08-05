<?php

namespace App\Http\Controllers;

use App\Imports\ChartOfAccountImport;
use App\Imports\CustomersImport;
use App\Imports\DepartemenImport;
use App\Imports\JournalEntryImport;
use App\Imports\KlasifikasiAkunImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    //
    public function importklasifikasiAkun(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new KlasifikasiAkunImport, $request->file('file'));

        return back()->with('success', 'Data klasifikasi akun berhasil diimpor.');
    }
    public function importchartOfAccount(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new ChartOfAccountImport, $request->file('file'));

        return back()->with('success', 'Import berhasil!');
    }
    public function importDepartemen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);
        Excel::import(new DepartemenImport, $request->file('file'));

        return back()->with('success', 'Data Departemen berhasil diimpor.');
    }
    public function importcustomers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);
        Excel::import(new CustomersImport, $request->file('file'));

        return back()->with('success', 'Data Customers berhasil diimpor.');
    }
    public function importjournal_entry(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $importer = new JournalEntryImport();
            Excel::import($importer, $request->file('file'));

            // Ambil hasil skipped
            $skipped = $importer->getSkippedGroups();

            if (count($skipped) > 0) {
                $pesan = 'Beberapa transaksi dilewati karena tidak balance: <br><ul>';
                foreach ($skipped as $group) {
                    $pesan .= "<li>$group</li>";
                }
                $pesan .= '</ul>';

                return back()->with('error', $pesan);
            }

            return back()->with('success', 'Semua data berhasil diimport dan seimbang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}

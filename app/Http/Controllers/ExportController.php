<?php

namespace App\Http\Controllers;

use App\Exports\ChartOfAccountExport;
use App\Exports\CustomersExport;
use App\Exports\DepartemenExport;
use App\Exports\ItemsExport;
use App\Exports\KlasifikasiAkunExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    //
    public function exportklasifikasiAkun()
    {
        return Excel::download(new KlasifikasiAkunExport, 'klasifikasiAkun.xlsx');
    }
    public function exportchartOfAccount()
    {
        return Excel::download(new ChartOfAccountExport, 'chartOfAccount.xlsx');
    }
    public function exportDepartemen()
    {
        return Excel::download(new DepartemenExport, 'departemen.xlsx');
    }
    public function exportcustomers()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }
    public function exportItems()
    {
        return Excel::download(new ItemsExport, 'items.xlsx');
    }
}

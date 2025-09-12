<?php

namespace App\Http\Controllers;

use App\Exports\ChartOfAccountExport;
use App\Exports\CustomersExport;
use App\Exports\DepartemenExport;
use App\Exports\EmployeeExport;
use App\Exports\IntangibleAssetExport;
use App\Exports\ItemsExport;
use App\Exports\KlasifikasiAkunExport;
use App\Exports\KomponenPenghasilanExport;
use App\Exports\ProjectExport;
use App\Exports\PtkpExport;
use App\Exports\TangibleAssetExport;
use App\Exports\TargetUnitExport;
use App\Exports\TaxRatesExport;
use App\Exports\TransaksiWahanaExport;
use App\Exports\VendorsExport;
use App\Exports\WahanaExport;
use App\TransaksiWahana;
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
    public function exportPtkp()
    {
        return Excel::download(new PtkpExport, 'ptkp.xlsx');
    }
    public function exportTaxRates()
    {
        return Excel::download(new TaxRatesExport, 'tax_rates.xlsx');
    }
    public function exportWahana()
    {
        return Excel::download(new WahanaExport, 'wahana.xlsx');
    }
    public function exportEmployee()
    {
        return Excel::download(new EmployeeExport, 'employee.xlsx');
    }
    public function exportTangibleAsset()
    {
        return Excel::download(new TangibleAssetExport, 'tangible_asset.xlsx');
    }
    public function exportIntangibleAsset()
    {
        return Excel::download(new IntangibleAssetExport, 'intangile_asset.xlsx');
    }
    public function exportProject()
    {
        return Excel::download(new ProjectExport, 'project.xlsx');
    }
    public function exportKomponenPenghasilan()
    {
        return Excel::download(new KomponenPenghasilanExport, 'komponen_penghasilan.xlsx');
    }
    public function exportTargetUnit()
    {
        return Excel::download(new TargetUnitExport, 'target_unit.xlsx');
    }
    public function exportTransaksiWahana()
    {
        return Excel::download(new TransaksiWahanaExport, 'transaksi_wahana.xlsx');
    }
}

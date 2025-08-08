<?php

use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenAkunController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IncomeStatementController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\KomposisiGajiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettingDepartementController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PembayaranGajiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::get('/home', 'HomeController@index')->name('home');
    // setup 
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');

    // user dan role
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController')->except('index', 'create', 'edit');
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
    Route::post('/roles-permissions', [RolePermissionController::class, 'update'])->name('roles.permissions.update');



    // setting company
    Route::resource('/taxpayers_company', 'TaxpayersCompanyController');

    Route::resource('/company_profile', 'CompanyProfileController');

    // general menu
    Route::resource('/numbering_account', 'NumberingAccountController');

    Route::resource('/klasifikasiAkun', 'KlasifikasiAkunController');
    // export dan import klasifikasi akun
    Route::get('/export/klasifikasiAkun', [ExportController::class, 'exportklasifikasiAkun'])->name('export.klasifikasiAkun');
    Route::post('/import/klasifikasiAkun', [ImportController::class, 'importklasifikasiAkun'])->name('import.klasifikasiAkun');

    Route::resource('/chartOfAccount', 'ChartOfAccountController');
    // export dan import chart of account
    Route::get('/export/charofaccount', [ExportController::class, 'exportchartOfAccount'])->name('export.chartOfAccount');
    Route::post('/import/chartofaccount', [ImportController::class, 'importchartOfAccount'])->name('import.chartOfAccount');

    // crud linked Accounts
    Route::resource('linkedAccount', 'linkedAccountController');

    Route::get('/admin/setting_departement', [SettingDepartementController::class, 'edit'])->name('setting_departement.edit');
    Route::put('/admin/setting_departement', [SettingDepartementController::class, 'update'])->name('setting_departement.update');

    Route::get('/departemen/assign-account', [DepartementController::class, 'assign'])->name('departemen.assign');
    Route::post('/departemen/assign-account', [DepartementController::class, 'storeAssign'])->name('departemen.assign.store');
    Route::resource('/departemen', 'DepartementController');
    // export dan import Departemen
    Route::get('/export/Departemen', [ExportController::class, 'exportDepartemen'])->name('export.Departemen');
    Route::post('/import/Departemen', [ImportController::class, 'importDepartemen'])->name('import.Departemen');

    // journal entry menu
    Route::get('view_journal_entry', [JournalEntryController::class, 'journalEntryShow'])->name('journal_entry.view_journal_entry');
    Route::get('view_journal_entry/result', [JournalEntryController::class, 'journalEntryView'])->name('journal_entry.view_journal_entry_result');
    // Tampilkan form kosong
    Route::get('filter_journal_entry', [JournalEntryController::class, 'showFilterForm'])->name('journal_entry.filter_journal_entry');
    // Tampilkan hasil filter
    Route::get('filter_journal_entry/result', [JournalEntryController::class, 'journalEntryFilter'])->name('journal_entry.filter_journal_entry_result');
    Route::resource('journal_entry', 'JournalEntryController');
    Route::post('/import/journal_entry', [ImportController::class, 'importjournal_entry'])->name('import.journal_entry');

    Route::get('/search-account', [DepartemenAkunController::class, 'search']);

    // general report
    Route::get('report_account', [ReportController::class, 'reportAccount'])->name('report.account');
    Route::get('report_klasifikasi', [ReportController::class, 'reportKlasifikasi'])->name('report.klasifikasi');
    Route::get('/report/departemen-akun', [ReportController::class, 'reportDepartemenAkun'])->name('report.departemen-akun');


    // report

    // buku besar
    Route::get('filter_buku_besar', [BukuBesarController::class, 'bukuBesarFilter'])->name('buku_besar.filter_buku_besar');
    Route::get('/laporan/buku-besar', [BukuBesarController::class, 'bukuBesarReport'])->name('buku_besar.buku_besar_report');

    // Trial Balance
    Route::get('filter_trial_balance', [TrialBalanceController::class, 'trialBalanceFilter'])->name('trial_balance.filter_trial_balance');
    Route::get('trial_balance_report', [TrialBalanceController::class, 'trialBalanceReport'])->name('trial_balance.trial_balance_report');

    // income statement
    Route::get('filter_income_statement', [IncomeStatementController::class, 'incomeStatementFilter'])->name('income_statement.filter_income_statement');
    Route::get('income_statement', [IncomeStatementController::class, 'incomeStatementReport'])->name('income_statement.income_statement_report');



    // sales menu 
    Route::resource('linkedAccountSales', 'linkedAccountSalesController');
    Route::resource('sales_option', 'salesOptionsController');
    Route::resource('item_category', 'ItemCategoryController');

    Route::resource('items', 'ItemController');
    // export dan import items
    Route::get('/export/items', [ExportController::class, 'exportItems'])->name('export.items');
    Route::post('/import/items', [ImportController::class, 'importItems'])->name('import.items');
    // payment method 
    Route::resource('PaymentMethod', 'PaymentMethodController');

    Route::resource('customers', 'CustomersController');
    // export dan import customers
    Route::get('/export/customers', [ExportController::class, 'exportcustomers'])->name('export.customers');
    Route::post('/import/customers', [ImportController::class, 'importcustomers'])->name('import.customers');

    Route::resource('sales_order', 'SalesOrderController');

    // end sales menu

    // purchases menu

    Route::resource('vendors', 'VendorsController');
    // export dan import items
    Route::get('/export/vendors', [ExportController::class, 'exportVendors'])->name('export.vendors');
    Route::post('/import/vendors', [ImportController::class, 'ImportVendors'])->name('import.vendors');


    // end purchases menu
    // payroll menu

    // level karyawan
    Route::resource('LevelKaryawan', 'LevelKaryawanController');
    // unit kerja
    Route::resource('unit_kerja', 'UnitKerjaController');
    // jabatan
    Route::resource('jabatan', 'JabatanController');
    // ptkp
    Route::resource('ptkp', 'PtkpController');
    // export dan import ptkp 
    Route::get('/export/Ptkp', [ExportController::class, 'exportPtkp'])->name('export.Ptkp');
    Route::post('/import/Ptkp', [ImportController::class, 'importPtkp'])->name('import.Ptkp');

    // komponen penghasilan
    Route::resource('komponen_penghasilan', 'KomponenPenghasilanController');
    Route::get('/export/KomponenPenghasilan', [ExportController::class, 'exportKomponenPenghasilan'])->name('export.KomponenPenghasilan');
    Route::post('/import/KomponenPenghasilan', [ImportController::class, 'importKomponenPenghasilan'])->name('import.KomponenPenghasilan');
    // Tax Rates
    Route::resource('tax_rates', 'TaxRatesController');
    // export dan import tax rates 
    Route::get('/export/TaxRates', [ExportController::class, 'exportTaxRates'])->name('export.TaxRates');
    Route::post('/import/TaxRates', [ImportController::class, 'importTaxRates'])->name('import.TaxRates');
    // employee
    Route::resource('employee', 'EmployeeController');
    Route::get('/export/Employee', [ExportController::class, 'exportEmployee'])->name('export.Employee');
    Route::post('/import/Employee', [ImportController::class, 'importEmployee'])->name('import.Employee');
    // wahana
    Route::resource('wahana', 'WahanaController');
    Route::get('/export/Wahana', [ExportController::class, 'exportWahana'])->name('export.Wahana');
    Route::post('/import/Wahana', [ImportController::class, 'importWahana'])->name('import.Wahana');
    // target wahana
    Route::resource('target_wahana', 'TargetWahanaController');
    // jenis hari
    Route::resource('jenis_hari', 'JenisHariController');
    // komposisi Gaji
    Route::resource('komposisi_gaji', 'KomposisiGajiController');
    Route::get('/get-komponen-by-karyawan/{id}', [KomposisiGajiController::class, 'getKomponenByKaryawan']);
    Route::delete('komposisi_gaji/detail/{id}', [KomposisiGajiController::class, 'destroyDetail'])->name('komposisi_gaji.detail.destroy');

    Route::resource('pembayaran_gaji', 'PembayaranGajiController');
    Route::resource('pembayaran_gaji_nonstaff', 'PembayaranGajiNonStaffController');
    Route::get('/get-pembayaran-gaji-by-karyawan/{id}', [PembayaranGajiController::class, 'getKomposisiGajiByKaryawan']);
    // end payroll menu

    // asset menu 
    Route::resource('kategori_asset', 'KategoriAssetController');
    Route::resource('lokasi', 'LokasiController');
    Route::resource('masa_manfaat', 'MasaManfaatController');
    Route::resource('metode_penyusutan', 'MetodePenyusutanController');

    Route::resource('tangible_asset', 'TangibleAssetController');
    Route::get('/export/TangibleAsset', [ExportController::class, 'exportTangibleAsset'])->name('export.TangibleAsset');
    Route::post('/import/TangibleAsset', [ImportController::class, 'importTangibleAsset'])->name('import.TangibleAsset');

    Route::resource('intangible_asset', 'IntangibleAssetController');
    Route::get('/export/IntangibleAsset', [ExportController::class, 'exportIntangibleAsset'])->name('export.IntangibleAsset');
    Route::post('/import/IntangibleAsset', [ImportController::class, 'importIntangibleAsset'])->name('import.IntangibleAsset');

    // end asset menu


    // project 
    Route::get('view_project', [ProjectController::class, 'projectView'])->name('project.view_project');
    Route::get('edit_project', [ProjectController::class, 'projectEdit'])->name('project.edit_project');
    Route::resource('project', 'ProjectController');
    Route::get('/export/Project', [ExportController::class, 'exportProject'])->name('export.Project');
    Route::post('/import/Project', [ImportController::class, 'importProject'])->name('import.Project');
    // end project menu

    // maintenance menu
    Route::resource('start_new_year', 'StartNewYearController');

    // end maintenance menu

});

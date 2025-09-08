<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BonusKaryawanController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\CoaSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenAkunController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IncomeStatementController;
use App\Http\Controllers\ItemController;
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
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseInvoiceDocumentController;
use App\Http\Controllers\PurchaseOrderDocumentController;
use App\Http\Controllers\ReceiptsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesInvoiceDocumentController;
use App\Http\Controllers\SalesOrderDocumentController;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\TargetUnitController;
use App\Http\Controllers\TargetWahanaController;
use App\SalesInvoiceDocument;
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
    Route::get('/coa/search', [CoaSearchController::class, 'search'])->name('coa.search');
    // export dan import chart of account
    Route::get('/export/charofaccount', [ExportController::class, 'exportchartOfAccount'])->name('export.chartOfAccount');
    Route::post('/import/chartofaccount', [ImportController::class, 'importchartOfAccount'])->name('import.chartOfAccount');

    // crud linked Accounts
    Route::resource('linkedAccount', 'linkedAccountController');

    Route::get('/admin/setting_departement', [SettingDepartementController::class, 'edit'])->name('setting_departement.edit');
    Route::put('/admin/setting_departement', [SettingDepartementController::class, 'update'])->name('setting_departement.update');

    Route::get('/departemen/assign-account', [DepartementController::class, 'assign'])->name('departemen.assign');
    Route::post('/departemen/assign-account', [DepartementController::class, 'storeAssign'])->name('departemen.assign.store');
    Route::delete('/departemen/assign/{id}', [DepartementController::class, 'destroyAssign'])->name('departemen.assign.destroy');

    Route::resource('/departemen', 'DepartementController');

    // sales taxes
    Route::resource('/sales_taxes', 'SalesTaxesController');

    // export dan import Departemen
    Route::get('/export/Departemen', [ExportController::class, 'exportDepartemen'])->name('export.Departemen');
    Route::post('/import/Departemen', [ImportController::class, 'importDepartemen'])->name('import.Departemen');

    // journal entry menu
    Route::get('/journal-entry/auto-data', [JournalEntryController::class, 'getAutoData']);

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
    Route::resource('sales_discount', 'SalesDiscountController');

    Route::resource('item_category', 'ItemCategoryController');

    Route::resource('items', 'ItemController');
    // export dan import items
    Route::get('/export/items', [ExportController::class, 'exportItems'])->name('export.items');
    Route::post('/import/items', [ImportController::class, 'importItems'])->name('import.items');
    // payment method 
    Route::resource('PaymentMethod', 'PaymentMethodController');
    // routes/web.php
    Route::get('/payment-methods/{id}/accounts', [\App\Http\Controllers\PaymentMethodController::class, 'accounts'])
        ->name('payment-methods.accounts');


    Route::resource('customers', 'CustomersController');
    // export dan import customers
    Route::get('/export/customers', [ExportController::class, 'exportcustomers'])->name('export.customers');
    Route::post('/import/customers', [ImportController::class, 'importcustomers'])->name('import.customers');

    Route::resource('sales_order', 'SalesOrderController');
    Route::get('sales-orders/documents', [SalesOrderDocumentController::class, 'index'])
        ->name('sales_orders.documents.index');

    Route::prefix('sales-orders/{salesOrder}')->group(function () {
        Route::post('/documents', [App\Http\Controllers\SalesOrderDocumentController::class, 'store'])->name('sales_orders.documents.store');
        Route::get('/documents/{document}/download', [App\Http\Controllers\SalesOrderDocumentController::class, 'download'])->name('sales_orders.documents.download');
        Route::delete('/documents/{document}', [App\Http\Controllers\SalesOrderDocumentController::class, 'destroy'])->name('sales_orders.documents.destroy');
    });

    Route::get('/sales-orders/{id}/pdf', [App\Http\Controllers\SalesOrderController::class, 'exportPdf'])
        ->name('sales_order.pdf');


    Route::resource('sales_invoice', 'SalesInvoiceController');
    Route::get('sales-invoice/documents', [SalesInvoiceDocumentController::class, 'index'])
        ->name('sales_invoice.documents.index');

    Route::get('/sales-invoice/{id}/pdf', [App\Http\Controllers\SalesInvoiceController::class, 'exportPdf'])
        ->name('sales_invoice.pdf');
    Route::prefix('sales-invoice/{salesInvoice}')->group(function () {
        Route::post('/documents', [App\Http\Controllers\SalesInvoiceDocumentController::class, 'store'])->name('sales_invoice.documents.store');
        Route::get('/documents/{document}/download', [App\Http\Controllers\SalesInvoiceDocumentController::class, 'download'])->name('sales_invoice.documents.download');
        Route::delete('/documents/{document}', [App\Http\Controllers\SalesInvoiceDocumentController::class, 'destroy'])->name('sales_invoice.documents.destroy');
    });

    Route::get('/sales_invoice/get-items/{salesOrderId}', [SalesInvoiceController::class, 'getItemsFromSalesOrder']);
    Route::resource('sales_deposits', 'SalesDepositController');
    Route::resource('receipts', 'ReceiptsController');
    // web.php
    Route::get('/get-invoices/{customer}', [ReceiptsController::class, 'getInvoices']);




    // end sales menu

    // purchases menu
    Route::resource('linkedAccountPurchases', 'LinkedAccountPurchasesControlller');
    Route::resource('purchases_options', 'PurchasesOptionsController');


    Route::resource('vendors', 'VendorsController');
    // export dan import items
    Route::get('/export/vendors', [ExportController::class, 'exportVendors'])->name('export.vendors');
    Route::post('/import/vendors', [ImportController::class, 'ImportVendors'])->name('import.vendors');

    // purchase order
    Route::resource('purchase_order', 'PurchaseOrderController');
    Route::get('purchase-order/documents', [PurchaseOrderDocumentController::class, 'index'])
        ->name('purchase_order.documents.index');
    Route::prefix('purchase-order/{purchasOrder}')->group(function () {
        Route::post('/documents', [App\Http\Controllers\PurchaseOrderDocumentController::class, 'store'])->name('purchase_order.documents.store');
        Route::get('/documents/{document}/download', [App\Http\Controllers\PurchaseOrderDocumentController::class, 'download'])->name('purchase_order.documents.download');
        Route::delete('/documents/{document}', [App\Http\Controllers\PurchaseOrderDocumentController::class, 'destroy'])->name('purchase_order.documents.destroy');
    });

    Route::get('/search-item', [ItemController::class, 'search']);

    // purchase invoice
    Route::resource('purchase_invoice', 'PurchaseInvoiceController');
    Route::get('purchase-invoice/documents', [PurchaseInvoiceDocumentController::class, 'index'])
        ->name('purchase_invoice.documents.index');
    Route::prefix('purchase-invoice/{purchaseInvoice}')->group(function () {
        Route::post('/documents', [App\Http\Controllers\PurchaseInvoiceDocumentController::class, 'store'])->name('purchase_invoice.documents.store');
        Route::get('/documents/{document}/download', [App\Http\Controllers\PurchaseInvoiceDocumentController::class, 'download'])->name('purchase_invoice.documents.download');
        Route::delete('/documents/{document}', [App\Http\Controllers\PurchaseInvoiceDocumentController::class, 'destroy'])->name('purchase_invoice.documents.destroy');
    });
    Route::get('/purchase_invoice/get-items/{purchaseOrderId}', [PurchaseInvoiceController::class, 'getItemsFromPurchaseOrder']);


    // payment
    Route::resource('payment', 'PaymentController');


    // end purchases menu

    // inventory menu 
    Route::resource('inventory', 'InventoryController');
    Route::resource('lokasi_inventory', 'LokasiInventoryController');
    Route::resource('price_list_inventory', 'PriceListInventoryController');
    Route::resource('options_inventory', 'OptionsInventoryController');

    // end inventory  menu


    // payroll menu

    // level karyawan
    Route::resource('LevelKaryawan', 'LevelKaryawanController');
    // unit kerja
    Route::resource('unit_kerja', 'UnitKerjaController');
    // jabatan
    Route::resource('jabatan', 'JabatanController');
    // ptkp
    Route::resource('ptkp', 'PtkpController');

    // absensi menu 
    Route::get('/scan-rfid', [AbsensiController::class, 'form'])->name('absensi.form');
    Route::post('/scan-rfid', [AbsensiController::class, 'scan'])->name('absensi.scan');

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
    Route::get('/wahana-by-unit/{id}', [TargetWahanaController::class, 'getWahanaByUnit']);

    // target unit
    Route::resource('target_unit', 'TargetUnitController');
    Route::get('/komponen-by-level/{id}', [TargetUnitController::class, 'getKomponenByLevel']);

    // transaksi Wahana
    Route::resource('transaksi_wahana', 'TransaksiWahanaController');

    // shift karyawan wahana
    Route::resource('shift_karyawan', 'ShiftKaryawanWahanaController');

    // jenis hari
    Route::resource('jenis_hari', 'JenisHariController');

    Route::resource('jam_kerja', 'JamKerjaController');
    // komposisi Gaji
    Route::resource('komposisi_gaji', 'KomposisiGajiController');
    Route::get('/get-komponen-by-karyawan/{id}', [KomposisiGajiController::class, 'getKomponenByKaryawan']);
    Route::delete('komposisi_gaji/detail/{id}', [KomposisiGajiController::class, 'destroyDetail'])->name('komposisi_gaji.detail.destroy');

    Route::resource('pembayaran_gaji', 'PembayaranGajiController');
    Route::resource('pembayaran_gaji_nonstaff', 'PembayaranGajiNonStaffController');
    Route::get('/get-pembayaran-gaji-by-karyawan/{id}', [PembayaranGajiController::class, 'getKomposisiGajiByKaryawan']);

    // slip gaji staff
    Route::get('/slip-gaji', [SlipGajiController::class, 'index'])->name('slip.index');
    Route::get('/slip-gaji/{id}', [SlipGajiController::class, 'show'])->name('slip.show');
    Route::get('/slip-gaji/{id}/download', [SlipGajiController::class, 'download'])->name('slip.download');

    // rekap absensi
    Route::get('/rekap-absensi', [ReportController::class, 'filter'])->name('report.absensi.filter');
    Route::get('/rekap-absensi/hasil', [ReportController::class, 'hasil'])->name('report.absensi.hasil');
    // report target wahana 
    Route::prefix('reports')->group(function () {
        Route::get('/target-wahana/filter', [ReportController::class, 'filterWahana'])->name('report.target_wahana.filter');
        Route::get('/target-wahana/result', [ReportController::class, 'resultWahana'])->name('report.target_wahana.result');
    });
    Route::get('/get-wahana-by-unit/{unitId}', [ReportController::class, 'getWahanaByUnit'])->name('report.getWahanaByUnit');
    Route::get('/reports/target-wahana/pdf', [ReportController::class, 'exportPdfWahana'])->name('report.target_wahana.pdf');
    Route::get('/reports/target-wahana/excel', [ReportController::class, 'exportExcelWahana'])->name('report.target_wahana.excel');
    // routes/web.php
    Route::get('/report/absensi/pdf', [ReportController::class, 'exportPdf'])->name('report.absensi.pdf');
    Route::get('/report/absensi/excel', [ReportController::class, 'exportExcel'])->name('report.absensi.excel');

    // slip gaji non staff
    Route::get('/slip-gaji-nonstaff', [SlipGajiController::class, 'indexNonStaff'])->name('slip.nonStaff.index');
    Route::get('/slip-gaji-nonstaff/{id}', [SlipGajiController::class, 'showNonSataff'])->name('slip.nonStaffshow');
    Route::get('/slip-gaji-nonstaff/{id}/download', [SlipGajiController::class, 'downloadNonStaff'])->name('slip.nonStaffdownload');

    Route::get('/bonus_karyawan', [BonusKaryawanController::class, 'index'])->name('bonus_karyawan.index');

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

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/start-new-year', [AccountingController::class, 'showStartNewYear'])->name('start_new_year');
        Route::post('/start-new-year', [AccountingController::class, 'startNewYearProcess'])->name('start_new_year_proses');
    });

    Route::get('activity_log', [ActivityLogController::class, 'index'])->name('activity_log.index');


    // end maintenance menu

    // taxes menu
    Route::resource('taxes', 'TaxesController');

    // end taxes menu
});

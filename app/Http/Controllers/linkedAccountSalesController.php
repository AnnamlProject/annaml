<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\linkedAccounts;
use Illuminate\Http\Request;

class linkedAccountSalesController extends Controller
{
    public function index()
    {
        $linkedAccountSales = linkedAccounts::with('akun')
            ->where('modul', 'sales')
            ->orderBy('kode') // opsional: urutkan berdasarkan kode
            ->get();

        return view('linkedAccountSales.index', compact('linkedAccountSales'));
    }
    public function create()
    {
        $akun1 = chartOfAccount::where('tipe_akun', 'Aset')->get();
        $akun2 = chartOfAccount::where('tipe_akun', 'Kewajiban')->get();
        $akun4 = chartOfAccount::where('tipe_akun', 'Pendapatan')->get();
        $accounts = chartOfAccount::where('tipe_akun', 'Ekuitas')->get();

        // Ambil semua akun BANK dengan level ACCOUNT
        $bankAccounts = chartOfAccount::whereRaw('LOWER(nama_akun) LIKE ?', ['%bank%'])
            ->whereRaw('LOWER(level_akun) = ?', ['account'])
            ->get();

        // Ambil prefix dari kode_akun untuk digunakan sebagai acuan pencarian sub account
        $bankPrefixes = $bankAccounts->pluck('kode_akun');

        // Ambil sub account berdasarkan prefix kode_akun
        $subBankAccounts = collect();
        foreach ($bankPrefixes as $prefix) {
            $subBankAccounts = $subBankAccounts->merge(
                chartOfAccount::whereRaw('LOWER(level_akun) = ?', ['sub account'])
                    ->where('kode_akun', 'like', $prefix . '%')
                    ->get()
            );
        }

        return view('linkedAccountSales.create', compact(
            'accounts',
            'akun1',
            'akun2',
            'akun4',
            'subBankAccounts'
        ));
    }



    public function store(Request $request)
    {
        $request->validate([
            'principal_bank_account_id' => 'required|exists:chart_of_accounts,id',
            'account_receivable_id' => 'required|exists:chart_of_accounts,id',
            'default_revenue_id' => 'required|exists:chart_of_accounts,id',
            'freight_revenue_id' => 'required|exists:chart_of_accounts,id',
            'early_payment_discount_id' => 'required|exists:chart_of_accounts,id',
            'prepaid_order_id' => 'required|exists:chart_of_accounts,id',
        ]);

        $data = [
            'principal_bank_account'   => $request->principal_bank_account_id,
            'account_receivable'       => $request->account_receivable_id,
            'default_revenue'          => $request->default_revenue_id,
            'freight_revenue'          => $request->freight_revenue_id,
            'early_payment_discount'   => $request->early_payment_discount_id,
            'prepaid_order'            => $request->prepaid_order_id,
        ];

        foreach ($data as $kode => $akun_id) {
            linkedAccounts::updateOrInsert(
                ['modul' => 'sales', 'kode' => $kode],
                ['akun_id' => $akun_id, 'updated_at' => now()]
            );
        }

        return redirect()->route('linkedAccountSales.index')->with('success', 'Linked Account Sales berhasil disimpan.');
    }
}

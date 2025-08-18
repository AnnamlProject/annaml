<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\linkedAccounts;
use Illuminate\Http\Request;

class LinkedAccountPurchasesControlller extends Controller
{
    //
    public function index()
    {
        $linkedAccountPurchases = linkedAccounts::with('akun')
            ->where('modul', 'purchases')
            ->orderBy('kode') // opsional: urutkan berdasarkan kode
            ->get();

        return view('linkedAccountPurchases.index', compact('linkedAccountPurchases'));
    }
    public function create()
    {
        $akun1 = chartOfAccount::where('tipe_akun', 'Aset')->get();
        $akun2 = chartOfAccount::where('tipe_akun', 'Kewajiban')->get();
        $akun4 = chartOfAccount::where('tipe_akun', 'Pendapatan')->get();
        $akun5 = chartOfAccount::where('tipe_akun', 'Beban')->get();
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

        return view('linkedAccountPurchases.create', compact(
            'accounts',
            'akun1',
            'akun2',
            'akun4',
            'akun5',
            'subBankAccounts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'principal_bank_account_id' => 'required|exists:chart_of_accounts,id',
            'account_payable_id' => 'required|exists:chart_of_accounts,id',
            'freight_expense_id' => 'required|exists:chart_of_accounts,id',
            'early_payment_purchase_discount_id' => 'required|exists:chart_of_accounts,id',
            'prepayments_prepaid_orders' => 'required|exists:chart_of_accounts,id',
        ]);

        $data = [
            'Principalk Bank Account'   => $request->principal_bank_account_id,
            'Account Payable'       => $request->account_payable_id,
            'Freught Expense'          => $request->freight_expense_id,
            'Early Payment Purchase Discount'          => $request->early_payment_purchase_discount_id,
            'Prepayments Prepaid Orders'   => $request->prepayments_prepaid_orders,
        ];

        foreach ($data as $kode => $akun_id) {
            linkedAccounts::updateOrInsert(
                ['modul' => 'purchases', 'kode' => $kode],
                ['akun_id' => $akun_id, 'updated_at' => now()]
            );
        }

        return redirect()->route('linkedAccountPurchases.index')->with('success', 'Linked Account Purchases berhasil disimpan.');
    }
}

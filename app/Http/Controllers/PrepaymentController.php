<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Item;
use App\JournalEntry;
use App\Prepayment;
use App\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrepaymentController extends Controller
{
    //

    public function index()
    {

        $data = Prepayment::with(['vendor', 'account'])->orderBy('tanggal_prepayment', 'asc')->paginate(20);
        return view('prepayment.index', compact('data'));
    }

    public function create()
    {
        $account = chartOfAccount::all();
        $vendor = Vendors::all();
        $paidAccount = \App\linkedAccounts::with('akun')
            ->where('kode', 'Prepayments Prepaid Orders')
            ->first();
        return view('prepayment.create', compact('vendor', 'account', 'paidAccount'));
    }
    public function store(Request $request)
    {

        // hilangkan titik ribuan sebelum validasi
        $request->merge([
            'amount' => str_replace('.', '', $request->amount),
        ]);

        // dd($request->all());
        // === 1) Validasi header ===
        $validated = $request->validate([
            'tanggal_prepayment'              => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'account_header_id' => 'required|exists:chart_of_accounts,id',
            'reference'            => 'required|string|max:255',
            'amount' => 'required|numeric',
            'comment'             => 'nullable|string',

        ]);

        DB::beginTransaction();
        try {
            // === 2) Simpan header ===
            $expense = Prepayment::create([
                'tanggal_prepayment'              => $validated['tanggal_prepayment'],
                'vendor_id' => $validated['vendor_id'],
                'account_id'            => $validated['account_header_id'],
                'amount'             => $validated['amount'],
                'reference' => $validated['reference'],
                'comment'             => $validated['comment'] ?? null,
            ]);

            // === 4) Buat Journal Entry ===
            $journal = JournalEntry::create([
                'source'  => 'Prepayment#' . $expense->id,
                'tanggal'  => $validated['tanggal_prepayment'],
                'comment'  => $validated['comment'] ?? null,
            ]);


            $totalCredit = 0;
            $totalDebit  = 0;

            $coaCode = fn($id) => $id ? \App\ChartOfAccount::whereKey($id)->value('kode_akun') : null;


            // Journal Debit Prepayment
            $accountPrepayment = ChartOfAccount::find($validated['account_header_id']);
            $journal->details()->create([
                'journal_entry_id' => $journal->id,
                'kode_akun' => $accountPrepayment->kode_akun,
                'debits'    => $expense->amount,
                'credits'   => 0,
                'comment'   => "Prepayment {$expense->id}",
            ]);

            // Journal Credit Kas/Bank
            $kasAccount = \App\LinkedAccounts::where('kode', 'Prepayments Prepaid Orders')->first();
            $journal->details()->create([
                'journal_entry_id' => $journal->id,
                'kode_akun' => $coaCode(optional($kasAccount)->akun_id),
                'debits'    => 0,
                'credits'   => $expense->amount,
                'comment'   => "Pembayaran Prepayment {$expense->id}",
            ]);
            DB::commit();

            return redirect()
                ->route('prepayment.index')
                ->with('success', 'Prepayment berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd("gagal", $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $data = Prepayment::with(['vendor', 'account'])->findOrFail($id);
        return view('prepayment.show', compact('data'));
    }
}

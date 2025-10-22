<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\JournalEntry;
use App\PaymentMethod;
use App\Payment;
use App\PaymentDetail;
use App\PaymentMethodDetail;
use App\Prepayment;
use App\PrepaymentAllocation;
use App\PurchaseInvoice;
use App\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    //
    public function index()
    {
        $data = Payment::with(['jenis_pembayaran', 'vendor', 'PaymentMethodAccount'])->orderBy('payment_date')->paginate(10);
        return view('payment.index', compact('data'));
    }
    public function create()
    {
        $vendor = Vendors::all();
        $jenis_pembayaran = PaymentMethod::all();
        $account = chartOfAccount::all();
        return view('payment.create', compact('vendor', 'account', 'jenis_pembayaran'));
    }
    public function store(Request $request)
    {

        // dd($request->all());
        $validated = $request->validate([
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'payment_method_account_id' => 'required|exists:payment_method_details,id',
            'source' => 'nullable|string|max:255',
            'vendor_id' => 'required|exists:vendors,id',
            'payment_date' => 'required|date',
            'comment' => 'nullable|string',
            // optional: payment_amounts dan prepayment_allocations diverifikasi di bawah
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Simpan ke tabel payments
            $payment = Payment::create([
                'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'],
                'payment_method_account_id' => $request->input('payment_method_account_id'),
                'source' => $validated['source'] ?? null,
                'vendor_id' => $validated['vendor_id'],
                'payment_date' => $validated['payment_date'],
                'comment' => $validated['comment'] ?? null,
                'type' => 'invoice', // default, bisa diganti kalau nanti ada "other"
            ]);

            // 2️⃣ Simpan detail pembayaran untuk setiap invoice
            if ($request->has('payment_amount')) {
                foreach ($request->payment_amount as $invoiceId => $amount) {
                    if ($amount > 0) {
                        $invoice = \App\PurchaseInvoice::find($invoiceId);

                        PaymentDetail::create([
                            'payment_id' => $payment->id,
                            'invoice_number_id' => $invoice->id ?? null,
                            'due_date' => $invoice->due_date ?? null,
                            'original_amount' => $invoice->original_amount ?? 0,
                            'amount_owing' => $invoice->amount_owing ?? 0,
                            'discount_available' => $invoice->discount_available ?? 0,
                            'discount_taken' => 0,
                            'payment_amount' => $amount,
                        ]);
                    }
                    // dd($invoice);
                }
            }

            // 3️⃣ Simpan alokasi prepayment (jika ada)
            if ($request->has('prepayment_allocations')) {
                foreach ($request->prepayment_allocations as $prepaymentId => $amount) {
                    if ($amount > 0) {
                        $relatedInvoice = collect($request->payment_amount ?? [])->keys()->first();
                        PrepaymentAllocation::create([
                            'prepayment_id' => $prepaymentId,
                            'purchase_invoice_id' => $relatedInvoice,
                            'allocated_amount' => $amount,
                        ]);
                    }
                    // dd($relatedInvoice);
                }
            }
            // 4️⃣ Buat Journal Entry
            $journal = JournalEntry::create([
                'source'  => 'Payment#' . $payment->id,
                'tanggal' => $validated['payment_date'],
                'comment' => $validated['comment'] ?? null,
            ]);

            // 4A️⃣ Credit: Kas/Bank (dari Payment Method Detail)
            $pmDetail = PaymentMethodDetail::with('chartOfAccount')->find($validated['payment_method_account_id']);
            $coaPayment = optional($pmDetail->chartOfAccount);

            $totalDebit = 0;
            $totalCredit = 0;

            // 4B️⃣ Debit: untuk setiap invoice yang dibayar
            if ($request->has('payment_amount')) {
                foreach ($request->payment_amount as $invoiceId => $amount) {
                    if ($amount > 0) {
                        $invoice = PurchaseInvoice::with('paymentmethodDetail.chartOfAccount')->find($invoiceId);
                        $coaDebit = optional($invoice->paymentmethodDetail->chartOfAccount);

                        $journal->details()->create([
                            'kode_akun' => $coaDebit->kode_akun ?? null,
                            'debits' => $amount,
                            'credits' => 0,
                            'comment' => 'Pembayaran invoice ' . ($invoice->invoice_number ?? $invoiceId),
                        ]);

                        $totalDebit += $amount;
                    }
                }
            }

            // 4C️⃣ Debit: untuk setiap prepayment yang dialokasikan
            if ($request->has('prepayment_allocations')) {
                foreach ($request->prepayment_allocations as $prepaymentId => $amount) {
                    if ($amount > 0) {
                        $prepayment = Prepayment::with('accountPrepayment')->find($prepaymentId);
                        $coaDebit = optional($prepayment->accountPrepayment);

                        $journal->details()->create([
                            'kode_akun' => $coaDebit->kode_akun ?? null,
                            'debits' => $amount,
                            'credits' => 0,
                            'comment' => 'Alokasi prepayment ' . ($prepayment->reference ?? $prepaymentId),
                        ]);

                        $totalDebit += $amount;
                    }
                }
            }

            // 4D️⃣ Credit total kas/bank
            $journal->details()->create([
                'kode_akun' => $coaPayment->kode_akun ?? null,
                'debits' => 0,
                'credits' => $totalDebit,
                'comment' => 'Pembayaran melalui ' . ($coaPayment->nama_akun ?? 'Kas/Bank'),
            ]);
            DB::commit();

            return redirect()
                ->route('payment.index')
                ->with('success', 'Payment berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Gagal menyimpan payment: ' . $e->getMessage()]);
        }
    }
    public function show($id)
    {
        $data = Payment::with(['vendor', 'jenis_pembayaran', 'PaymentMethodAccount', 'details'])->findOrFail($id);

        $purchase_invoice = db::table('payments')
            ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payment_id')
            ->leftJoin('purchase_invoices', 'payment_details.invoice_number_id', '=', 'purchase_invoices.id')
            ->where('payments.id', $id)
            ->select('purchase_invoices.invoice_number')
            ->first();


        $invoice_number = $purchase_invoice->invoice_number ?? null;

        return view('payment.show', compact('data', 'invoice_number'));
    }
    public function edit($id)
    {
        // 1️⃣ Load relasi lengkap, termasuk nested details
        $data = Payment::with([
            'vendor',
            'jenis_pembayaran',
            'PaymentMethodAccount',
            'details.invoice.details' // sampai ke purchase_invoice_details
        ])->findOrFail($id);

        $vendor = Vendors::all();
        $jenis_pembayaran = PaymentMethod::all();

        // 2️⃣ Hitung original_amount di controller
        foreach ($data->details as $detail) {
            $totalAmount = 0;
            $amount = 0;
            $tax_amount = 0;

            // pastikan invoice dan invoice->details tersedia
            if ($detail->invoice && $detail->invoice->details) {
                foreach ($detail->invoice->details as $invDetail) {
                    $price = $invDetail->price ?? 0;
                    $discount = $invDetail->discount ?? 0;
                    $qty = $invDetail->quantity ?? 0;

                    $freight = $detail->invoice->freight;
                    $tax_amount = $invDetail->tax_amount;
                    $amount += ($price - $discount) * $qty;

                    $totalAmount = $amount + $freight + $tax_amount;
                }
            }

            $totalPaid = PaymentDetail::where('invoice_number_id', $detail->invoice_number_id)
                ->sum('payment_amount');


            // tambahkan properti baru agar bisa langsung dipakai di Blade
            $detail->original_amount = $totalAmount;
            $detail->amount_owing = $totalAmount - $totalPaid;
        }

        return view('payment.edit', compact('data', 'vendor', 'jenis_pembayaran'));
    }
}

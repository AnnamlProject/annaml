<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\PaymentMethod;
use App\Payment;
use App\PaymentDetail;
use App\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    //
    public function index()
    {
        $data = Payment::latest()->paginate(10);
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
        $request->validate([
            'jenis_pembayaran_id' => 'required',
            'from_account' => 'required|string',
            'source' => 'required|string',
            'vendor_id' => 'required',
            'payment_date' => 'required|date',
            'type' => 'required|in:Invoice,Other',
            'comment' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'from_account' => $request->from_account,
                'source' => $request->source,
                'vendor_id' => $request->vendor_id,
                'payment_date' => $request->payment_date,
                'type' => $request->type,
                'comment' => $request->comment,
            ]);

            if ($request->type === 'Invoice') {
                foreach ($request->invoice_details as $detail) {
                    if (!empty($detail['invoice_number'])) {
                        PaymentDetail::create([
                            'payment_id' => $payment->id,
                            'due_date' => $detail['due_date'] ?? null,
                            'invoice_number' => $detail['invoice_number'],
                            'original_amount' => $detail['original_amount'] ?? 0,
                            'amount_owing' => $detail['amount_owing'] ?? 0,
                            'discount_available' => $detail['discount_available'] ?? 0,
                            'discount_taken' => $detail['discount_taken'] ?? 0,
                            'payment_amount' => $detail['payment_amount'] ?? 0,
                        ]);
                    }
                }
            } elseif ($request->type === 'Other') {
                foreach ($request->other_details as $detail) {
                    if (!empty($detail['account'])) {
                        PaymentDetail::create([
                            'payment_id' => $payment->id,
                            'account_id' => $detail['account'],
                            'description' => $detail['description'] ?? null,
                            'amount' => $detail['amount'] ?? 0,
                            'tax' => $detail['tax'] ?? 0,
                            'allocation' => $detail['allocation'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('payment.index')->with('success', 'Payment saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to save payment: ' . $e->getMessage()]);
        }
    }
}

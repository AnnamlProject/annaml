<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\PaymentMethod;
use App\Payment;
use App\PaymentDetail;
use App\Prepayment;
use App\PrepaymentAllocation;
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
        $vendorId = $request->vendor_id;
        $tanggal_payment = $request->payment_date;
        $source = $request->source;
        $jenis_pembayaran = $request->jenis_pembayaran_id;
        $account_payment = $request->payment_method_account_id;
        $comment = $request->comment;


        $payment = Payment::create([
            'vendor_id' => $vendorId,
            'jenis_pembayaran_id' => $jenis_pembayaran,
            'payment_method_account_id' => $account_payment,
            'payment_date' => $tanggal_payment,
            'type' => 'vendor_payment',
            'source' => $source,
            'comment' => $comment,
        ]);

        if ($request->payment_amount) {
            foreach ($request->payment_amount as $invoiceId => $amount) {
                if ($amount > 0) {
                    PaymentDetail::create([
                        'payment_id' => $payment->id,
                        'payment_amount' => $request->payment_amount,
                        'invoice_number_id' => $request->invoice_number_id,
                        'account_id' => $request->account_id ?? null
                    ]);
                }
            }
        }

        // 2️⃣ Simpan alokasi prepayment
        if ($request->prepayment_allocations) {
            foreach ($request->prepayment_allocations as $prepaymentId => $amount) {
                if ($amount > 0) {
                    PrepaymentAllocation::create([
                        'prepayment_id' => $prepaymentId,
                        'purchase_invoice_id' => null, // nanti bisa dikaitkan
                        'allocated_amount' => $amount,
                    ]);

                    // kurangi saldo prepayment
                    $prepayment = Prepayment::find($prepaymentId);
                    $prepayment->amount -= $amount;
                    $prepayment->save();
                }
            }
        }
    }
}

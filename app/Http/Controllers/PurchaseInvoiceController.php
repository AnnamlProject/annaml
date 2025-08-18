<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Customers;
use App\Item;
use App\PaymentMethod;
use App\Project;
use App\PurchaseInvoice;
use App\PurchaseInvoiceDetail;
use App\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    //
    public function index()
    {
        $data = PurchaseInvoice::with(['jenisPembayaran', 'customer'])->paginate(10);
        return view('purchase_invoice.index', compact('data'));
    }
    public function create()
    {
        $customer = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all();
        $accounts = chartOfAccount::all();
        $purchase_order = PurchaseOrder::all();
        $project = Project::all();
        return view('purchase_invoice.create', compact('customer', 'jenis_pembayaran',  'items', 'accounts', 'purchase_order', 'project'));
    }

    public function getItemsFromPurchaseOrder($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with(['details.item', 'details.account'])
            ->findOrFail($purchaseOrderId);

        return response()->json([
            'items' => $purchaseOrder->details->map(function ($detail) {
                return [
                    'id'           => $detail->item->id ?? null,
                    'item_number'  => $detail->item->item_number ?? '',
                    'description'  => $detail->item_description ?? '',
                    'quantity'     => $detail->quantity ?? 0,
                    'order'        => $detail->order ?? 0,
                    'back_order'   => $detail->back_order ?? 0,
                    'unit'         => $detail->item->unit ?? '',
                    'price'        => $detail->price ?? 0,
                    'tax'          => $detail->tax ?? 0,
                    'tax_amount'   => $detail->tax_amount ?? 0,
                    'amount'       => $detail->amount ?? 0,
                    'account_id'   => $detail->account_id ?? null,
                    'account_name' => $detail->account->nama_akun ?? '',
                ];
            })->values(), // supaya index array rapi
        ]);
    }


    private function generateKodeInvoice()
    {
        $last = \App\PurchaseInvoice::orderBy('invoice_number', 'desc')->first();

        if ($last && preg_match('/INV-(\d+)/', $last->invoice_number, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'INV-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        // Cek apakah checkbox auto_generate dicentang
        if ($request->has('auto_generate')) {
            $invoice_number = $this->generateKodeInvoice();
        } else {
            // Validasi invoice_number manual
            $request->validate([
                'invoice_number' => 'unique:purchase_invoices,invoice_number',
            ]);
            $invoice_number = $request->invoice_number;
        }

        // ✅ VALIDASI FORM UTAMA & ITEM
        $request->validate([
            'date_invoice'           => 'required|date',
            'shipping_date'          => 'required|date',
            'customer_id'            => 'required|exists:customers,id',
            'purchase_order_id'        => 'nullable|exists:purchase_orders,id',
            'jenis_pembayaran_id'    => 'required|exists:payment_methods,id',
            'shipping_address'       => 'required|string',
            'freight'                => 'required|numeric|min:0',
            'early_payment_terms'    => 'nullable|string',
            'messages'               => 'nullable|string',

            // Validasi item detail
            'items'                  => 'nullable|array|min:1',
            'items.*.item_id'        => 'nullable|exists:items,id',
            'items.*.quantity'       => 'nullable|numeric|min:0',
            'items.*.order'          => 'nullable|numeric|min:0',
            'items.*.back_order'     => 'nullable|numeric|min:0',
            'items.*.unit'           => 'nullable|string',
            'items.*.item_description'    => 'nullable|string',
            'items.*.price'     => 'nullable|numeric|min:0',
            'items.*.tax'       => 'nullable|numeric|min:0',
            'items.*.tax_amount'          => 'nullable|numeric|min:0',
            'items.*.amount'         => 'nullable|numeric|min:0',
            'items.*.account_id'        => 'nullable|exists:chart_of_accounts,id',
            'items.*.project_id'        => 'nullable|exists:projects,id',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Simpan ke tabel sales_invoices
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number'       => $invoice_number,
                'date_invoice'         => $request->date_invoice,
                'shipping_date'        => $request->shipping_date,
                'customer_id'          => $request->customer_id,
                'purchase_order_id'       => $request->purchase_order_id,
                'jenis_pembayaran_id'  => $request->jenis_pembayaran_id,
                'shipping_address'     => $request->shipping_address,
                'freight'              => $request->freight,
                'early_payment_terms'  => $request->early_payment_terms,
                'messages'             => $request->messages,
            ]);

            // ✅ Simpan ke tabel sales_invoice_details
            foreach ($request->items as $item) {
                PurchaseInvoiceDetail::create([
                    'purchase_invoice_id'   => $purchaseInvoice->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'order'              => $item['order'],
                    'back_order'         => $item['back_order'] ?? 0,
                    'unit'               => $item['unit'],
                    'item_description'   => $item['item_description'],
                    'price'         => $item['price'] ?? 0,
                    'tax'           => $item['tax'] ?? 0,
                    'tax_amount'              => $item['tax_amount'] ?? 0,
                    'amount'             => $item['amount'] ?? 0,
                    'account_id'         => $item['account_id'],
                    'project_id'         => $item['project_id'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_invoice.index')->with('success', 'Purchase Invoice berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
    public function show($id)
    {
        // Ambil sales order beserta relasi terkait
        $purchaseInvoice = PurchaseInvoice::with([
            'customer',
            'jenisPembayaran',
            'details.item',
            'details.account'
        ])->findOrFail($id);

        return view('purchase_invoice.show', compact('purchaseInvoice'));
    }
    public function edit($id)
    {
        $purchaseInvoice = PurchaseInvoice::with('details')->findOrFail($id);
        $customers = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all(); // semua item yang bisa dipilih


        return view('purchase_invoice.edit', compact('purchaseInvoice', 'customers', 'jenis_pembayaran', 'items'));
    }
}

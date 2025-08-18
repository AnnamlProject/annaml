<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Customers;
use App\Item;
use App\PaymentMethod;
use App\PurchaseOrder;
use App\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    //

    public function index()
    {
        $data = PurchaseOrder::with(['jenisPembayaran', 'customer'])->paginate(10);
        return view('purchase_order.index', compact('data'));
    }
    public function create()
    {
        $customer = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all(); // semua item yang bisa dipilih
        $accounts = chartOfAccount::all(); // akun-akun untuk entri jurnal
        return view('purchase_order.create', compact('customer', 'jenis_pembayaran',  'items', 'accounts'));
    }
    public function store(Request $request)
    {
        // ✅ VALIDASI FORM INPUT
        $request->validate([
            'order_number'         => 'required|unique:purchase_orders,order_number',
            'date_order'           => 'required|date',
            'shipping_date'        => 'required|date',
            'customer_id'          => 'required|exists:customers,id',
            'jenis_pembayaran_id'  => 'required|exists:payment_methods,id',
            'shipping_address'     => 'required|string',
            'freight'              => 'required|numeric|min:0',
            'early_payment_terms'  => 'nullable|string',
            'messages'             => 'nullable|string',

            // Validasi detail item
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'required|numeric|min:0',
            'items.*.order'        => 'required|numeric|min:0',
            'items.*.back_order'   => 'nullable|numeric|min:0',
            'items.*.unit'         => 'required|string',
            'items.*.description'  => 'required|string',
            'items.*.price'   => 'nullable|numeric|min:0',
            'items.*.tax'     => 'nullable|numeric|min:0',
            'items.*.tax_amount'        => 'nullable|numeric|min:0',
            'items.*.amount'       => 'nullable|numeric|min:0',
            'items.*.account'      => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Simpan data utama ke tabel sales_orders
            $purchaseOrder = PurchaseOrder::create([
                'order_number'         => $request->order_number,
                'date_order'           => $request->date_order,
                'shipping_date'        => $request->shipping_date,
                'customer_id'          => $request->customer_id,
                'jenis_pembayaran_id'  => $request->jenis_pembayaran_id,
                'shipping_address'     => $request->shipping_address,
                'freight'              => $request->freight,
                'early_payment_terms'  => $request->early_payment_terms,
                'messages'             => $request->messages,
            ]);

            // ✅ Simpan setiap item ke tabel sales_order_details
            foreach ($request->items as $item) {
                PurchaseOrderDetail::create([
                    'purchase_order_id'     => $purchaseOrder->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'order'              => $item['order'],
                    'back_order'         => $item['back_order'],
                    'unit'               => $item['unit'],
                    'item_description'   => $item['description'],
                    'price'         => $item['price'],
                    'tax'           => $item['tax'],
                    'tax_amount'              => $item['tax_amount'],
                    'amount'             => $item['amount'],
                    'account_id'         => $item['account'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_order.index')->with('success', 'Purchase order berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
    public function show($id)
    {
        // Ambil sales order beserta relasi terkait
        $purchaseOrder = PurchaseOrder::with([
            'customer',
            'jenisPembayaran',
            'details.item',
            'details.account'
        ])->findOrFail($id);

        return view('purchase_order.show', compact('purchaseOrder'));
    }
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('details')->findOrFail($id);
        $customers = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all(); // semua item yang bisa dipilih


        return view('purchase_order.edit', compact('purchaseOrder', 'customers', 'jenis_pembayaran', 'items'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_number'         => 'required|string',
            'date_order'           => 'required|date',
            'shipping_date'        => 'required|date',
            'customer_id'          => 'required|exists:customers,id',
            'jenis_pembayaran_id'  => 'required|exists:payment_methods,id',
            'shipping_address'     => 'required|string',
            'freight'              => 'required|numeric|min:0',
            'early_payment_terms'  => 'nullable|string',
            'messages'             => 'nullable|string',
            'early_payment_terms' => 'required|string',
            // edatil
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'required|numeric|min:0',
            'items.*.order'        => 'required|numeric|min:0',
            'items.*.back_order'   => 'nullable|numeric|min:0',
            'items.*.unit'         => 'required|string',
            'items.*.description'  => 'required|string',
            'items.*.price'   => 'nullable|numeric|min:0',
            'items.*.tax'     => 'nullable|numeric|min:0',
            'items.*.tax_amount'        => 'nullable|numeric|min:0',
            'items.*.amount'       => 'nullable|numeric|min:0',
            'items.*.account'      => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrder->update([
                'order_number'         => $request->order_number,
                'date_order'           => $request->date_order,
                'shipping_date'        => $request->shipping_date,
                'customer_id'          => $request->customer_id,
                'jenis_pembayaran_id'  => $request->jenis_pembayaran_id,
                'shipping_address'     => $request->shipping_address,
                'freight'              => $request->freight,
                'early_payment_terms'  => $request->early_payment_terms,
                'messages'             => $request->messages,
            ]);

            // Hapus detail lama dan simpan ulang
            PurchaseOrderDetail::where('purchase_order_id', $purchaseOrder->id)->delete();

            foreach ($request->items as $item) {
                PurchaseOrderDetail::create([
                    'purchase_order_id'     => $purchaseOrder->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'order'              => $item['order'],
                    'back_order'         => $item['back_order'],
                    'unit'               => $item['unit'],
                    'item_description'   => $item['description'],
                    'price'         => $item['price'],
                    'tax'           => $item['tax'],
                    'tax_amount'              => $item['tax_amount'],
                    'amount'             => $item['amount'],
                    'account_id'         => $item['account'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_order.index')->with('success', 'Purchase order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage())->withInput();
        }
    }
}

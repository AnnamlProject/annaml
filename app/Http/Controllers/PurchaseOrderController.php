<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Customers;
use App\Item;
use App\LocationInventory;
use App\PaymentMethod;
use App\PurchaseOrder;
use App\PurchaseOrderDetail;
use App\SalesTaxes;
use App\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    //

    public function index()
    {
        $data = PurchaseOrder::with(['jenisPembayaran', 'vendor', 'locationInventory'])->paginate(10);
        return view('purchase_order.index', compact('data'));
    }
    public function create()
    {
        $vendor = Vendors::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all(); // semua item yang bisa dipilih
        $accounts = chartOfAccount::all(); // akun-akun untuk entri jurnal
        $sales_taxes = SalesTaxes::all();
        $locationInventory = LocationInventory::all();
        return view('purchase_order.create', compact('vendor', 'jenis_pembayaran',  'items', 'accounts', 'sales_taxes', 'locationInventory'));
    }
    private function generateKodeOrder($dateOrder)
    {
        // Formatkan tanggal order sesuai kebutuhan (misal YYYYMMDD)
        $dateFormatted = \Carbon\Carbon::parse($dateOrder)->format('Ymd');

        $prefix = 'PO-' . $dateFormatted . '-';

        // Cari order terakhir berdasarkan tanggal order yg sama
        $last = \App\PurchaseOrder::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($last && preg_match('/' . $prefix . '(\d+)/', $last->order_number, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        // Cek apakah checkbox auto_generate dicentang
        if ($request->has('auto_generate')) {
            $order_number = $this->generateKodeOrder($request->date_order);
        } else {
            // Validasi manual
            $request->validate([
                'order_number' => 'unique:purchase_orders,order_number',
            ]);
            $order_number = $request->order_number;
        }

        // ✅ VALIDASI FORM INPUT
        $request->validate([
            'order_number'         => 'required|unique:purchase_orders,order_number',
            'date_order'           => 'required|date',
            'shipping_date'        => 'required|date',
            'vendor_id'          => 'required|exists:vendors,id',
            'account_id' => 'required|exists:payment_method_details,id',
            'jenis_pembayaran_id'  => 'required|exists:payment_methods,id',
            'location_id'  => 'required|exists:location_inventories,id',
            'shipping_address'     => 'required|string',
            'freight'              => 'required|numeric|min:0',
            'early_payment_terms'  => 'nullable|string',
            'messages'             => 'nullable|string',

            // Validasi detail item
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'nullable|numeric|min:0',
            'items.*.order'        => 'required|numeric|min:0',
            'items.*.back_order'   => 'nullable|numeric|min:0',
            'items.*.unit'         => 'required|string',
            'items.*.description'  => 'required|string',
            'items.*.price'   => 'nullable|numeric|min:0',
            'items.*.tax_id'      => 'nullable|exists:sales_taxes,id',
            'items.*.tax_amount'        => 'nullable|numeric|min:0',
            'items.*.amount'       => 'nullable|numeric|min:0',
            'items.*.account'      => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Simpan data utama ke tabel sales_orders
            $purchaseOrder = PurchaseOrder::create([
                'order_number'         => $order_number,
                'date_order'           => $request->date_order,
                'shipping_date'        => $request->shipping_date,
                'vendor_id'          => $request->vendor_id,
                'account_id' => $request->account_id,
                'location_id' => $request->location_id,
                'jenis_pembayaran_id'  => $request->jenis_pembayaran_id,
                'shipping_address'     => $request->shipping_address,
                'freight'              => $request->freight,
                'early_payment_terms'  => $request->early_payment_terms,
                'messages'             => $request->messages,
            ]);

            // ✅ Simpan setiap item ke tabel sales_order_details
            foreach ($request->items as $item) {
                $price = isset($item['price']) ? (float) str_replace(',', '', $item['price']) : 0;
                $amount     = isset($item['amount'])     ? (float) str_replace(',', '', $item['amount'])     : 0;
                $tax_amount       = isset($item['tax_amount'])        ? (float) str_replace(',', '', $item['tax_amount'])        : 0;


                PurchaseOrderDetail::create([
                    'purchase_order_id'     => $purchaseOrder->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'] ?? 0,
                    'order'              => $item['order'],
                    'back_order'         => $item['back_order'],
                    'unit'               => $item['unit'],
                    'item_description'   => $item['description'],
                    'price'         => $price,
                    'tax_id'           => $item['tax_id'],
                    'tax_amount'              => $tax_amount,
                    'amount'             => $amount,
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
            'vendor',
            'locationInventory',
            'jenisPembayaran',
            'details.item',
            'details.account',
            'details.sales_taxes'
        ])->findOrFail($id);

        return view('purchase_order.show', compact('purchaseOrder'));
    }
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('details', 'details.sales_taxes')->findOrFail($id);
        $vendors = Vendors::all();
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all();
        $locationInventory = LocationInventory::all();
        $account = ChartOfAccount::all();
        $sales_taxes = SalesTaxes::all(); // ✅ ambil semua pajak

        return view('purchase_order.edit', compact(
            'purchaseOrder',
            'vendors',
            'jenis_pembayaran',
            'items',
            'account',
            'sales_taxes',
            'locationInventory' // ✅ kirim ke view
        ));
    }

    public function update(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'order_number'         => 'required|string',
            'date_order'           => 'required|date',
            'shipping_date'        => 'required|date',
            'vendor_id'          => 'required|exists:vendors,id',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'location_id' => 'required|exists:location_inventories,id',
            'jenis_pembayaran_id'  => 'required|exists:payment_methods,id',
            'shipping_address'     => 'required|string',
            'freight'              => 'required|numeric|min:0',
            'early_payment_terms'  => 'nullable|string',
            'messages'             => 'nullable|string',
            // edatil
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'required|numeric|min:0',
            'items.*.order'        => 'required|numeric|min:0',
            'items.*.back_order'   => 'nullable|numeric|min:0',
            'items.*.unit'         => 'required|string',
            'items.*.description'  => 'required|string',
            'items.*.price'   => 'nullable|numeric|min:0',
            'items.*.tax_id'     => 'nullable|exists:sales_taxes,id',
            'items.*.tax_amount'        => 'nullable|numeric|min:0',
            'items.*.amount'       => 'nullable|numeric|min:0',
            'items.*.account_id'      => 'required|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrder->update([
                'order_number'         => $request->order_number,
                'date_order'           => $request->date_order,
                'shipping_date'        => $request->shipping_date,
                'vendor_id'          => $request->vendor_id,
                'account_id' => $request->account_id,
                'location_id' => $request->location_id,
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
                    'tax_id'           => $item['tax_id'],
                    'tax_amount'              => $item['tax_amount'],
                    'amount'             => $item['amount'],
                    'account_id'         => $item['account_id'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_order.index')->with('success', 'Purchase order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = PurchaseOrder::with(['details', 'invoices'])->findOrFail($id);

                // 🚫 Cek apakah sudah dipakai di Invoice
                if ($order->invoices()->exists()) {
                    throw new \Exception("PO ini sudah digunakan dalam Purchase Invoice, tidak bisa dihapus.");
                }

                // ✅ Kalau aman, hapus (details ikut terhapus otomatis via cascade)
                $order->delete();
            });

            return redirect()->route('purchase_order.index')->with('success', 'Purchase Order berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('purchase_order.index')->with('error', $e->getMessage());
        }
    }
}

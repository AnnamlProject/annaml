<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Customers;
use App\Employee;
use App\Item;
use App\PaymentMethod;
use App\Project;
use App\SalesInvoice;
use App\SalesInvoiceDetail;
use App\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    //

    public function index()
    {
        $data = SalesInvoice::with(['customer', 'jenisPembayaran', 'salesOrder', 'salesPerson'])->latest()->paginate(10);
        return view('sales_invoice.index', compact('data'));
    }
    public function create()
    {
        $customer = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $employee = Employee::all();
        $items = Item::all(); // semua item yang bisa dipilih
        $accounts = chartOfAccount::all();
        $sales_order = SalesOrder::all();
        $project = Project::all();

        return view('sales_invoice.create', compact('customer', 'jenis_pembayaran', 'employee', 'items', 'accounts', 'sales_order', 'project'));
    }

    public function getItemsFromSalesOrder($salesOrderId)
    {
        $salesOrder = SalesOrder::with('details.item', 'details.account')->findOrFail($salesOrderId);


        return response()->json([
            'items' => $salesOrder->details->map(function ($detail) {
                return [
                    'id' => $detail->item->id, // ✅ tambahkan ini
                    'item_number'  => $detail->item->item_number ?? '',
                    'item_id' => $detail->item->item_id,
                    'description' => $detail->item_description,
                    'quantity' => $detail->quantity,
                    'order' => $detail->order,
                    'back_order' => $detail->back_order,
                    'unit' => $detail->item->unit,
                    'base_price' => $detail->item->base_price,
                    'discount' => $detail->discount ?? 0,
                    'price' => $detail->price ?? 0,
                    'amount' => $detail->amount ?? 0,
                    'tax' => $detail->tax ?? 0,
                    'account_id' => $detail->account_id,
                    'account_name' => $detail->account->nama_akun ?? '',
                ];
            }),
        ]);
    }
    private function generateKodeInvoice()
    {
        $last = \App\SalesInvoice::orderBy('invoice_number', 'desc')->first();

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
                'invoice_number' => 'unique:sales_invoices,invoice_number',
            ]);
            $invoice_number = $request->invoice_number;
        }

        // ✅ VALIDASI FORM UTAMA & ITEM
        $request->validate([
            'invoice_date'           => 'required|date',
            'shipping_date'          => 'required|date',
            'customers_id'            => 'required|exists:customers,id',
            'sales_person_id'        => 'required|exists:employees,id',
            'sales_order_id'        => 'nullable|exists:sales_orders,id',
            'jenis_pembayaran_id'    => 'required|exists:payment_methods,id',
            'shipping_address'       => 'required|string',
            'freight'                => 'required|numeric|min:0',
            'early_payment_terms'    => 'nullable|string',
            'messages'               => 'nullable|string',

            // Validasi item detail
            'items'                  => 'nullable|array|min:1',
            'items.*.item_id'        => 'nullable|exists:items,id',
            'items.*.quantity'       => 'nullable|numeric|min:0',
            'items.*.order_quantity'          => 'nullable|numeric|min:0',
            'items.*.back_order'     => 'nullable|numeric|min:0',
            'items.*.unit'           => 'nullable|string',
            'items.*.description'    => 'nullable|string',
            'items.*.base_price'     => 'nullable|numeric|min:0',
            'items.*.discount'       => 'nullable|numeric|min:0',
            'items.*.price'          => 'nullable|numeric|min:0',
            'items.*.amount'         => 'nullable|numeric|min:0',
            'items.*.tax'            => 'nullable|numeric|min:0',
            'items.*.account_id'        => 'nullable|exists:chart_of_accounts,id',
            'items.*.project_id'        => 'nullable|exists:projects,id',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Simpan ke tabel sales_invoices
            $salesInvoice = SalesInvoice::create([
                'invoice_number'       => $invoice_number,
                'invoice_date'         => $request->invoice_date,
                'shipping_date'        => $request->shipping_date,
                'customers_id'          => $request->customers_id,
                'sales_order_id'       => $request->sales_order_id,
                'sales_person_id'      => $request->sales_person_id,
                'jenis_pembayaran_id'  => $request->jenis_pembayaran_id,
                'shipping_address'     => $request->shipping_address,
                'freight'              => $request->freight,
                'early_payment_terms'  => $request->early_payment_terms,
                'messages'             => $request->messages,
            ]);

            // ✅ Simpan ke tabel sales_invoice_details
            foreach ($request->items as $item) {
                SalesInvoiceDetail::create([
                    'sales_invoice_id'   => $salesInvoice->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'order_quantity'              => $item['order_quantity'],
                    'back_order'         => $item['back_order'] ?? 0,
                    'unit'               => $item['unit'],
                    'description'   => $item['description'],
                    'base_price'         => $item['base_price'] ?? 0,
                    'discount'           => $item['discount'] ?? 0,
                    'price'              => $item['price'] ?? 0,
                    'amount'             => $item['amount'] ?? 0,
                    'tax'                => $item['tax'] ?? 0,
                    'account_id'         => $item['account_id'],
                    'project_id'         => $item['project_id'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales_invoice.index')->with('success', 'Sales invoice berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()])->withInput();
        }
    }
    public function show($id)
    {
        // Ambil sales order beserta relasi terkait
        $salesInvoice = SalesInvoice::with([
            'customer',
            'salesPerson',
            'jenisPembayaran',
            'details.item',
            'details.account'
        ])->findOrFail($id);

        return view('sales_invoice.show', compact('salesInvoice'));
    }

    public function edit($id)
    {
        $salesInvoice = SalesInvoice::with('details')->findOrFail($id);
        $customers = Customers::all();
        $employees = Employee::all();
        $jenis_pembayaran = PaymentMethod::all();
        $project = Project::all();
        $items = Item::all(); // semua item yang bisa dipilih


        return view('sales_invoice.edit', compact('salesInvoice', 'customers', 'employees', 'jenis_pembayaran', 'items', 'project'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'shipping_date' => 'required|date',
            'customers_id' => 'required|exists:customers,id',
            'employee_id' => 'required|exists:employees,id',
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'shipping_address' => 'required|string',
            'freight' => 'required|numeric',
            'early_payment_terms' => 'required|string',



            'items' => 'nullable|array',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.order_quantity' => 'nullable|numeric|min:0',
            'items.*.back_order' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
            'items.*.description' => 'nullable|string',
            'items.*.base_price' => 'nullable|numeric',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.account' => 'nullable|exists:chart_of_accounts,id',
            'items.*.project' => 'nullable|exists:projects,id',
        ]);

        DB::beginTransaction();

        try {
            $salesInvoice = SalesInvoice::findOrFail($id);
            $salesInvoice->update([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'shipping_date' => $request->shipping_date,
                'customers_id' => $request->customers_id,
                'employee_id' => $request->employee_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'shipping_address' => $request->shipping_address,
                'freight' => $request->freight,
                'early_payment_terms' => $request->early_payment_terms,
                'messages' => $request->messages,
            ]);

            // Hapus detail lama dan simpan ulang
            SalesInvoiceDetail::where('sales_invoice_id', $salesInvoice->id)->delete();

            foreach ($request->items as $item) {
                SalesInvoiceDetail::create([
                    'sales_invoice_id' => $salesInvoice->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'order_quantity' => $item['order_quantity'],
                    'back_order' => $item['back_order'] ?? 0,
                    'unit' => $item['unit'],
                    'description' => $item['description'],
                    'base_price' => $item['base_price'],
                    'discount' => $item['discount'],
                    'price' => $item['price'],
                    'amount' => $item['amount'],
                    'tax' => $item['tax'],
                    'account_id' => $item['account'],
                    'project_id' => $item['project'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales_invoice.index')->with('success', 'Sales Invoice berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Ambil invoice beserta detail
            $invoice = SalesInvoice::with('details')->findOrFail($id);

            // Hapus semua detail terlebih dahulu
            $invoice->details()->delete();

            // Hapus invoice utamanya
            $invoice->delete();

            DB::commit();

            return redirect()->route('sales_invoice.index')->with('success', 'Sales Invoice berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

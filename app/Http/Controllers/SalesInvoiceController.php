<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Customers;
use App\Employee;
use App\Item;
use App\LocationInventory;
use App\PaymentMethod;
use App\Project;
use App\SalesInvoice;
use App\SalesInvoiceDetail;
use App\SalesOrder;
use App\SalesTaxes;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $sales_taxes = SalesTaxes::all();
        $freightAccount = \App\linkedAccounts::with('akun')
            ->where('kode', 'Freight Revenue')
            ->first();
        $lokasi_inventory = LocationInventory::all();

        return view('sales_invoice.create', compact('customer', 'jenis_pembayaran', 'employee', 'items', 'accounts', 'sales_order', 'project', 'sales_taxes', 'freightAccount', 'lokasi_inventory'));
    }

    public function getItemsFromSalesOrder($salesOrderId)
    {
        $salesOrder = SalesOrder::with([
            'details.item.quantities',
            'details.item.accounts.cogsAccount',
            'details.item.accounts.assetAccount',
            'details.account',
            'details.sales_taxes.salesAccount'
        ])->findOrFail($salesOrderId);

        $locationId = $salesOrder->location_id; // 🔹 ambil lokasi dari SO header

        return response()->json([
            'items' => $salesOrder->details->map(function ($detail) use ($locationId) {
                $item = $detail->item;

                // 🔹 filter qty & value sesuai location_id
                $onHandQty   = $item->quantities->where('location_id', $locationId)->sum('on_hand_qty') ?: 1;
                $onHandValue = $item->quantities->where('location_id', $locationId)->sum('on_hand_value');
                $unitCost    = $onHandQty > 0 ? $onHandValue / $onHandQty : 0;

                return [
                    'id'                  => $item->id,
                    'item_number'         => $item->item_number ?? '',
                    'item_name'           => $item->item_name ?? '',
                    'description'         => $detail->item_description,
                    'quantity'            => $detail->quantity,
                    'order'               => $detail->order,
                    'back_order'          => $detail->back_order,
                    'unit'                => $item->units->unit_of_measure ?? $detail->unit,
                    'base_price'          => $detail->base_price,
                    'discount'            => $detail->discount ?? 0,
                    'price'               => $detail->price ?? 0,
                    'amount'              => $detail->amount ?? 0,

                    // Pajak
                    'tax_id'              => $detail->tax_id ?? null,
                    'tax_rate'            => optional($detail->sales_taxes)->rate ?? 0,
                    'tax_sales_account_id' => optional($detail->sales_taxes)->sales_account_id ?? null,
                    'tax_sales_account_name' => optional(optional($detail->sales_taxes)->salesAccount)->nama_akun ?? '',

                    // Pendapatan (dari detail.account)
                    'account_id'          => $detail->account_id,
                    'account_name'        => $detail->account->nama_akun ?? '',

                    // Tambahan untuk HPP
                    'type'                => $item->type,
                    'unit_cost'           => $unitCost,
                    'cogs_account_id'     => $item->accounts->cogs_account_id ?? null,
                    'cogs_account_name'   => optional($item->accounts->cogsAccount)->nama_akun ?? 'COGS',
                    'asset_account_id'    => $item->accounts->asset_account_id ?? null,
                    'asset_account_name'  => optional($item->accounts->assetAccount)->nama_akun ?? 'Inventory',
                ];
            }),
        ]);
    }

    private function generateKodeOrder($dateOrder)
    {
        // Formatkan tanggal order sesuai kebutuhan (misal YYYYMMDD)
        $dateFormatted = \Carbon\Carbon::parse($dateOrder)->format('Ymd');

        $prefix = 'SI-' . $dateFormatted . '-';

        // Cari order terakhir berdasarkan tanggal order yg sama
        $last = \App\SalesInvoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($last && preg_match('/' . $prefix . '(\d+)/', $last->invoice_number, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1) Header
            $salesInvoice = \App\SalesInvoice::create([
                'invoice_number'      => $this->generateKodeOrder($request->invoice_date),
                'invoice_date'        => $request->invoice_date,
                'shipping_date'       => $request->shipping_date,
                'customers_id'        => $request->customers_id,
                'sales_order_id'      => $request->sales_order_id,
                'sales_person_id'     => $request->sales_person_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'shipping_address'    => $request->shipping_address,
                'freight'             => (float) str_replace(',', '', $request->freight),
                'early_payment_terms' => $request->early_payment_terms,
                'messages'            => $request->messages,
                'status_sales_invoice' => 1,
            ]);
            // dump('📌 SalesInvoice tersimpan:', $salesInvoice->toArray());


            // 3b) Update status PO
            if ($request->sales_order_id) {
                \App\SalesOrder::whereKey($request->sales_order_id)
                    ->update(['status_sales' => 2]);
                // dump('Purchase Order update status_sales=2:', $request->sales_order_id);
            }
            // 2) Detail + kalkulasi ter-cache
            $lines = [];  // simpan angka bersih untuk jurnal & stok

            foreach ($request->items as $row) {
                $base_price = (float) str_replace(',', '', $row['base_price']  ?? 0);
                $discount   = (float) str_replace(',', '', $row['discount']    ?? 0);
                $price      = (float) str_replace(',', '', $row['price']       ?? 0);
                $amount     = (float) str_replace(',', '', $row['amount']      ?? 0);
                // konsolidasikan pajak: sumbernya tax_value dari form
                $tax_amount = (float) str_replace(',', '', $row['tax_value']   ?? 0);
                $tax_id     = $row['tax_id'] ?? null;

                // qty yang dipakai untuk pengiriman/HPP adalah order_quantity (bukan quantity total SO)
                $shippedQty = (float) str_replace(',', '', $row['order_quantity'] ?? 0);

                $detail = \App\SalesInvoiceDetail::create([
                    'sales_invoice_id' => $salesInvoice->id,
                    'item_id'          => $row['item_id'],
                    'quantity'         => (float) str_replace(',', '', $row['quantity'] ?? 0),
                    'order_quantity'   => $shippedQty,                 // << dipakai utk HPP
                    'back_order'       => (float) str_replace(',', '', $row['back_order'] ?? 0),
                    'unit'             => $row['unit'],
                    'description'      => $row['description'],
                    'base_price'       => $base_price,
                    'discount'         => $discount,
                    'price'            => $price,
                    'amount'           => $amount,
                    'tax'              => $tax_amount,                 // simpan nilai pajak yg sama
                    'account_id'       => $row['account_id'],
                    'project_id'       => $row['project_id'],
                    'tax_id'           => $tax_id,
                ]);
                // dump("📝 Detail tersimpan untuk item {$row['item_id']}:", $detail->toArray());

                // Ambil unit cost SEBELUM stok disesuaikan
                $iq = \App\ItemQuantities::where('item_id', $row['item_id'])
                    ->where('location_id', $request->location_id)
                    ->first();

                $unitCost = ($iq && $iq->on_hand_qty > 0)
                    ? ($iq->on_hand_value / $iq->on_hand_qty)
                    : 0.0;

                $cogsAmount   = $shippedQty * $unitCost;
                $qtyChange    = -$shippedQty;
                $valueChange  = -$cogsAmount;

                // Simpan untuk jurnal (jangan hitung ulang sesudah stok berubah)
                $lines[] = [
                    'item_id'      => $row['item_id'],
                    'desc'         => $row['description'],
                    'revenue_acct' => $row['account_id'],
                    'revenue_amt'  => $amount,
                    'tax_id'       => $tax_id,
                    'tax_amount'   => $tax_amount,
                    'shipped_qty'  => $shippedQty,
                    'unit_cost'    => $unitCost,
                    'cogs_amount'  => $cogsAmount,
                ];

                // Kurangi stok berdasarkan shippedQty (order_quantity)
                $this->adjustItemQuantity($row['item_id'], $request->location_id, $qtyChange, $valueChange);

                // dump("📦 Update Inventory untuk Item #{$row['item_id']}", [
                //     'qty_before'   => optional($iq)->on_hand_qty,
                //     'value_before' => optional($iq)->on_hand_value,
                //     'qtyChange'    => $qtyChange,
                //     'valueChange'  => $valueChange,
                //     'unitCost'     => $unitCost,
                //     'qty_after'    => (optional($iq)->on_hand_qty ?? 0) + $qtyChange,
                //     'value_after'  => (optional($iq)->on_hand_value ?? 0) + $valueChange,
                // ]);
            }

            // 3) Jurnal: pakai angka cache $lines
            $journal = \App\JournalEntry::create([
                'source'  => 'sales_invoice',
                'tanggal' => $request->invoice_date,
                'comment' => "Sales Invoice #{$salesInvoice->invoice_number}",
            ]);
            // dump('📒 Journal Entry tersimpan:', $journal->toArray());

            $coaCode = fn($id) => $id ? \App\ChartOfAccount::whereKey($id)->value('kode_akun') : null;

            foreach ($lines as $ln) {
                // Revenue (Credit)
                if ($ln['revenue_amt'] > 0 && !empty($ln['revenue_acct'])) {
                    $rev = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun'        => $coaCode($ln['revenue_acct']),
                        'debits'           => 0,
                        'credits'          => $ln['revenue_amt'],
                        'comment'          => $ln['desc'],
                    ]);
                    // dump('💰 Journal Revenue:', $rev->toArray());
                }

                // COGS (Debit) & Inventory (Credit) — pakai angka cache
                if ($ln['cogs_amount'] > 0) {
                    $cogsAcctId   = \App\ItemAccount::where('item_id', $ln['item_id'])->value('cogs_account_id');
                    $assetAcctId  = \App\ItemAccount::where('item_id', $ln['item_id'])->value('asset_account_id');

                    $cogs = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun'        => $coaCode($cogsAcctId),
                        'debits'           => $ln['cogs_amount'],
                        'credits'          => 0,
                        'comment'          => 'COGS ' . $ln['desc'],
                    ]);
                    // dump('📊 Journal COGS:', $cogs->toArray());

                    $inv = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun'        => $coaCode($assetAcctId),
                        'debits'           => 0,
                        'credits'          => $ln['cogs_amount'],
                        'comment'          => 'Inventory Out ' . $ln['desc'],
                    ]);
                    // dump('📦 Journal Inventory Out:', $inv->toArray());
                }

                // PPN Keluaran (Credit) — konsisten pakai tax_value/tax_amount
                if ($ln['tax_amount'] > 0 && !empty($ln['tax_id'])) {
                    $taxSalesAcctId = \App\SalesTaxes::whereKey($ln['tax_id'])->value('sales_account_id');
                    $tax = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun'        => $coaCode($taxSalesAcctId),
                        'debits'           => 0,
                        'credits'          => $ln['tax_amount'],
                        'comment'          => 'PPN Keluaran',
                    ]);
                    // dump('🧾 Journal Pajak:', $tax->toArray());
                }
            }

            // Freight (Credit)
            if ((float) $salesInvoice->freight > 0) {
                $freightLinked = \App\LinkedAccounts::where('kode', 'Freight Revenue')->first();
                $freight = \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun'        => $coaCode(optional($freightLinked)->akun_id),
                    'debits'           => 0,
                    'credits'          => (float) $salesInvoice->freight,
                    'comment'          => 'Freight Revenue',
                ]);
                // dump('🚚 Journal Freight:', $freight->toArray());
            }

            // Payment (Debit) = total revenue + tax + freight
            $grandTotal = array_reduce($lines, fn($acc, $ln) => $acc + $ln['revenue_amt'] + $ln['tax_amount'], 0.0)
                + (float) $salesInvoice->freight;

            if ($grandTotal > 0) {
                $pmDetail = \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)
                    ->where('is_default', 1)->first()
                    ?? \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)->first();

                $payment = \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun'        => $coaCode(optional($pmDetail)->account_id),
                    'debits'           => $grandTotal,
                    'credits'          => 0,
                    'comment'          => 'Payment / Debit',
                ]);
                // dump('🏦 Journal Payment:', $payment->toArray());
            }

            DB::commit();
            return redirect()->route('sales_invoice.index')->with('success', 'Sales invoice berhasil disimpan.');
            // dd('✅ Semua proses selesai tanpa error.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd('❌ Error:', $e->getMessage(), $e->getTraceAsString());
        }
    }


    protected function adjustItemQuantity($itemId, $locationId, $qtyChange, $valueChange)
    {
        $itemQty = \App\ItemQuantities::firstOrCreate(
            ['item_id' => $itemId, 'location_id' => $locationId],
            ['on_hand_qty' => 0, 'on_hand_value' => 0]
        );

        // sebelum update (optional untuk debugging)
        Log::debug("Adjusting stock", [
            'item_id'      => $itemId,
            'location_id'  => $locationId,
            'qty_before'   => $itemQty->on_hand_qty,
            'value_before' => $itemQty->on_hand_value,
            'qty_change'   => $qtyChange,
            'value_change' => $valueChange,
        ]);

        // update stok
        $itemQty->on_hand_qty   += $qtyChange;
        $itemQty->on_hand_value += $valueChange;

        // jaga jangan sampai negatif (opsional)
        if ($itemQty->on_hand_qty < 0) {
            $itemQty->on_hand_qty = 0;
        }
        if ($itemQty->on_hand_value < 0) {
            $itemQty->on_hand_value = 0;
        }

        $itemQty->save();

        // sesudah update (optional untuk debugging)
        Log::debug("Stock updated", [
            'item_id'      => $itemId,
            'location_id'  => $locationId,
            'qty_after'    => $itemQty->on_hand_qty,
            'value_after'  => $itemQty->on_hand_value,
        ]);
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
        DB::beginTransaction();

        try {
            $salesInvoice = \App\SalesInvoice::findOrFail($id);

            // 1) Update header
            $salesInvoice->update([
                'invoice_date'        => $request->invoice_date,
                'shipping_date'       => $request->shipping_date,
                'customers_id'        => $request->customers_id,
                'sales_order_id'      => $request->sales_order_id,
                'sales_person_id'     => $request->sales_person_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'shipping_address'    => $request->shipping_address,
                'freight'             => (float) str_replace(',', '', $request->freight),
                'early_payment_terms' => $request->early_payment_terms,
                'messages'            => $request->messages,
            ]);
            dump('Invoice diupdate:', $salesInvoice->toArray());

            // 2) Reset detail lama
            \App\SalesInvoiceDetail::where('sales_invoice_id', $salesInvoice->id)->delete();

            // 3) Insert detail baru + update stok
            foreach ($request->items as $row) {
                $detail = \App\SalesInvoiceDetail::create([
                    'sales_invoice_id' => $salesInvoice->id,
                    'item_id'          => $row['item_id'],
                    'quantity'         => (float) str_replace(',', '', $row['quantity']),
                    'order_quantity'   => (float) str_replace(',', '', $row['order_quantity']),
                    'back_order'       => (float) str_replace(',', '', $row['back_order'] ?? 0),
                    'unit'             => $row['unit'],
                    'description'      => $row['description'],
                    'base_price'       => (float) str_replace(',', '', $row['base_price']),
                    'discount'         => (float) str_replace(',', '', $row['discount']),
                    'price'            => (float) str_replace(',', '', $row['price']),
                    'amount'           => (float) str_replace(',', '', $row['amount']),
                    'tax'              => (float) str_replace(',', '', $row['tax_value']),
                    'account_id'       => $row['account_id'],
                    'project_id'       => $row['project_id'],
                ]);
                dump('Detail updated:', $detail->toArray());

                // stok keluar tetap sama logika dengan store()
            }

            // 4) Reset jurnal lama
            $journal = \App\JournalEntry::where('source', 'sales_invoice')
                ->where('comment', "Sales Invoice #{$salesInvoice->invoice_number}")
                ->first();

            if ($journal) {
                \App\JournalEntryDetail::where('journal_entry_id', $journal->id)->delete();
                dump('Journal lama dihapus:', $journal->id);
            }

            // 5) Insert jurnal baru (sama persis seperti store, tapi semua angka distel pakai str_replace)
            // ...
            // (isi sesuai logika debit/credit revenue, tax, cogs, inventory)

            DB::commit();
            return redirect()->route('sales_invoice.index')
                ->with('success', 'Sales Invoice berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd('❌ Error update:', $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $invoice = \App\SalesInvoice::with('details')->findOrFail($id);

            // 🔹 Rollback stok dulu
            foreach ($invoice->details as $detail) {
                $qty   = (float) str_replace(',', '', $detail->quantity);
                $price = (float) str_replace(',', '', $detail->price);
                $value = $qty * $price;

                $this->adjustItemQuantity($detail->item_id, $invoice->location_id, $qty, $value);
                // dump("Rollback stok item_id={$detail->item_id}", [
                //     'qty_dikembalikan'   => $qty,
                //     'value_dikembalikan' => $value,
                // ]);
            }

            // 🔹 Hapus Journal Entry
            $journal = \App\JournalEntry::where('source', 'sales_invoice')
                ->where('comment', "Sales Invoice #{$invoice->invoice_number}")
                ->first();

            if ($journal) {
                \App\JournalEntryDetail::where('journal_entry_id', $journal->id)->delete();
                $journal->delete();
                // dump('Journal dihapus:', $journal->id);
            }

            // 🔹 Hapus detail
            \App\SalesInvoiceDetail::where('sales_invoice_id', $invoice->id)->delete();

            // 🔹 Hapus header
            $invoice->delete();

            DB::commit();
            return redirect()->route('sales_invoice.index')
                ->with('success', 'Sales Invoice berhasil dihapus beserta jurnal & rollback stok.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    public function exportPdf($id)
    {
        $salesInvoice = SalesInvoice::with('details.item', 'customer', 'jenisPembayaran', 'salesPerson', 'salesOrder', 'details.project')->findOrFail($id);

        $pdf = Pdf::loadView('sales_invoice.pdf', compact('salesInvoice'));
        return $pdf->download('SalesInvoice-' . $salesInvoice->invoice_number . '.pdf');
    }
}

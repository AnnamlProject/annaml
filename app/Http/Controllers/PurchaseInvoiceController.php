<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Customers;
use App\Item;
use App\JournalEntry;
use App\JournalEntryDetail;
use App\linkedAccounts;
use App\LocationInventory;
use App\PaymentMethod;
use App\Project;
use App\PurchaseInvoice;
use App\PurchaseInvoiceDetail;
use App\PurchaseOrder;
use App\SalesTaxes;
use App\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    //
    public function index()
    {
        $data = PurchaseInvoice::with(['jenisPembayaran', 'vendor'])->paginate(10);
        return view('purchase_invoice.index', compact('data'));
    }
    public function create()
    {
        $jenis_pembayaran = PaymentMethod::all();
        $items = Item::all();
        $accounts = chartOfAccount::all();
        $purchase_order = PurchaseOrder::all();
        $project = Project::all();
        $vendor = Vendors::all();
        $sales_taxes = SalesTaxes::all();
        $freightAccount = \App\linkedAccounts::with('akun')
            ->where('kode', 'Freight Expense')
            ->first();
        $lokasi_inventory = LocationInventory::all();
        return view('purchase_invoice.create', compact('jenis_pembayaran',  'items', 'accounts', 'purchase_order', 'project', 'vendor', 'sales_taxes', 'freightAccount', 'lokasi_inventory'));
    }

    public function getItemsFromPurchaseOrder($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::with([
            'details.item',
            'details.account',
            'details.sales_taxes'
        ])->findOrFail($purchaseOrderId);

        return response()->json([
            'items' => $purchaseOrder->details->map(function ($detail) {
                return [
                    'id'           => $detail->item->id ?? null,
                    'item_number'  => $detail->item->item_number ?? '',
                    'description'  => $detail->item_description ?? '',
                    'quantity'     => $detail->quantity ?? 0,
                    'order'        => $detail->order ?? 0,
                    'back_order'   => $detail->back_order ?? 0,
                    'unit'         => $detail->unit ?? '',
                    'price'        => $detail->price ?? 0,
                    'tax_id'       => $detail->tax_id ?? null, // ambil ID pajak
                    'tax_rate'     => optional($detail->sales_taxes)->rate ?? 0, // ambil rate pajak
                    'tax_purchase_account_id'   => optional($detail->sales_taxes)->purchase_account_id ?? null,
                    'tax_purchase_account_name' => optional(optional($detail->sales_taxes)->purchaseAccount)->nama_akun ?? '',
                    'tax_amount'   => $detail->tax_amount ?? 0,
                    'amount'       => $detail->amount ?? 0,
                    'account_id'   => $detail->account_id ?? null,
                    'account_name' => $detail->account->nama_akun ?? '',
                ];
            })->values(),
        ]);
    }

    private function generateKodeInvoice($dateOrder)
    {
        $dateFormatted = \Carbon\Carbon::parse($dateOrder)->format('Ymd');

        $prefix = 'INV-' . $dateFormatted . '-';

        // Cari order terakhir berdasarkan tanggal order yg sama
        $last = \App\PurchaseInvoice::where('invoice_number', 'like', $prefix . '%')
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
        // 0) Nomor invoice (auto/manual)
        if ($request->has('auto_generate')) {
            $invoice_number = $this->generateKodeInvoice($request->date_invoice);
        } else {
            $request->validate(['invoice_number' => 'unique:purchase_invoices,invoice_number']);
            $invoice_number = $request->invoice_number;
        }

        // 1) Normalisasi payload
        $payload = $request->all();

        $payload['freight'] = isset($payload['freight'])
            ? floatval(str_replace(',', '', $payload['freight']))
            : 0;

        $payload['items'] = collect($payload['items'] ?? [])->map(function ($row) {
            foreach (['price', 'tax', 'tax_amount', 'amount', 'quantity', 'order', 'back_order'] as $f) {
                if (isset($row[$f])) {
                    $row[$f] = $row[$f] === '' ? null : floatval(str_replace(',', '', $row[$f]));
                }
            }

            if (!empty($row['account_id']) && !is_numeric($row['account_id'])) {
                $id = \App\ChartOfAccount::where('kode_akun', $row['account_id'])->value('id');
                $row['account_id'] = $id ?: null;
            }

            if (isset($row['account_id']) && intval($row['account_id']) === 0) {
                $row['account_id'] = null;
            }

            return $row;
        })->all();

        $request->replace($payload);

        // 2) Validasi
        $request->validate([
            'date_invoice'        => 'required|date',
            'shipping_date'       => 'required|date',
            'vendor_id'           => 'required|exists:vendors,id',
            'header_account_id'   => 'required|exists:payment_method_details,id',
            'purchase_order_id'   => 'nullable|exists:purchase_orders,id',
            'location_id'   => 'nullable|exists:location_inventories,id',
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'shipping_address'    => 'required|string',
            'freight'             => 'required|numeric|min:0',
            'items'               => 'nullable|array|min:1',
            'items.*.item_id'     => 'nullable|exists:items,id',
            'items.*.account_id'  => 'nullable|integer|exists:chart_of_accounts,id',
            'items.*.tax_id'      => 'nullable|integer|exists:sales_taxes,id',
        ]);

        DB::beginTransaction();
        try {
            // 3) Simpan header
            $purchaseInvoice = \App\PurchaseInvoice::create([
                'invoice_number'      => $invoice_number,
                'date_invoice'        => $request->date_invoice,
                'shipping_date'       => $request->shipping_date,
                'vendor_id'           => $request->vendor_id,
                'account_id'          => $request->header_account_id,
                'purchase_order_id'   => $request->purchase_order_id,
                'location_id' => $request->location_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'shipping_address'    => $request->shipping_address,
                'freight'             => $request->freight,
                'early_payment_terms' => $request->early_payment_terms,
                'messages'            => $request->messages,
            ]);
            // dump('Purchase Invoice tersimpan:', $purchaseInvoice->toArray());

            // 3b) Update status PO
            if ($request->purchase_order_id) {
                \App\PurchaseOrder::whereKey($request->purchase_order_id)
                    ->update(['status_purchase' => 1]);
                // dump('Purchase Order update status_purchase=1:', $request->purchase_order_id);
            }

            // 4) Simpan detail
            foreach ($request->items ?? [] as $row) {
                $detail = \App\PurchaseInvoiceDetail::create([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'item_id'           => $row['item_id'],
                    'quantity'          => $row['quantity'],
                    'order'             => $row['order'],
                    'back_order'        => $row['back_order'] ?? 0,
                    'unit'              => $row['unit'],
                    'item_description'  => $row['item_description'],
                    'price'             => $row['price'] ?? 0,
                    'tax_id'            => $row['tax_id'],
                    'tax_amount'        => $row['tax_amount'] ?? 0,
                    'amount'            => $row['amount'] ?? 0,
                    'account_id'        => $row['account_id'],
                    'project_id'        => $row['project_id'] ?? null,
                ]);
                // dump('Detail tersimpan:', $detail->toArray());

                // ✅ Update stok di lokasi invoice
                $qtyChange   = $row['quantity'] ?? 0;
                $valueChange = ($row['quantity'] ?? 0) * ($row['price'] ?? 0);

                $this->adjustItemQuantity($row['item_id'], $request->location_id, $qtyChange, $valueChange);
            }

            // 5) Jurnal header
            $journal = \App\JournalEntry::create([
                'source' => 'purchase_invoice',
                'tanggal' => $request->date_invoice,
                'comment' => "Purchase Invoice #{$purchaseInvoice->invoice_number}",
            ]);
            // dump('Journal Entry tersimpan:', $journal->toArray());

            $coaCode = fn($id) => $id ? \App\ChartOfAccount::whereKey($id)->value('kode_akun') : null;

            // 6) Jurnal debit items & PPN
            foreach ($request->items ?? [] as $row) {
                if (!empty($row['amount']) && $row['amount'] > 0 && !empty($row['account_id'])) {
                    $kodeAkun = $coaCode($row['account_id']);
                    $jd = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun' => $kodeAkun,
                        'debits' => $row['amount'],
                        'credits' => 0,
                        'comment' => $row['item_description'] ?? null,
                    ]);
                    // dump('Journal Debit Item:', $jd->toArray());
                }

                if (!empty($row['tax_amount']) && $row['tax_amount'] > 0 && !empty($row['tax_id'])) {
                    $taxAccountId = \App\SalesTaxes::whereKey($row['tax_id'])->value('purchase_account_id');
                    $kodeAkunPpn  = $coaCode($taxAccountId);

                    if (!$kodeAkunPpn) {
                        throw new \Exception("SalesTax ID {$row['tax_id']} tidak punya purchase_account_id valid");
                    }

                    $jp = \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun' => $kodeAkunPpn,
                        'debits' => $row['tax_amount'],
                        'credits' => 0,
                        'comment' => 'PPN ' . ($row['tax'] ?? '') . '%',
                    ]);
                    // dump('Journal Debit PPN:', $jp->toArray());
                }
            }

            // 7) Freight (debit)
            if ($request->freight > 0) {
                $freightLinked = \App\linkedAccounts::where('kode', 'Freight Expense')->first();
                $freightKode   = $coaCode(optional($freightLinked)->akun_id);

                $jf = \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun' => $freightKode,
                    'debits' => $request->freight,
                    'credits' => 0,
                    'comment' => 'Freight Expense',
                ]);
                // dump('Journal Debit Freight:', $jf->toArray());
            }

            // 8) Kredit (kas/bank/hutang)
            $grandTotal = collect($request->items)->sum('amount')
                + collect($request->items)->sum('tax_amount')
                + ($request->freight ?? 0);

            if ($grandTotal > 0) {
                $pmDetail = \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)
                    ->where('is_default', 1)->first()
                    ?? \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)->first();

                $pmKode = $coaCode(optional($pmDetail)->account_id);
                if (!$pmKode) {
                    throw new \Exception("Payment Method ID {$request->jenis_pembayaran_id} tidak punya akun default");
                }

                $jc = \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun' => $pmKode,
                    'debits' => 0,
                    'credits' => $grandTotal,
                    'comment' => 'Payment / Credit',
                ]);
                // dump('Journal Credit Payment:', $jc->toArray());
            }

            DB::commit();
            // dd('✅ Semua proses selesai tanpa error.');
            return redirect()->route('purchase_invoice.index')->with('success', 'Purchase invoice berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd('❌ Error:', $e->getMessage(), $e->getTraceAsString(), [
                'request_items' => $request->items
            ]);
        }
    }
    protected function adjustItemQuantity($itemId, $locationId, $qtyChange, $valueChange)
    {
        $itemQty = \App\ItemQuantities::firstOrCreate(
            ['item_id' => $itemId, 'location_id' => $locationId],
            ['on_hand_qty' => 0, 'on_hand_value' => 0]
        );

        $itemQty->on_hand_qty   += $qtyChange;
        $itemQty->on_hand_value += $valueChange;
        $itemQty->save();

        return $itemQty;
    }

    public function show($id)
    {
        // Ambil sales order beserta relasi terkait
        $purchaseInvoice = PurchaseInvoice::with([
            'vendor',
            'jenisPembayaran',
            'details.item',
            'details.account',
            'details.sales_taxes'
        ])->findOrFail($id);


        return view('purchase_invoice.show', compact('purchaseInvoice'));
    }
    public function edit($id)
    {
        $purchaseInvoice = PurchaseInvoice::with('details')->findOrFail($id);
        $jenis_pembayaran = PaymentMethod::all();
        $vendor = Vendors::all();
        $items = Item::all(); // semua item yang bisa dipilih
        $sales_taxes = SalesTaxes::all();
        $project = Project::all();
        $freightAccount = \App\linkedAccounts::with('akun')
            ->where('kode', 'Freight Expense')
            ->first();
        $locationInventory = LocationInventory::all();


        return view('purchase_invoice.edit', compact('purchaseInvoice', 'vendor', 'jenis_pembayaran', 'items', 'sales_taxes', 'project', 'freightAccount', 'locationInventory'));
    }
    public function update(Request $request, $id)
    {
        // 1) Normalisasi payload
        $payload = $request->all();
        $payload['freight'] = isset($payload['freight'])
            ? floatval(str_replace(',', '', $payload['freight']))
            : 0;

        $payload['items'] = collect($payload['items'] ?? [])->map(function ($row) {
            foreach (['price', 'tax', 'tax_amount', 'amount', 'quantity', 'order', 'back_order'] as $f) {
                if (isset($row[$f])) {
                    $row[$f] = $row[$f] === '' ? null : floatval(str_replace(',', '', $row[$f]));
                }
            }
            return $row;
        })->all();

        $request->replace($payload);

        // 2) Validasi
        $request->validate([
            'date_invoice'      => 'required|date',
            'shipping_date'     => 'required|date',
            'vendor_id'         => 'required|exists:vendors,id',
            'header_account_id' => 'required|exists:payment_method_details,id',
            'location_id' => 'required|exists:location_inventories,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'shipping_address'  => 'required|string',
            'freight'           => 'required|numeric|min:0',

            'items'                  => 'nullable|array|min:1',
            'items.*.item_id'        => 'nullable|exists:items,id',
            'items.*.quantity'       => 'nullable|numeric|min:0',
            'items.*.order'          => 'nullable|numeric|min:0',
            'items.*.back_order'     => 'nullable|numeric|min:0',
            'items.*.price'          => 'nullable|numeric|min:0',
            'items.*.tax_id'         => 'nullable|numeric|exists:sales_taxes,id',
            'items.*.tax_amount'     => 'nullable|numeric|min:0',
            'items.*.amount'         => 'nullable|numeric|min:0',
            'items.*.account_id'     => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            // 3) Ambil invoice lama
            $purchaseInvoice = \App\PurchaseInvoice::findOrFail($id);

            // 4) Update header
            $purchaseInvoice->update([
                'date_invoice'        => $request->date_invoice,
                'shipping_date'       => $request->shipping_date,
                'vendor_id'           => $request->vendor_id,
                'location_id'           => $request->location_id,
                'account_id'          => $request->header_account_id,
                'purchase_order_id'   => $request->purchase_order_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'shipping_address'    => $request->shipping_address,
                'freight'             => $request->freight,
                'early_payment_terms' => $request->early_payment_terms,
                'messages'            => $request->messages,
            ]);
            // dump('Invoice diupdate:', $purchaseInvoice->toArray());

            // 5) Update status PO
            if ($request->purchase_order_id) {
                \App\PurchaseOrder::whereKey($request->purchase_order_id)
                    ->update(['status_purchase' => 1]);
                // dump('Purchase Order status_purchase = 1:', $request->purchase_order_id);
            }

            // 6) Reset detail lama

            // 6) Rollback detail lama dari stok
            $oldDetails = \App\PurchaseInvoiceDetail::where('purchase_invoice_id', $purchaseInvoice->id)->get();

            foreach ($oldDetails as $old) {
                $oldQty   = $old->quantity ?? 0;
                $oldValue = ($old->quantity ?? 0) * ($old->price ?? 0);

                // rollback stok
                $this->adjustItemQuantity($old->item_id, $purchaseInvoice->location_id, -$oldQty, -$oldValue);
            }

            \App\PurchaseInvoiceDetail::where('purchase_invoice_id', $purchaseInvoice->id)->delete();


            // Hapus detail lama
            \App\PurchaseInvoiceDetail::where('purchase_invoice_id', $purchaseInvoice->id)->delete();

            foreach ($request->items ?? [] as $row) {
                $detail = \App\PurchaseInvoiceDetail::create([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'item_id'             => $row['item_id'],
                    'quantity'            => $row['quantity'],
                    'order'               => $row['order'],
                    'back_order'          => $row['back_order'] ?? 0,
                    'unit'                => $row['unit'],
                    'item_description'    => $row['item_description'],
                    'price'               => $row['price'] ?? 0,
                    'tax_id'              => $row['tax_id'],
                    'tax_amount'          => $row['tax_amount'] ?? 0,
                    'amount'              => $row['amount'] ?? 0,
                    'account_id'          => $row['account_id'],
                    'project_id'          => $row['project_id'] ?? null,
                ]);

                // update stok dengan qty & value baru
                $newQty   = $row['quantity'] ?? 0;
                $newValue = ($row['quantity'] ?? 0) * ($row['price'] ?? 0);

                $this->adjustItemQuantity($row['item_id'], $request->location_id, $newQty, $newValue);
            }


            // 7) Reset Journal lama
            $journal = \App\JournalEntry::where('source', 'purchase_invoice')
                ->where('comment', "Purchase Invoice #{$purchaseInvoice->invoice_number}")
                ->first();

            if ($journal) {
                \App\JournalEntryDetail::where('journal_entry_id', $journal->id)->delete();
                // dump('Journal lama dihapus:', $journal->id);
            } else {
                $journal = \App\JournalEntry::create([
                    'source'  => 'purchase_invoice',
                    'tanggal' => $request->date_invoice,
                    'comment' => "Purchase Invoice #{$purchaseInvoice->invoice_number}",
                ]);
                // dump('Journal baru dibuat:', $journal->toArray());
            }

            // Helper ambil kode akun
            $coaCode = fn($id) => $id ? \App\ChartOfAccount::whereKey($id)->value('kode_akun') : null;

            // 8) Insert Journal baru
            foreach ($request->items ?? [] as $row) {
                if (!empty($row['amount']) && $row['amount'] > 0 && !empty($row['account_id'])) {
                    \App\JournalEntryDetail::create([
                        'journal_entry_id' => $journal->id,
                        'kode_akun'        => $coaCode($row['account_id']),
                        'debits'           => $row['amount'],
                        'credits'          => 0,
                        'comment'          => $row['item_description'] ?? null,
                    ]);
                }

                if (!empty($row['tax_amount']) && $row['tax_amount'] > 0 && !empty($row['tax_id'])) {
                    $salesTax = \App\SalesTaxes::find($row['tax_id']);
                    if ($salesTax && $salesTax->purchase_account_id) {
                        \App\JournalEntryDetail::create([
                            'journal_entry_id' => $journal->id,
                            'kode_akun'        => $coaCode($salesTax->purchase_account_id),
                            'debits'           => $row['tax_amount'],
                            'credits'          => 0,
                            'comment'          => 'PPN ' . ($row['tax'] ?? '') . '%',
                        ]);
                    }
                }
            }

            // Freight
            if ($request->freight > 0) {
                $freightLinked = \App\LinkedAccounts::where('kode', 'Freight Expense')->first();
                $freightKode   = $coaCode(optional($freightLinked)->akun_id);
                \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun'        => $freightKode,
                    'debits'           => $request->freight,
                    'credits'          => 0,
                    'comment'          => 'Freight Expense',
                ]);
            }

            // Credit lawan
            $grandTotal = collect($request->items)->sum('amount')
                + collect($request->items)->sum('tax_amount')
                + $request->freight;

            if ($grandTotal > 0) {
                $pmDetail = \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)
                    ->where('is_default', 1)
                    ->first()
                    ?? \App\PaymentMethodDetail::where('payment_method_id', $request->jenis_pembayaran_id)->first();

                $pmKode = $coaCode(optional($pmDetail)->account_id);

                \App\JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'kode_akun'        => $pmKode,
                    'debits'           => 0,
                    'credits'          => $grandTotal,
                    'comment'          => 'Payment / Credit',
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_invoice.index')
                ->with('success', 'Purchase Invoice berhasil diupdate beserta Journal.');
        } catch (\Throwable $e) {
            DB::rollBack();
            dd('❌ Error update:', $e->getMessage(), $e->getTraceAsString());
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $invoice = \App\PurchaseInvoice::with('details')->findOrFail($id);

            // 1) Rollback stok dari semua detail SEBELUM dihapus
            foreach ($invoice->details as $detail) {
                $qty   = $detail->quantity ?? 0;
                $value = ($detail->quantity ?? 0) * ($detail->price ?? 0);

                $this->adjustItemQuantity($detail->item_id, $invoice->location_id, -$qty, -$value);
            }

            // 2) Hapus Journal Entry
            $journal = \App\JournalEntry::where('source', 'purchase_invoice')
                ->where('comment', "Purchase Invoice #{$invoice->invoice_number}") // atau pakai source_id kalau ada
                ->first();

            if ($journal) {
                \App\JournalEntryDetail::where('journal_entry_id', $journal->id)->delete();
                $journal->delete();
            }

            // 3) Hapus detail invoice
            \App\PurchaseInvoiceDetail::where('purchase_invoice_id', $invoice->id)->delete();

            // 4) Hapus header invoice
            $invoice->delete();

            DB::commit();
            return redirect()->route('purchase_invoice.index')
                ->with('success', 'Purchase Invoice beserta jurnal & stok berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Gagal menghapus invoice: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Customers;
use App\jenis_pembayaran;
use App\PaymentMethod;
use App\SalesDeposit;
use App\SalesDepositDetail;
use App\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesDepositController extends Controller
{
    //
    public function index()
    {
        $data = SalesDeposit::with(['account', 'jenis_pembayaran', 'customer'])->latest()->paginate(5);
        return view('sales_deposits.index', compact('data'));
    }
    public function create()
    {
        $account = chartOfAccount::all();
        $customer = Customers::all();
        $jenis_pembayaran = PaymentMethod::all();
        $sales_invoices = SalesInvoice::all();

        return view('sales_deposits.create', compact('account', 'customer', 'jenis_pembayaran', 'sales_invoices'));
    }

    public function store(Request $request)
    {
        // Bersihkan format ribuan sebelum validasi
        $request->merge([
            'deposit_amount' => str_replace(',', '.', str_replace('.', '', $request->deposit_amount))
        ]);

        // Validasi input
        $validatedData = $request->validate([
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'customers_id' => 'required|exists:customers,id',
            'deposit_no' => 'required|string|unique:sales_deposits,deposit_no',
            'deposit_date' => 'required|date',
            'deposit_reference' => 'nullable|string',
            'comment' => 'nullable|string',
            'deposit_amount' => 'required|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.invoice_date' => 'nullable|date',
            'items.*.sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'items.*.original_amount' => 'nullable|numeric|min:0',
            'items.*.amount_owing' => 'nullable|numeric|min:0',
            'items.*.discount_available' => 'nullable|numeric|min:0',
            'items.*.discount_taken' => 'nullable|numeric|min:0',
            'items.*.amount_received' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Simpan ke sales_deposits
            $deposit = SalesDeposit::create([
                'deposit_no'             => $validatedData['deposit_no'],
                'deposit_date'           => $validatedData['deposit_date'],
                'jenis_pembayaran_id'    => $validatedData['jenis_pembayaran_id'],
                'account_id'             => $validatedData['account_id'],
                'customer_id'            => $validatedData['customers_id'],
                'deposit_reference'      => $validatedData['deposit_reference'] ?? null,
                'deposit_amount'         => $validatedData['deposit_amount'],
                'comment'                => $validatedData['comment'] ?? null,
            ]);

            // Simpan detail jika ada
            if (!empty($validatedData['items']) && is_array($validatedData['items'])) {
                foreach ($validatedData['items'] as $index => $item) {
                    if (!isset($item['amount_received']) || $item['amount_received'] <= 0) {
                        continue;
                    }

                    SalesDepositDetail::create([
                        'deposit_id'         => $deposit->id,
                        'sales_invoice_id'   => $item['sales_invoice_id'] ?? null,
                        'original_amount'    => $item['original_amount'] ?? 0,
                        'amount_owing'       => $item['amount_owing'] ?? 0,
                        'discount_available' => $item['discount_available'] ?? 0,
                        'discount_taken'     => $item['discount_taken'] ?? 0,
                        'invoice_date'       => $item['invoice_date'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sales_deposits.index')->with('success', 'Sales Deposit berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $deposit = SalesDeposit::with(['customer', 'jenis_pembayaran', 'account', 'details.invoice'])->findOrFail($id);
        return view('sales_deposits.show', compact('deposit'));
    }

    public function edit($id)
    {
        $sales_deposits = SalesDeposit::with('details')->findOrFail($id);
        $jenis_pembayaran = PaymentMethod::all();
        $account = ChartOfAccount::all();
        $customer = Customers::all();
        $sales_invoices = SalesInvoice::all();

        return view('sales_deposits.edit', compact(
            'sales_deposits',
            'jenis_pembayaran',
            'account',
            'customer',
            'sales_invoices'
        ));
    }

    public function update(Request $request, $id)
    {
        // Ubah format angka: "1.000.000,50" → "1000000.50"
        $formattedAmount = str_replace(',', '.', str_replace('.', '', $request->deposit_amount));
        $request->merge(['deposit_amount' => $formattedAmount]);

        // Log setelah merge
        Log::debug('Formatted Request:', $request->all());

        // Validasi
        $request->validate([
            'jenis_pembayaran_id' => 'required|exists:payment_methods,id',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'customers_id' => 'required|exists:customers,id',
            'deposit_no' => 'required|string|unique:sales_deposits,deposit_no,' . $id,
            'deposit_date' => 'required|date',
            'deposit_reference' => 'nullable|string',
            'comment' => 'nullable|string',
            'deposit_amount' => 'required|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.invoice_date' => 'nullable|date',
            'items.*.sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'items.*.original_amount' => 'nullable|numeric|min:0',
            'items.*.amount_owing' => 'nullable|numeric|min:0',
            'items.*.discount_available' => 'nullable|numeric|min:0',
            'items.*.discount_taken' => 'nullable|numeric|min:0',
            'items.*.amount_received' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $deposit = SalesDeposit::findOrFail($id);

            $deposit->update([
                'deposit_no'         => $request->deposit_no,
                'deposit_date'       => $request->deposit_date,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'account_id'         => $request->account_id,
                'customer_id'        => $request->customers_id,
                'deposit_reference'  => $request->deposit_reference,
                'deposit_amount'     => $request->deposit_amount, // Sudah diformat
                'comment'            => $request->comment,
            ]);

            Log::debug('SalesDeposit updated', ['id' => $deposit->id]);

            // Hapus detail lama
            SalesDepositDetail::where('deposit_id', $deposit->id)->delete();
            Log::debug('Deleted old details');

            // Tambah ulang detail jika ada
            if ($request->has('items')) {
                foreach ($request->items as $index => $item) {
                    Log::debug("Processing item $index", $item);

                    if (!isset($item['amount_received']) || $item['amount_received'] <= 0) {
                        Log::debug("Item $index skipped (empty amount_received)");
                        continue;
                    }

                    SalesDepositDetail::create([
                        'deposit_id'         => $deposit->id,
                        'sales_invoice_id'   => $item['sales_invoice_id'] ?? null,
                        'original_amount'    => $item['original_amount'] ?? 0,
                        'amount_owing'       => $item['amount_owing'] ?? 0,
                        'discount_available' => $item['discount_available'] ?? 0,
                        'discount_taken'     => $item['discount_taken'] ?? 0,
                        'invoice_date'       => $item['invoice_date'] ?? null,
                        'used_amount'        => $item['amount_received'] ?? 0,
                    ]);

                    Log::debug("Detail $index saved");
                }
            }

            DB::commit();
            return redirect()->route('sales_deposits.index')->with('success', 'Sales Deposit berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal update SalesDeposit: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Ambil invoice beserta detail
            $deposits = SalesDeposit::with('details')->findOrFail($id);

            // Hapus semua detail terlebih dahulu
            $deposits->details()->delete();

            // Hapus deposits utamanya
            $deposits->delete();

            DB::commit();

            return redirect()->route('sales_deposits.index')->with('success', 'Sales deposits berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

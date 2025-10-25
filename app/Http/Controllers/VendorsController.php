<?php

namespace App\Http\Controllers;

use App\Perusahaan;
use App\Vendors;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorsController extends Controller
{
    //
    public function index(): View
    {
        $vendors = Vendors::latest()->paginate(5);

        return view('vendors.index', compact('vendors'));
    }
    public function create(): View
    {
        return  view('vendors.create');
    }
    private function generateKodeVendors()
    {
        $last = \App\Vendors::orderBy('kd_vendor', 'desc')->first();

        if ($last && preg_match('/VEN-(\d+)/', $last->kd_vendor, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'VEN-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Hapus 'required' karena bisa auto-generate
            'kd_vendor' => 'nullable|string|max:255',
            'nama_vendors' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:20'
        ]);

        // Kalau kosong (auto generate), buat kode otomatis
        if (empty($validated['kd_vendor'])) {
            $validated['kd_vendor'] = $this->generateKodeVendors();
        }

        Vendors::create($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendors created successfully.');
    }
    public function show($kd_vendor)
    {
        $vendors = Vendors::where('kd_vendor', $kd_vendor)->firstOrFail();
        return view('vendors.show', compact('vendors'));
    }
    public function edit($kd_vendor)
    {
        $vendors = Vendors::where('kd_vendor', $kd_vendor)->firstOrFail();
        return view('vendors.edit', compact('vendors'));
    }
    public function update(Request $request, $kd_vendor)
    {
        $vendors = Vendors::where('kd_vendor', $kd_vendor)->firstOrFail();
        $validated = $request->validate([
            'kd_vendors' => 'nullable|string|max:255',
            'nama_vendors' => 'nullable|string|max:75',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:20'
        ]);

        $vendors->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendors updated successfully.');
    }
    public function destroy($id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $vendor = Vendors::with(['purchaseOrder', 'invoices'])->findOrFail($id);

                // 🚫 Cek apakah sudah dipakai di Invoice
                if ($vendor->invoices()->exists()) {
                    throw new \Exception("Vendor ini sudah digunakan dalam Purchase Invoices tidak bisa dihapus.");
                }
                if ($vendor->purchaseOrder()->exists()) {
                    throw new \Exception("Vendor ini sudah digunakan dalam Purchase Order tidak bisa dihapus.");
                }

                // ✅ Kalau aman, hapus (details ikut terhapus otomatis via cascade)
                $vendor->delete();
            });

            return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('vendors.index')->with('error', $e->getMessage());
        }
    }
    public function search(Request $request)
    {
        $term = $request->q;
        $vendors = Vendors::where('nama_vendors', 'like', "%$term%")
            ->select('id', 'nama_vendors')
            ->limit(20)
            ->get();

        return response()->json($vendors);
    }
    public function getInvoicesAndPrepayments($vendorId)
    {
        $vendor = Vendors::with([
            'invoices' => function ($q) {
                $q->where('status_purchase', 0)
                    ->with([
                        'details',
                        'paymentmethodDetail.chartOfAccount',
                    ]);
            },
            'invoices.details',                 // relasi detail invoice
            'invoices.paymentmethodDetail.chartOfAccount', // relasi akun invoice
            'prepayments.accountPrepayment',
        ])->findOrFail($vendorId);

        // Hitung total untuk setiap invoice
        $invoices = $vendor->invoices->map(function ($invoice) {
            $subtotal = 0;
            $total_tax = 0;

            foreach ($invoice->details as $item) {
                $amount = ($item->price - $item->discount) * $item->quantity;
                $subtotal += $amount;
                $total_tax += $item->tax_amount;
            }

            $invoice->original_amount = $subtotal + $total_tax + ($invoice->freight ?? 0);
            $invoice->amount_owing = $invoice->original_amount;

            $invoice->invoice_number_id = $invoice->id;

            // Header account (misalnya Hutang Dagang)
            $invoice->header_account_id = $invoice->payment_method_account_id ?? null;
            $invoice->header_account_code = $invoice->paymentmethodDetail->chartOfAccount->kode_akun ?? null;
            $invoice->header_account_name = $invoice->paymentmethodDetail->chartOfAccount->nama_akun ?? null;

            return $invoice;
        });

        // Tambahkan informasi akun untuk prepayment
        $prepayments = $vendor->prepayments->map(function ($p) {
            $p->account_prepayment_id = $p->account_prepayment;
            $p->account_prepayment_code = $p->accountPrepayment->kode_akun ?? null;
            $p->account_prepayment_name = $p->accountPrepayment->nama_akun ?? null;
            return $p;
        });

        return response()->json([
            'invoices' => $invoices,
            'prepayments' => $prepayments,
        ]);
    }
}

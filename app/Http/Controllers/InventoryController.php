<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Inventory;
use App\LocationInventory;
use App\PriceListInventory;
use App\Taxes;
use App\Vendors;
use App\Item;
use App\ItemQuantity;
use App\ItemUnit;
use App\ItemPrice;
use App\ItemVendor;
use App\ItemAccount;
use App\ItemBuild;
use App\ItemBuildDetail;
use App\ItemQuantities;
use App\SalesTaxes;
use App\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    //
    public function index()
    {
        $items = Item::with('quantities')->paginate(10);
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        $accounts = chartOfAccount::where('aktif', true)->orderBy('kode_akun')->get();
        $items = Item::all();
        $taxes = SalesTaxes::all();
        $lokasiInventory = LocationInventory::all();
        $priceListInventory = PriceListInventory::all();
        $vendors = Vendors::all();
        return view('inventory.create', compact('accounts', 'items', 'taxes', 'lokasiInventory', 'priceListInventory', 'vendors'));
    }
    public function store(Request $request)
    {
        // ===== 1) VALIDASI DASAR =====
        $rules = [
            'item_number' => ['required', 'string', 'max:100', Rule::unique('items', 'item_number')],
            'item_description'   => ['required', 'string', 'max:255'],
            'type'        => ['required', Rule::in(['inventory', 'service'])],

            // Quantities (opsional)
            'location_id'           => ['nullable', 'integer', 'exists:location_inventories,id'],
            'on_hand_qty'           => ['nullable', 'integer'],
            'on_hand_value'         => ['nullable', 'numeric'],
            'pending_orders_qty'    => ['nullable', 'integer'],
            'pending_orders_value'  => ['nullable', 'numeric'],
            'purchase_order_qty'    => ['nullable', 'integer'],
            'sales_order_qty'       => ['nullable', 'integer'],
            'reorder_minimum'       => ['nullable', 'integer'],
            'reorder_to_order'      => ['nullable', 'integer'],

            // Units
            'stocking_unit'         => ['nullable', 'string', 'max:100'],
            'selling_same_as_stocking' => ['nullable'],
            'selling_unit'          => ['nullable', 'string', 'max:100'],
            'selling_relationship'  => ['nullable', 'integer', 'min:0'],
            'buying_same_as_stocking'  => ['nullable'],
            'buying_unit'           => ['nullable', 'string', 'max:100'],
            'buying_relationship'   => ['nullable', 'integer', 'min:0'],

            // Pricing (array)
            'pricing'               => ['nullable', 'array'],
            'pricing.*.name'        => ['nullable', 'string', 'max:255'],
            'pricing.*.price'       => ['nullable', 'numeric'],

            // Vendors
            'vendor_id'             => ['nullable', 'integer', 'exists:vendors,id'],

            // Accounts (inventory)
            'asset_account_id'      => ['nullable', 'integer', 'exists:chart_of_accounts,id'],
            'revenue_account_id'    => ['nullable', 'integer', 'exists:chart_of_accounts,id'],
            'cogs_account_id'       => ['nullable', 'integer', 'exists:chart_of_accounts,id'],
            'variance_account_id'   => ['nullable', 'integer', 'exists:chart_of_accounts,id'],
            'expense_account_id'    => ['nullable', 'integer', 'exists:chart_of_accounts,id'], // untuk service

            // Build
            'build'                 => ['nullable', 'array'],
            'build.*.item_id'       => ['nullable', 'integer', 'exists:items,id'],
            'build.*.unit'          => ['nullable', 'string', 'max:100'],
            'build.*.description'   => ['nullable', 'string', 'max:255'],
            'build.*.quantity'      => ['nullable', 'integer', 'min:0'],

            // Taxes
            'taxes'                 => ['nullable', 'array'],
            'taxes.*'               => ['integer', 'exists:taxes,id'],
            'tax_exempt'            => ['nullable', 'array'],
            'tax_exempt.*'          => ['integer', 'exists:taxes,id'],

            // Files
            'picture'               => ['nullable', 'file', 'image', 'max:2048'],
            'thumbnail'             => ['nullable', 'file', 'image', 'max:1024'],

            // Descriptions
            'long_description'      => ['nullable', 'string'],
        ];

        // Tambah aturan "wajib akun" sesuai type
        if ($request->type === 'inventory') {
            $rules['asset_account_id'][]    = 'required';
            $rules['revenue_account_id'][]  = 'required';
            $rules['cogs_account_id'][]     = 'required';
            $rules['variance_account_id'][] = 'required';
        } else { // service
            $rules['revenue_account_id'][]  = 'required';
            $rules['expense_account_id'][]  = 'required';
        }

        $validated = $request->validate($rules);

        // ===== 2) SIMPAN DALAM TRANSAKSI =====
        DB::beginTransaction();

        try {
            // ===== 2a) Upload gambar (jika ada) =====
            $picturePath = null;
            $thumbPath   = null;

            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('items/pictures', 'public');
            }
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('items/thumbnails', 'public');
            }

            // ===== 2b) Buat Item =====
            $item = Item::create([
                'item_number'     => $validated['item_number'],
                'item_description'       => $validated['item_description'],
                // Form ini pakai "long_description" sebagai deskripsi panjang,
                // mapping ke kolom yang kamu punya:
                'item_description' => $request->input('long_description'), // atau 'description' sesuai kebutuhanmu
                'description'     => $request->input('long_description'),
                'type'            => $validated['type'],
                'picture_path'    => $picturePath,
                'thubmnail_path'  => $thumbPath, // mengikuti nama kolom di DB
            ]);

            // ===== 2c) Item Quantities (opsional, buat 1 baris per item) =====
            $anyQuantityFilled = $request->filled([
                'location_id',
                'on_hand_qty',
                'on_hand_value',
                'pending_orders_qty',
                'pending_orders_value',
                'purchase_order_qty',
                'sales_order_qty',
                'reorder_minimum',
                'reorder_to_order'
            ]);

            if ($anyQuantityFilled) {
                ItemQuantities::create([
                    'item_id'               => $item->id,
                    'location_id'           => $request->input('location_id'),
                    'on_hand_qty'           => (int) $request->input('on_hand_qty', 0),
                    'on_hand_value'         => (float) $request->input('on_hand_value', 0),
                    'pending_orders_qty'    => (int) $request->input('pending_orders_qty', 0),
                    'pending_orders_value'  => (float) $request->input('pending_orders_value', 0),
                    'purchase_order_qty'    => (int) $request->input('purchase_order_qty', 0),
                    'sales_order_qty'       => (int) $request->input('sales_order_qty', 0),
                    'reorder_minimum'       => $request->input('reorder_minimum'),
                    'reorder_to_order'      => $request->input('reorder_to_order'),
                ]);
            }

            // ===== 2d) Item Units =====
            // Checkbox akan bernilai "1" jika dicentang, null jika tidak
            $sellingSame = $request->has('selling_same_as_stocking');
            $buyingSame  = $request->has('buying_same_as_stocking');

            ItemUnit::create([
                'item_id'                   => $item->id,
                'selling_same_as_stocking'  => $sellingSame,
                'selling_unit'              => $sellingSame ? null : $request->input('selling_unit'),
                'selling_relationship'      => $sellingSame ? null : $request->input('selling_relationship'),

                'buying_same_as_stocking'   => $buyingSame,
                'buying_unit'               => $buyingSame ? null : $request->input('buying_unit'),
                'buying_relationship'       => $buyingSame ? null : $request->input('buying_relationship'),

                'unit_of_measure'           => $request->input('stocking_unit'), // stok utama
            ]);

            // ===== 2e) Item Prices (pricing array dari Inventory/Service) =====
            $pricing = collect($request->input('pricing', []))
                ->filter(function ($row) {
                    // minimal ada nama atau price
                    return !empty($row['name']) || isset($row['price']);
                });

            foreach ($pricing as $row) {
                ItemPrice::create([
                    'item_id'        => $item->id,
                    'price_list_name' => $row['name'] ?? '',
                    'price'          => (float) ($row['price'] ?? 0),
                ]);
            }

            // ===== 2f) Item Vendor (opsional) =====
            if ($request->filled('vendor_id')) {
                ItemVendor::create([
                    'item_id'        => $item->id,
                    'vendor_id'      => $request->input('vendor_id'),
                    'vendor_contact' => null, // isi jika ada field kontak di form
                ]);
            }

            // ===== 2g) Item Accounts =====
            // Inventory: asset, revenue, cogs, variance (required)
            // Service: revenue, expense (required)
            $accountsPayload = [
                'item_id'            => $item->id,
                'asset_account_id'   => $request->input('asset_account_id'),
                'revenue_account_id' => $request->input('revenue_account_id'),
                'cogs_account_id'    => $request->input('cogs_account_id'),
                'variance_account_id' => $request->input('variance_account_id'),
                'expense_account_id' => $request->input('expense_account_id'),
            ];

            // Kosongkan yang tidak relevan tergantung type agar bersih
            if ($item->type === 'inventory') {
                // expense tidak relevan untuk inventory (biarkan null)
                $accountsPayload['expense_account_id'] = null;
            } else { // service
                // inventory tidak butuh asset/cogs/variance (biarkan null)
                $accountsPayload['asset_account_id']    = null;
                $accountsPayload['cogs_account_id']     = null;
                $accountsPayload['variance_account_id'] = null;
            }

            ItemAccount::create($accountsPayload);

            // ===== 2h) Build (opsional): header + details =====
            $buildRows = collect($request->input('build', []))
                ->filter(function ($row) {
                    return !empty($row['item_id']) && !empty($row['quantity']);
                });

            if ($buildRows->isNotEmpty()) {
                // Default build header (silakan sesuaikan jika nanti ada di form)
                $build = ItemBuild::create([
                    'item_id'         => $item->id,
                    'build_quantity'  => 1,
                    'additional_costs' => null,
                    'cost_account'    => null,
                ]);

                foreach ($buildRows as $d) {
                    ItemBuildDetail::create([
                        'item_build_id' => $build->id,
                        'item_id'       => $d['item_id'],          // komponen
                        'unit'          => $d['unit'] ?? '',
                        'description'   => $d['description'] ?? null,
                        'quantity'      => (int) $d['quantity'],
                    ]);
                }
            }

            // ===== 2i) Taxes =====
            $selectedTaxIds = collect($request->input('taxes', []))->filter();      // [id, id, ...]
            $exemptTaxIds   = collect($request->input('tax_exempt', []))->filter(); // [id, id, ...]

            if ($selectedTaxIds->isNotEmpty()) {
                // Ambil nama pajak dari master taxes
                $taxes = \App\SalesTaxes::whereIn('id', $selectedTaxIds)->get(['id', 'name']);

                foreach ($taxes as $tax) {
                    \App\ItemTaxes::create([
                        'item_id'   => $item->id,
                        'tax_name'  => $tax->name,                     // skema kamu minta tax_name, bukan tax_id
                        'is_exempt' => $exemptTaxIds->contains($tax->id),
                    ]);
                }
            }


            DB::commit();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Item berhasil dibuat beserta seluruh konfigurasi terkait.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang terlanjur ter-upload bila gagal
            if (!empty($picturePath)) {
                Storage::disk('public')->delete($picturePath);
            }
            if (!empty($thumbPath)) {
                Storage::disk('public')->delete($thumbPath);
            }

            report($e);
            return back()->withInput()->with('error', 'Gagal menyimpan item: ' . $e->getMessage());
        }
    }
}

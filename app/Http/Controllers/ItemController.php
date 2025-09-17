<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use App\Item;
use App\ItemBuild;
use App\itemCategory;
use App\PriceListInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with(['category', 'account'])->latest()->get();
        $data = itemCategory::get()
            ->pluck('nama_kategori')
            ->filter()
            ->unique()
            ->values();

        // $items = Item::all();
        return view('items.index', compact('items', 'data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = chartOfAccount::all();
        $priceListInventory = PriceListInventory::all();
        $categories = itemCategory::where('status', 1)->orderBy('nama_kategori')->get();
        return view('items.create', compact('accounts', 'categories', 'priceListInventory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_number' => 'required|string|unique:items,item_number',
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'category_id' => 'nullable|exists:item_categories,id',
            'brand' => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'is_active' => 'boolean',
            'image'     => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        //upload image
        $image = $request->file('image');
        $imageName = null;

        if ($image) {
            $image->storeAs('public/items', $image->hashName());
            $imageName = $image->hashName();
        }

        // Tambahkan nama file gambar ke data validasi
        $validated['image'] = $imageName;
        // Simpan barcode berdasarkan item_number
        $validated['barcode'] = $request->item_number;

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Item::findOrFail($id);

        return view('items.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $accounts = chartOfAccount::all();
        $categories = ItemCategory::all();

        return view('items.edit', compact('item', 'accounts', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'item_number' => 'required|string|unique:items,item_number,' . $id,
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'category_id' => 'nullable|exists:item_categories,id',
            'brand' => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Jika ada file image baru di-upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/items', $imageName);
            $validated['image'] = $imageName;
        }

        // Barcode update berdasarkan item_number
        $validated['barcode'] = $validated['item_number'];

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // Hapus gambar jika ada
        if ($item->image) {
            Storage::delete('public/items/' . $item->image);
        }

        // Hapus item dari database
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus.');
    }
    public function search(Request $request)
    {
        $items = Item::with('quantities', 'units', 'accounts')
            ->where('item_description', 'like', '%' . $request->q . '%')
            ->orWhere('item_number', 'like', '%' . $request->q . '%')
            ->get();

        return response()->json($items->map(function ($item) {
            return [
                'id'            => $item->id,
                'item_number'   => $item->item_number,
                'item_description' => $item->item_description,
                'on_hand_qty'   => $item->quantities->sum('on_hand_qty'), // ✅ pakai sum
                'unit'          => $item->units->buying_unit ?? '-',
                'tax_rate'      => $item->tax_rate,
                'account_id'    => $item->accounts->cogs_account_id ?? null,
                'account_name'  => optional($item->accounts->account)->nama_akun ?? '-',
            ];
        }));
    }

    public function info($id)
    {
        $item = Item::with(['quantities', 'units'])->findOrFail($id);

        return response()->json([
            'description'   => $item->item_description,
            'unit'          => optional($item->units)->unit_of_measure ?? '-',
            'current_stock' => $item->quantities->sum('on_hand_qty'),
        ]);
    }
    public function bom($id)
    {
        $itemBuild = ItemBuild::with(['details.item.quantities'])->where('item_id', $id)->first();

        if (!$itemBuild) {
            return response()->json(['details' => []]);
        }

        return response()->json([
            'build_quantity' => $itemBuild->build_quantity,
            'additional_costs' => $itemBuild->additional_costs,
            'details' => $itemBuild->details->map(function ($d) {
                $unitCost  = optional($d->item->quantities->first())->on_hand_value ?? 0;
                $amount    = $unitCost * $d->quantity;
                $available = $d->item->quantities->sum('on_hand_qty'); // stok komponen

                return [
                    'component_id' => $d->item_id,
                    'description'  => $d->description ?? $d->item->item_description,
                    'unit'         => $d->unit,
                    'quantity'     => $d->quantity,
                    'unit_cost'    => $unitCost,
                    'amount'       => $amount,
                    'available'    => $available,
                ];
            }),
        ]);
    }
}

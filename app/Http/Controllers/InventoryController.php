<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Inventory;
use App\Item;
use App\Taxes;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //
    public function index()
    {
        $items = Inventory::with('quantities')->paginate(10);
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        $accounts = chartOfAccount::where('aktif', true)->orderBy('kode_akun')->get();
        $items = Item::all();
        $taxes = Taxes::all();
        return view('inventory.create', compact('accounts', 'items', 'taxes'));
    }
}

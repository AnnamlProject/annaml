<?php

namespace App\Http\Controllers;

use App\BuildOfBom;
use App\Item;
use App\ItemBuild;
use Illuminate\Http\Request;

class BuildOfBomController extends Controller
{
    //
    public function index()
    {
        $data = BuildOfBom::with(['item'])->paginate(10);
        return view('build_of_bom.index', compact('data'));
    }
    public function create()
    {
        $item = Item::all();
        return view('build_of_bom.create', compact('item'));
    }
}

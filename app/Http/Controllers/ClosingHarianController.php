<?php

namespace App\Http\Controllers;

use App\ClosingHarian;
use App\UnitKerja;
use App\Wahana;
use Illuminate\Http\Request;

class ClosingHarianController extends Controller
{
    //
    public function index()
    {
        $data = ClosingHarian::with(['wahana', 'unitKerja'])->orderBy('tanggal')->get();
        return view('closing_harian.index', compact('data'));
    }
    public function create()
    {
        $wahana = Wahana::all();
        $unitKerja = UnitKerja::all();
        return view('closing_harian.create', compact('wahana', 'unitKerja'));
    }
}

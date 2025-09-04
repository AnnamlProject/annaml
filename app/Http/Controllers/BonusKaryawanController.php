<?php

namespace App\Http\Controllers;

use App\BonusKaryawan;
use Illuminate\Http\Request;

class BonusKaryawanController extends Controller
{
    //
    public function index()
    {
        $data = BonusKaryawan::with(['employee', 'shift', 'transaksiWahana.wahana', 'jenisHari'])->get();
        return view('bonus_karyawan.index', compact('data'));
    }
}

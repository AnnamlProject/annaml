<?php

namespace App\Http\Controllers;

use App\KomponenPenghasilan;
use App\LevelKaryawan;
use App\Targetunit;
use App\UnitKerja;
use Illuminate\Http\Request;

class TargetUnitController extends Controller
{
    //
    public function index()
    {
        $data = TargetUnit::with(['unit', 'komponen', 'levelKaryawan'])
            ->join('unit_kerjas', 'targetunits.unit_kerja_id', '=', 'unit_kerjas.id')
            ->orderBy('unit_kerjas.nama_unit')
            ->select('targetunits.*')
            ->get();

        return view('target_unit.index', compact('data'));
    }
    public function create()
    {
        $unit = UnitKerja::all();
        $levelKaryawan = LevelKaryawan::all();
        return view('target_unit.create', compact('unit', 'levelKaryawan'));
    }
    public function getKomponenByLevel($levelId)
    {
        $komponen = KomponenPenghasilan::where('level_karyawan_id', $levelId)->get();
        return response()->json($komponen);
    }
    public function store(Request $request)
    {
        // Bersihkan titik ribuan sebelum validasi
        $request->merge([

            'target_bulanan' => str_replace('.', '', $request->target_bulanan),
            'besaran_nominal' => str_replace('.', '', $request->besaran_nominal),
        ]);

        // Validasi setelah input dibersihkan
        $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'level_karyawan_id' => 'required|exists:level_karyawans,id',
            'komponen_penghasilan_id' => 'required|exists:komponen_penghasilans,id',
            'target_bulanan' => 'required|numeric',
            'besaran_nominal' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        Targetunit::create($request->all());

        return redirect()->route('target_unit.index')->with('success', 'Data Target Unit berhasil ditambahkan');
    }
    public function show($id)
    {
        $target_unit = Targetunit::findOrFail($id);

        return view('target_unit.show', compact('target_unit'));
    }
    public function edit($id)
    {
        //get post by ID
        $target_unit = \App\Targetunit::findOrFail($id);
        $levelKaryawan = LevelKaryawan::all();
        $unit = UnitKerja::all();

        return view('target_unit.edit', compact('target_unit', 'levelKaryawan', 'unit'));
    }
    public function update(Request $request, Targetunit $target_unit)
    {
        $request->merge([
            'target_bulanan' => str_replace('.', '', $request->target_bulanan),
            'besaran_nominal' => str_replace('.', '', $request->besaran_nominal),
        ]);

        $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'level_karyawan_id' => 'required|exists:level_karyawans,id',
            'komponen_penghasilan_id' => 'required|exists:komponen_penghasilans,id',
            'target_bulanan' => 'required|numeric',
            'besaran_nominal' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        $target_unit->update($request->all());

        return redirect()->route('target_unit.index')->with('success', 'Data Target Wahana berhasil diperbarui');
    }


    public function destroy($id)
    {
        $target_unit = Targetunit::findOrFail($id);

        $target_unit->delete();

        return redirect()->route('target_unit.index')->with('success', ' Data berhasil dihapus.');
    }
}

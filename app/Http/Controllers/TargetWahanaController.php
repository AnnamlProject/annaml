<?php

namespace App\Http\Controllers;

use App\JenisHari;
use App\TargetWahana;
use App\UnitKerja;
use App\Wahana;
use Facade\FlareClient\View;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as FacadesView;

class TargetWahanaController extends Controller
{
    //
    public function index()
    {
        $data = TargetWahana::with('wahana', 'jenis_hari', 'unit')->orderBy('wahana_id')->get();
        return view('target_wahana.index', compact('data'));
    }
    public function create()
    {
        $unit = UnitKerja::all();
        $jenis_hari = JenisHari::all();
        return view('target_wahana.create', compact('jenis_hari', 'unit'));
    }
    public function getWahanaByUnit($unitId)
    {
        $wahana = Wahana::where('unit_kerja_id', $unitId)->get();
        return response()->json($wahana);
    }

    public function store(Request $request)
    {
        // Bersihkan titik ribuan sebelum validasi
        $request->merge([

            'target_harian' => str_replace('.', '', $request->target_harian),
        ]);

        // Validasi setelah input dibersihkan
        $request->validate([
            'wahana_id' => 'required|exists:wahanas,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'jenis_hari_id' => 'required|exists:jenis_haris,id',
            'target_harian' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'keterangan' => 'nullable|string'
        ]);

        TargetWahana::create($request->all());

        return redirect()->route('target_wahana.index')->with('success', 'Data Target Wahana berhasil ditambahkan');
    }
    public function destroy($id)
    {
        $target_wahana = TargetWahana::findOrFail($id);

        $target_wahana->delete();

        return redirect()->route('target_wahana.index')->with('success', ' Data berhasil dihapus.');
    }
    public function show($id)
    {
        $target_wahana = TargetWahana::findOrFail($id);

        return view('target_wahana.show', compact('target_wahana'));
    }

    public function edit($id)
    {
        //get post by ID
        $target_wahana = \App\TargetWahana::findOrFail($id);
        $unit = UnitKerja::all();
        $jenis_hari = JenisHari::all();

        return view('target_wahana.edit', compact('target_wahana', 'jenis_hari', 'unit'));
    }
    public function update(Request $request, TargetWahana $target_wahana)
    {
        $request->merge([
            'target_harian' => str_replace('.', '', $request->target_harian),
        ]);

        $request->validate([
            'wahana_id' => 'required|exists:wahanas,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'jenis_hari_id' => 'required|exists:jenis_haris,id',
            'target_harian' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'keterangan' => 'nullable|string'
        ]);

        $target_wahana->update($request->all());

        return redirect()->route('target_wahana.index')->with('success', 'Data Target Wahana berhasil diperbarui');
    }
}

<?php

namespace App\Http\Controllers;

use App\KomponenPenghasilan;
use App\LevelKaryawan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class KomponenPenghasilanController extends Controller
{
    //
    public function index()
    {
        $data = KomponenPenghasilan::with('levelKaryawan')->get();
        return view('komponen_penghasilan.index', compact('data'));
    }
    public function create()
    {
        $levels = LevelKaryawan::all();
        return view('komponen_penghasilan.create', compact('levels'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_komponen' => 'required',
            'tipe' => 'required',
            'kategori' => 'required',
            'sifat' => 'required',
            'periode_perhitungan' => 'required',
            'status_komponen' => 'required',
            'level_karyawan_id' => 'required|exists:level_karyawans,id',
            'cek_komponen' => 'nullable|boolean'
        ]);

        KomponenPenghasilan::create($request->all());

        return redirect()->route('komponen_penghasilan.index')->with('success', 'Data berhasil ditambahkan.');
    }
    public function show($id)
    {
        $data = KomponenPenghasilan::findOrFail($id);

        return view('komponen_penghasilan.show', compact('data'));
    }
    public function edit($id)
    {
        $data = KomponenPenghasilan::findOrFail($id);
        $levels = LevelKaryawan::all(); // untuk dropdown level kepegawaian
        return view('komponen_penghasilan.edit', compact('data', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_komponen' => 'required',
            'tipe' => 'required',
            'kategori' => 'required',
            'sifat' => 'required',
            'periode_perhitungan' => 'required',
            'status_komponen' => 'required',
            'level_karyawan_id' => 'required|exists:level_karyawans,id',
            'cek_komponen' => 'nullable|boolean',
        ]);

        $data = KomponenPenghasilan::findOrFail($id);

        $data->update([
            'nama_komponen' => $request->nama_komponen,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'sifat' => $request->sifat,
            'periode_perhitungan' => $request->periode_perhitungan,
            'status_komponen' => $request->status_komponen,
            'level_karyawan_id' => $request->level_karyawan_id,
            'cek_komponen' => $request->has('cek_komponen') ? 1 : 0
        ]);

        return redirect()->route('komponen_penghasilan.index')->with('success', 'Data berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $komponen_penghasilan = KomponenPenghasilan::findOrFail($id);



        // Hapus komponen_penghasilan dari database
        $komponen_penghasilan->delete();

        return redirect()->route('komponen_penghasilan.index')->with('success', 'komponen_penghasilan berhasil dihapus.');
    }
}

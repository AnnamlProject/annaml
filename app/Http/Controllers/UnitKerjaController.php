<?php

namespace App\Http\Controllers;

use App\UnitKerja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    //
    public function index()
    {
        $unit_kerja = UnitKerja::all();
        return view('unit_kerja.index', compact('unit_kerja'));
    }
    public function create()
    {
        return view('unit_kerja.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string',
            'urutan' => 'required|integer',
            'deskripsi' => 'nullable|string',

        ]);

        UnitKerja::create($request->all());

        return redirect()->route('unit_kerja.index')->with('success', 'Unit Kerja berhasil ditambahkan.');
    }
    public function show($id)
    {
        $unit_kerja = UnitKerja::findOrFail($id);

        return view('unit_kerja.show', compact('unit_kerja'));
    }
    public function edit(string $id): View
    {
        //get post by ID
        $unit_kerja = UnitKerja::findOrFail($id);

        return view('unit_kerja.edit', compact('unit_kerja'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_unit' => 'required|string',
            'urutan' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);

        $unit_kerja = UnitKerja::findOrFail($id);

        $unit_kerja->update($request->all());

        return redirect()->route('unit_kerja.index')->with('success', 'Unit Kerja berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $unit_kerja = UnitKerja::findOrFail($id);

        $unit_kerja->delete();

        return redirect()->route('unit_kerja.index')->with('success', ' berhasil dihapus.');
    }
}

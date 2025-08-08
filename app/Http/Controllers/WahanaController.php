<?php

namespace App\Http\Controllers;

use App\UnitKerja;
use App\Wahana;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WahanaController extends Controller
{
    //
    public function index()
    {
        $data = Wahana::with('UnitKerja')->orderBy('nama_wahana')->get();
        return view('wahana.index', compact('data'));
    }
    public function create()
    {
        $unit_kerja = UnitKerja::all();
        return view('wahana.create', compact('unit_kerja'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_wahana' => 'required|array',
            'unit_kerja_id' => 'required|array',
            'kategori' => 'required|array',
            'status' => 'required|array',
            'kapasitas' => 'required|array',
        ]);

        // Ambil kode terakhir
        $lastKode = Wahana::where('kode_wahana', 'like', 'WHN-%')
            ->orderByDesc('kode_wahana')
            ->value('kode_wahana');

        $lastNumber = $lastKode ? (int)substr($lastKode, 4) : 0;

        $data = [];

        for ($i = 0; $i < count($request->nama_wahana); $i++) {
            // Lewati jika nama kosong
            if (empty($request->nama_wahana[$i])) continue;

            $lastNumber++;
            $kode_wahana = 'WHN-' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

            $data[] = [
                'kode_wahana' => $kode_wahana,
                'nama_wahana' => $request->nama_wahana[$i],
                'unit_kerja_id' => $request->unit_kerja_id[$i],
                'kategori' => $request->kategori[$i],
                'status' => $request->status[$i],
                'kapasitas' => $request->kapasitas[$i],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (count($data)) {
            Wahana::insert($data);
            return redirect()->route('wahana.index')->with('success', 'Data berhasil ditambahkan.');
        } else {
            return back()->with('error', 'Tidak ada data yang dimasukkan.');
        }
    }
    public function show($id)
    {
        $wahana = Wahana::findOrFail($id);

        return view('wahana.show', compact('wahana'));
    }
    public function edit(string $id): View
    {
        //get post by ID
        $wahana = Wahana::findOrFail($id);
        $unit = UnitKerja::all();

        return view('wahana.edit', compact('wahana', 'unit'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'kode_wahana' => 'required|string',
            'nama_wahana' => 'required|string',
            'status' => 'required|string',
            'kapasitas' => 'nullable|integer',
            'kategori' => 'nullable|string'

        ]);

        $wahana = Wahana::findOrFail($id);

        $wahana->update($request->all());

        return redirect()->route('wahana.index')->with('success', 'Wahana berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $wahana = Wahana::findOrFail($id);

        $wahana->delete();

        return redirect()->route('wahana.index')->with('success', ' Data berhasil dihapus.');
    }
}

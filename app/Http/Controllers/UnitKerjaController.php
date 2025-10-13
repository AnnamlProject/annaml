<?php

namespace App\Http\Controllers;

use App\GroupUnit;
use App\UnitKerja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitKerjaController extends Controller
{
    //
    public function index()
    {
        $unit_kerja = UnitKerja::with(['groupUnit'])->get();
        return view('unit_kerja.index', compact('unit_kerja'));
    }
    public function create()
    {
        $groupUnit = GroupUnit::all();
        return view('unit_kerja.create', compact('groupUnit'));
    }
    public function store(Request $request)
    {
        // 1️⃣ Validasi array
        $validated = $request->validate([
            'group_unit_id.*' => 'required|exists:group_units,id',
            'nama_unit.*'          => 'required|string|max:255',
            'deskripsi.*'     => 'nullable|string',
            'urutan.*'     => 'required|integer',
        ]);

        // 3️⃣ Siapkan data untuk insert batch
        $data = [];
        for ($i = 0; $i < count($request->group_unit_id); $i++) {
            if (empty($request->group_unit_id[$i])) continue;

            $data[] = [
                'group_unit_id' => $request->group_unit_id[$i],
                'nama_unit'          => $request->nama_unit[$i],
                'urutan'     => $request->urutan[$i],
                'deskripsi'     => $request->deskripsi[$i] ?? null,
                'created_at'    => now(),
                'updated_at'    => now()
            ];
        }

        // 4️⃣ Simpan data
        if (count($data)) {
            \App\UnitKerja::insert($data);
            return redirect()->route('unit_kerja.index')->with('success', 'Data berhasil ditambahkan.');
        } else {
            return back()->with('error', 'Tidak ada data yang dimasukkan.');
        }
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
        $groupUnit = GroupUnit::all();

        return view('unit_kerja.edit', compact('unit_kerja', 'groupUnit'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'group_unit_id' => 'required|exists:group_units,id',
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
        try {
            DB::transaction(function () use ($id) {
                $unit_kerja = UnitKerja::with(['jenisHari', 'employee', 'targetUnit'])->findOrFail($id);

                // 🚫 Cek apakah sudah dipakai di Invoice
                if ($unit_kerja->jenisHari()->exists()) {
                    throw new \Exception("Unit Kerja ini,tidak bisa di hapus karena sudah digunakan dalam Jenis Hari.");
                }
                if ($unit_kerja->employee()->exists()) {
                    throw new \Exception("Unit Kerja ini,tidak bisa di hapus karena sudah digunakan dalam Employee.");
                }
                if ($unit_kerja->targetUnit()->exists()) {
                    throw new \Exception("Unit Kerja ini,tidak bisa di hapus karena sudah digunakan dalam Target unit.");
                }

                // ✅ Kalau aman, hapus (details ikut terhapus otomatis via cascade)
                $unit_kerja->delete();
            });

            return redirect()->route('unit_kerja.index')->with('success', 'Unit Kerja berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('unit_kerja.index')->with('error', $e->getMessage());
        }
    }
}

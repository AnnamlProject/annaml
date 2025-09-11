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
        // Ambil query dasar
        $query = Wahana::select('wahanas.*')
            ->join('unit_kerjas', 'unit_kerjas.id', '=', 'wahanas.unit_kerja_id')
            ->orderBy('unit_kerjas.urutan', 'asc')
            ->orderBy('wahanas.urutan', 'asc');


        // Filter Unit
        if ($unit = request('filter_tipe')) {
            $query->whereHas('UnitKerja', function ($q) use ($unit) {
                $q->where('nama_unit', $unit);
            });
        }

        // Filter Status
        if ($status = request('filter_status')) {
            $query->where('status', $status);
            // pastikan di tabel Wahana ada kolom 'status'
            // misalnya nilainya 'aktif' / 'nonaktif' atau 1/0
        }

        // Filter Search
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_wahana', 'like', "%$search%")
                    ->orWhere('kode_wahana', 'like', "%$search%");
            });
        }

        // Eksekusi query
        $data = $query->get();

        // Ambil list unit unik
        $unitkerja = UnitKerja::select('nama_unit')->distinct()->pluck('nama_unit');

        return view('wahana.index', compact('data', 'unitkerja'));
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
            'urutan' => 'required|array',
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
                'urutan' => $request->urutan[$i],
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
            'kategori' => 'nullable|string',
            'urutan' => 'required|integer'

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
    public function byUnit($unitId)
    {
        // asumsi kolom foreign key: wahanas.unit_kerja_id
        $wahanas = Wahana::where('unit_kerja_id', $unitId)
            ->orderBy('nama_wahana')
            ->get(['id', 'nama_wahana']);

        return response()->json($wahanas);
    }
}

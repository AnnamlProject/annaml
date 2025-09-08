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
        $query = KomponenPenghasilan::with('levelKaryawan');


        // Filter Level Karyawan
        if ($level_karyawan = request('filter_tipe')) {
            $query->whereHas('levelKaryawan', function ($q) use ($level_karyawan) {
                $q->where('nama_level', $level_karyawan);
            });
        }

        // Filter Status
        if ($status = request('filter_status')) {
            $query->where('status_komponen', $status);
            // pastikan di tabel Wahana ada kolom 'status'
            // misalnya nilainya 'aktif' / 'nonaktif' atau 1/0
        }
        if ($sifat = request('filter_sifat')) {
            $query->where('sifat', $sifat);
            // pastikan di tabel Wahana ada kolom 'status'
            // misalnya nilainya 'aktif' / 'nonaktif' atau 1/0
        }
        if ($tipe_gaji = request('filter_tipe_gaji')) {
            $query->where('tipe', $tipe_gaji);
            // pastikan di tabel Wahana ada kolom 'status'
            // misalnya nilainya 'aktif' / 'nonaktif' atau 1/0
        }
        $searchable = ['nama_komponen', 'kategori'];

        if ($search = request('search')) {
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
                $q->orWhereHas('levelKaryawan', function ($q4) use ($search) {
                    $q4->where('nama_level', 'like', "%{$search}%");
                });
            });
        }


        // Eksekusi query sekali di akhir
        $data = $query->get();
        // Atau kalau mau paginasi:
        // $data = $query->paginate(20)->appends(request()->query());

        // Sumber data untuk dropdown
        $levelKaryawan = LevelKaryawan::select('nama_level')->distinct()->orderBy('nama_level')->pluck('nama_level');
        return view('komponen_penghasilan.index', compact('data', 'levelKaryawan'));
    }
    public function create()
    {
        $levels = LevelKaryawan::all();
        return view('komponen_penghasilan.create', compact('levels'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_komponen' => 'required|string|max:255',
            'tipe' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'sifat' => 'required|string|max:100',
            'periode_perhitungan' => 'required|string|max:100',
            'status_komponen' => 'required|string|max:100',
            'level_karyawan_id' => 'required|exists:level_karyawans,id',
            'cek_komponen' => 'nullable|boolean',
            'is_kehadiran' => 'nullable|boolean',
        ]);

        // Pastikan boolean terset benar
        $validated['cek_komponen'] = $request->has('cek_komponen');
        $validated['is_kehadiran'] = $request->has('is_kehadiran');

        KomponenPenghasilan::create($validated);

        return redirect()->route('komponen_penghasilan.index')
            ->with('success', 'Data berhasil ditambahkan.');
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
            'is_kehadiran' => 'nullable|boolean',
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
            'cek_komponen' => $request->has('cek_komponen') ? 1 : 0,
            'is_kehadiran' => $request->has('is_kehadiran') ? 1 : 0,
        ]);

        return redirect()->route('komponen_penghasilan.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $komponen_penghasilan = KomponenPenghasilan::findOrFail($id);



        // Hapus komponen_penghasilan dari database
        $komponen_penghasilan->delete();

        return redirect()->route('komponen_penghasilan.index')->with('success', 'komponen_penghasilan berhasil dihapus.');
    }
}

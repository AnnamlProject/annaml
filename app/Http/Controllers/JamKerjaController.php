<?php

namespace App\Http\Controllers;

use App\Jamkerja;
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    //
    public function index()
    {
        $data = Jamkerja::all();
        return view('jam_kerja.index', compact('data'));
    }
    public function create()
    {
        return view('jam_kerja.create');
    }
    public function edit($id)
    {
        $data = Jamkerja::findOrFail($id);
        return view('jam_kerja.edit', compact('data'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jam_masuk'  => ['required', 'date_format:H:i'],
            'jam_keluar' => ['required', 'date_format:H:i', 'after:jam_masuk'],
        ]);

        $jamKerja = Jamkerja::findOrFail($id);
        $jamKerja->update([
            'jam_masuk'  => $validated['jam_masuk'],
            'jam_keluar' => $validated['jam_keluar'],
        ]);

        return redirect()
            ->route('jam_kerja.index')
            ->with('success', 'Jam Kerja berhasil diupdate.');
    }
}

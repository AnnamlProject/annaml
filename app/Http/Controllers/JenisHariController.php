<?php

namespace App\Http\Controllers;

use App\JenisHari;
use Illuminate\Http\Request;

class JenisHariController extends Controller
{
    //
    public function index()
    {
        $data = JenisHari::all();
        return view('jenis_hari.index', compact('data'));
    }
    public function create()
    {
        return view('jenis_hari.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Hapus 'required' karena bisa auto-generate

            'nama' => 'string|max:255',
            'deskripsi' => 'nullable|string',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
        ]);


        JenisHari::create($validated);

        return redirect()->route('jenis_hari.index')->with('success', 'Location Asset created successfully.');
    }
    public function show($id)
    {
        $data = JenisHari::findOrFail($id);
        return view('jenis_hari.show', compact('data'));
    }
    public function edit($id)
    {
        $data = JenisHari::findOrFail($id);
        return view('jenis_hari.edit', compact('data'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'string|max:255',
            'deskripsi' => 'nullable|string',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',

        ]);

        $jenis_hari = JenisHari::findOrFail($id);

        $jenis_hari->update($request->all());

        return redirect()->route('jenis_hari.index')->with('success', 'jenis hari update successfully.');
    }
    public function destroy($id)
    {
        $jenis_hari = JenisHari::findOrFail($id);

        $jenis_hari->delete();

        return redirect()->route('jenis_hari.index')->with('success', ' Data berhasil dihapus.');
    }
}

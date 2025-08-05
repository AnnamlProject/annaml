<?php

namespace App\Http\Controllers;

use App\Jabatan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    //
    public function index(): View
    {
        $jabatans = Jabatan::latest()->paginate(10);
        return view('jabatan.index', compact('jabatans'));
    }

    public function create(): View
    {
        return view('jabatan.create');
    }
    private function generateKodeJabatan()
    {
        $last = \App\Jabatan::orderBy('kd_jabatan', 'desc')->first();

        if ($last && preg_match('/JAB-(\d+)/', $last->kd_jabatan, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'JAB-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Hapus 'required' karena bisa auto-generate
            'kd_jabatan' => 'nullable|string|max:255',
            'nama_jabatan' => 'nullable|string|max:255',
            'desc_jabatan' => 'nullable|string|max:255'
        ]);

        // Kalau kosong (auto generate), buat kode otomatis
        if (empty($validated['kd_jabatan'])) {
            $validated['kd_jabatan'] = $this->generateKodeJabatan();
        }

        Jabatan::create($validated);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan created successfully.');
    }
    public function show($kd_jabatan)
    {
        $jabatans = Jabatan::where('kd_jabatan', $kd_jabatan)->firstOrFail();
        return view('jabatan.show', compact('jabatans'));
    }
    public function edit($kd_jabatan)
    {
        $jabatan = Jabatan::where('kd_jabatan', $kd_jabatan)->firstOrFail();
        return view('jabatan.edit', compact('jabatan'));
    }
    public function update(Request $request, $kd_jabatan)
    {
        $jabatan = Jabatan::where('kd_jabatan', $kd_jabatan)->firstOrFail();
        $validated = $request->validate([
            'kd_jabatan' => 'nullable|string|max:255',
            'nama_jabatan' => 'nullable|string|max:255',
            'desc_jabatan' => 'nullable|string|max:255'
        ]);

        $jabatan->update($validated);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan updated successfully.');
    }
    public function destroy($kd_jabatan): RedirectResponse
    {
        //get post by ID
        $jabatan = Jabatan::where('kd_jabatan', $kd_jabatan)->firstOrFail();

        //delete post
        $jabatan->delete();

        //redirect to index
        return redirect()->route('jabatan.index')->with('success', 'Jabatan deleted successfully.');
    }
}

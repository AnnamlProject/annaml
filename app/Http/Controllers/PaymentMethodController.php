<?php

namespace App\Http\Controllers;

use App\Models\PayementMethod;
use App\PaymentMethod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    //
    public function index()
    {
        $data = PaymentMethod::latest()->paginate(10);
        return view('PaymentMethod.index', compact('data'));
    }
    public function create()
    {
        return view('PaymentMethod.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_jenis' => 'required|string',
            'nama_jenis' => 'required|string',
            'status' => 'required|boolean'

        ]);

        PaymentMethod::create($request->all());

        return redirect()->route('PaymentMethod.index')->with('success', 'Data berhasil ditambahkan.');
    }
    public function show($id)
    {
        $PaymentMethod = PaymentMethod::findOrFail($id);

        return view('PaymentMethod.show', compact('PaymentMethod'));
    }
    public function edit(string $id): View
    {
        //get post by ID
        $PaymentMethod = PaymentMethod::findOrFail($id);

        return view('PaymentMethod.edit', compact('PaymentMethod'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_jenis' => 'required|string',
            'nama_jenis' => 'required|string',
            'status' => 'required|boolean'
        ]);

        $PayementMethod = PaymentMethod::findOrFail($id);

        $PayementMethod->update($request->all());

        return redirect()->route('PaymentMethod.index')->with('success', 'Data berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $PayementMethod = PaymentMethod::findOrFail($id);

        $PayementMethod->delete();

        return redirect()->route('PaymentMethod.index')->with('success', 'Data berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Customers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomersController extends Controller
{
    //

    public function index(): View
    {
        $customers = Customers::latest()->paginate(5);
        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }
    private function generateKodeCustomers()
    {
        $last = \App\Customers::orderBy('kd_customers', 'desc')->first();

        if ($last && preg_match('/PER-(\d+)/', $last->kd_customers, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'CUS-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Hapus 'required' karena bisa auto-generate
            'kd_customers' => 'nullable|string|max:255',
            'nama_customers' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:255',
            'limit_kredit' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:20'
        ]);

        // Kalau kosong (auto generate), buat kode otomatis
        if (empty($validated['kd_customers'])) {
            $validated['kd_customers'] = $this->generateKodeCustomers();
        }

        Customers::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customers created successfully.');
    }
    public function show($kd_customers)
    {
        $customer = Customers::where('kd_customers', $kd_customers)->firstOrFail();
        return view('customers.show', compact('customer'));
    }

    public function edit($kd_customers)
    {
        $customers = Customers::where('kd_customers', $kd_customers)->firstOrFail();
        return view('customers.edit', compact('customers'));
    }

    public function update(Request $request, $kd_customers)
    {
        $customers = Customers::where('kd_customers', $kd_customers)->firstOrFail();
        $validated = $request->validate([
            'kd_customers' => 'nullable|string|max:255',
            'nama_customers' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:255',
            'limit_kredit' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:20'
        ]);

        $customers->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customers updated successfully.');
    }
    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $customers = Customers::findOrFail($id);

        //delete post
        $customers->delete();

        //redirect to index
        return redirect()->route('customers.index')->with('success', 'Customers deleted successfully.');
    }
}

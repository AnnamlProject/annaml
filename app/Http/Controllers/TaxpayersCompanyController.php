<?php

namespace App\Http\Controllers;

use App\TaxpayersProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaxpayersCompanyController extends Controller
{
    //

    public function index(): View
    {
        $taxpayers = TaxpayersProfile::all();
        return view('taxpayers_company.index', compact('taxpayers'));
    }
    public function create(): View
    {
        return view('taxpayers_company.create');
    }
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'nama_perusahaan'     => 'required|min:5',
            'jalan'   => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kota' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'kode_pos' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|string',
            'logo'     => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'bentuk_badan_hukum' => 'nullable|string',
            'npwp' => 'nullable|string',
            'klu_code' => 'nullable|string',
            'klu_description' => 'nullable|string',
            'tax_office' => 'nullable|string'

        ]);

        //upload image
        $image = $request->file('logo');
        $imageName = null;

        if ($image) {
            $image->storeAs('public/taxpayers', $image->hashName());
            $imageName = $image->hashName();
        }


        // simpan data dan ambil objeknya
        $profile = TaxpayersProfile::create([
            'logo'     => $imageName,
            'nama_perusahaan'     => $request->nama_perusahaan,
            'jalan'   => $request->jalan,
            'kelurahan' => $request->kelurahan,
            'kecamatan'     => $request->kecamatan,
            'kota'   => $request->kota,
            'provinsi' => $request->provinsi,
            'kode_pos' => $request->kode_pos,
            'phone_number'     => $request->phone_number,
            'email'   => $request->email,
            'bentuk_badan_hukum' => $request->bentuk_badan_hukum,
            'npwp' => $request->npwp,
            'klu_code' => $request->klu_code,
            'klu_description' => $request->klu_description,
            'tax_office' => $request->tax_office
        ]);

        // redirect ke show dengan id
        return redirect()->route('taxpayers_company.show', $profile->id)->with('success', 'Data Berhasil Disimpan!');
    }
    public function show(string $id): View
    {
        //get post by ID
        $taxpayers = TaxpayersProfile::findOrFail($id);

        //render view with post
        return view('taxpayers_company.show', compact('taxpayers'));
    }
    public function edit(string $id): View
    {
        //get post by ID
        $taxpayers = TaxpayersProfile::findOrFail($id);

        //render view with post
        return view('taxpayers_company.edit', compact('taxpayers'));
    }
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'nama_perusahaan'     => 'required|min:5',
            'jalan'   => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kota' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'kode_pos' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|string',
            'logo'     => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        //get post by ID
        $taxpayers = TaxpayersProfile::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('logo')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/taxpayers', $image->hashName());

            //delete old image
            Storage::delete('public/taxpayers/' . $taxpayers->logo);

            //update post with new image
            $taxpayers->update([
                'image'     => $image->hashName(),
                'nama_perusahaan'     => $request->nama_perusahaan,
                'jalan'   => $request->jalan,
                'kelurahan' => $request->kelurahan,
                'kecamatan'     => $request->kecamatan,
                'kota'   => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'phone_number'     => $request->phone_number,
                'email'   => $request->email,
                'bentuk_badan_hukum' => $request->bentuk_badan_hukum,
                'npwp' => $request->npwp,
                'klu_code' => $request->klu_code,
                'klu_description' => $request->klu_description,
                'tax_office' => $request->tax_office
            ]);
        } else {

            //update post without image
            $taxpayers->update([
                'nama_perusahaan'     => $request->nama_perusahaan,
                'jalan'   => $request->jalan,
                'kelurahan' => $request->kelurahan,
                'kecamatan'     => $request->kecamatan,
                'kota'   => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'phone_number'     => $request->phone_number,
                'email'   => $request->email,
                'bentuk_badan_hukum' => $request->bentuk_badan_hukum,
                'npwp' => $request->npwp,
                'klu_code' => $request->klu_code,
                'klu_description' => $request->klu_description,
                'tax_office' => $request->tax_office
            ]);
        }

        //redirect to index
        return redirect()->route('taxpayers_company.show', $taxpayers->id)->with(['success' => 'Data Berhasil Diubah!']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Jabatan;
use App\LevelKaryawan;
use App\Ptkp;
use App\UnitKerja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    //

    public function index()
    {
        $employees = Employee::with(['jabatan', 'ptkp', 'levelKaryawan', 'unitKerja'])->latest()->paginate(5);

        $level_karyawan = LevelKaryawan::pluck('nama_level')->filter()->unique()->values();

        return view('employee.index', compact('employees', 'level_karyawan'));
    }


    public function create()
    {
        $levels = LevelKaryawan::all();
        $ptkps = Ptkp::all();
        $jabatans = Jabatan::all();
        $units = UnitKerja::all();
        return view('employee.create', compact('levels', 'ptkps', 'jabatans', 'units'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_karyawan' => 'required|string',
            'nama_karyawan' => 'required|string',
            'nik' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'agama' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'ptkp_id' => 'required|exists:ptkps,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'level_kepegawaian_id' => 'required|exists:level_karyawans,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'status_pegawai' => 'nullable|string',
            'sertifikat' => 'nullable|string',
            'photo' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_ktp' => 'nullable|mimes:jpg,jpeg,png,pdf'

        ]);

        // Simpan file jika ada
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('foto_ktp')) {
            $validated['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'public');
        }

        Employee::create($validated);

        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }
    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        return view('employee.show', compact('employee'));
    }
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $levels = LevelKaryawan::all();
        $ptkps = Ptkp::all();
        $jabatans = Jabatan::all();
        $units = UnitKerja::all();

        return view('employee.edit', compact('employee', 'ptkps', 'levels', 'jabatans', 'units'));
    }
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'kode_karyawan' => 'required|string',
            'nama_karyawan' => 'required|string',
            'nik' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'agama' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'ptkp_id' => 'required|exists:ptkps,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'level_kepegawaian_id' => 'required|exists:level_karyawans,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'status_pegawai' => 'nullable|string',
            'sertifikat' => 'nullable|string',
            'photo' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'foto_ktp' => 'nullable|mimes:jpg,jpeg,png,pdf'
        ]);

        //get post by ID
        $employee = Employee::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('logo')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/employee', $image->hashName());

            //delete old image
            Storage::delete('public/employee/' . $employee->logo);

            //update post with new image
            $employee->update([
                'kode_karyawan'     => $image->hashName(),
                'nama_karyawan'     => $request->nama_perusahaan,
                'nik'   => $request->jalan,
                'tempat_lahir' => $request->kelurahan,
                'tanggal_lagir'     => $request->kecamatan,
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
            $employee->update([
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
        return redirect()->route('taxpayers_company.show', $employee->id)->with(['success' => 'Data Berhasil Diubah!']);
    }
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->delete();

        return redirect()->route('employee.index')->with('success', ' Data berhasil dihapus.');
    }
}

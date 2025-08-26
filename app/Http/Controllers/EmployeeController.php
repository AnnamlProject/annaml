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
            'foto_ktp' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'rfid_code' => 'required|string|unique:employees,rfid_code',


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
        $employee = Employee::with(['jabatan', 'ptkp', 'levelKaryawan', 'unitKerja'])->findOrFail($id);

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
        // Validasi form
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
            'foto_ktp' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'rfid_code' => 'nullable|string|unique:employees,rfid_code,' . $id,
        ]);

        $employee = Employee::findOrFail($id);

        // handle upload photo jika ada
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo->storeAs('public/employee', $photo->hashName());

            // hapus foto lama
            if ($employee->photo) {
                Storage::delete('public/employee/' . $employee->photo);
            }

            $employee->photo = $photo->hashName();
        }

        if ($request->hasFile('foto_ktp')) {
            $ktp = $request->file('foto_ktp');
            $ktp->storeAs('public/employee', $ktp->hashName());

            // hapus KTP lama
            if ($employee->foto_ktp) {
                Storage::delete('public/employee/' . $employee->foto_ktp);
            }

            $employee->foto_ktp = $ktp->hashName();
        }

        // update data lain
        $employee->update($request->except(['photo', 'foto_ktp']));

        return redirect()->route('employee.show', $employee->id)
            ->with(['success' => 'Data Berhasil Diubah!']);
    }
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->delete();

        return redirect()->route('employee.index')->with('success', ' Data berhasil dihapus.');
    }
}

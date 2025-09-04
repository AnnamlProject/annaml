<?php

namespace App\Http\Controllers;

use App\BonusKaryawan;
use App\Employee;
use App\Exports\EmployeeExport;
use App\JenisHari;
use App\ShiftKaryawanWahana;
use App\UnitKerja;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftKaryawanWahanaController extends Controller
{
    //
    public function index()
    {
        $data = ShiftKaryawanWahana::with([
            'karyawan',
            'unitKerja',
            'wahana',
            'jenisHari'
        ])
            ->join('unit_kerjas', 'shift_karyawan_wahanas.unit_kerja_id', '=', 'unit_kerjas.id')
            ->orderBy('unit_kerjas.nama_unit')
            ->select('shift_karyawan_wahanas.*')
            ->get();
        return view('shift_karyawan.index', compact('data'));
    }
    public function create()
    {
        $karyawan = Employee::all();
        $unitKerja = UnitKerja::all();
        $jenisHari = JenisHari::all();
        return view('shift_karyawan.create', compact('karyawan', 'unitKerja', 'jenisHari'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'wahana_id'     => 'required|exists:wahanas,id',
            'tanggal'       => 'required|date',
            'jenis_hari_id' => 'required|exists:jenis_haris,id',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'status'        => 'required|in:Penetapan,Perubahan,Tambahan',
            'keterangan'    => 'nullable|string'
        ]);

        // Hitung lama jam kerja
        $jamMulai   = Carbon::parse($request->jam_mulai);
        $jamSelesai = Carbon::parse($request->jam_selesai);
        $lamaJam    = $jamMulai->diffInMinutes($jamSelesai) / 60;

        // Ambil default jam kerja dari jenis_haris
        $jenisHari   = \App\JenisHari::find($request->jenis_hari_id);
        $defaultJam  = null;
        $persentase  = null;

        if ($jenisHari && $jenisHari->jam_mulai && $jenisHari->jam_selesai) {
            $defaultMulai   = Carbon::parse($jenisHari->jam_mulai);
            $defaultSelesai = Carbon::parse($jenisHari->jam_selesai);
            $defaultJam     = $defaultMulai->diffInMinutes($defaultSelesai) / 60;

            if ($defaultJam > 0) {
                $persentase = $lamaJam / $defaultJam;
            }
        }

        try {
            // Simpan shift
            $shift = ShiftKaryawanWahana::create([
                'employee_id'    => $request->employee_id,
                'unit_kerja_id'  => $request->unit_kerja_id,
                'wahana_id'      => $request->wahana_id,
                'tanggal'        => $request->tanggal,
                'jenis_hari_id'  => $request->jenis_hari_id,
                'jam_mulai'      => $request->jam_mulai,
                'jam_selesai'    => $request->jam_selesai,
                'lama_jam'       => $lamaJam,
                'persentase_jam' => $persentase,
                'status'         => $request->status,
                'keterangan'     => $request->keterangan
            ]);

            // Buat bonus pending
            BonusKaryawan::create([
                'employee_id'   => $shift->employee_id,
                'shift_id'      => $shift->id,
                'tanggal'       => $shift->tanggal,
                'jenis_hari_id' => $shift->jenis_hari_id,
                'bonus'         => 0,
                'transportasi'  => 0,
                'total'         => 0,
                'status'        => 'Pending', // default
            ]);

            return redirect()->route('shift_karyawan.index')
                ->with('success', 'Shift karyawan + Bonus Pending berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan shift: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = ShiftKaryawanWahana::findOrFail($id);
        return view('shift_karyawan.show', compact('data'));
    }
    public function edit($id)
    {
        $shift_karyawan = ShiftKaryawanWahana::findOrFail($id);
        $karyawan = Employee::all();
        $unitKerja = UnitKerja::all();
        $jenisHari = JenisHari::all();

        return view('shift_karyawan.edit', compact('shift_karyawan', 'karyawan', 'unitKerja', 'jenisHari'));
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'unit_kerja_id' => 'required|exists:unit_kerjas,id',
            'wahana_id'     => 'required|exists:wahanas,id',
            'tanggal'       => 'required|date',
            'jenis_hari_id' => 'required|exists:jenis_haris,id',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'status'        => 'required|in:Penetapan,Perubahan,Tambahan',
            'keterangan'    => 'nullable|string'
        ]);

        // Hitung lama jam
        $jamMulai   = Carbon::parse($request->jam_mulai);
        $jamSelesai = Carbon::parse($request->jam_selesai);
        $lamaJam    = $jamMulai->diffInMinutes($jamSelesai) / 60;

        // Ambil default jam kerja dari jenis_haris
        $jenisHari   = JenisHari::find($request->jenis_hari_id);
        $defaultJam  = null;
        $persentase  = null;

        if ($jenisHari && $jenisHari->jam_mulai && $jenisHari->jam_selesai) {
            $defaultMulai   = Carbon::parse($jenisHari->jam_mulai);
            $defaultSelesai = Carbon::parse($jenisHari->jam_selesai);
            $defaultJam     = $defaultMulai->diffInMinutes($defaultSelesai) / 60;

            if ($defaultJam > 0) {
                $persentase = $lamaJam / $defaultJam;
            }
        }

        try {
            // Update shift
            $shift = ShiftKaryawanWahana::findOrFail($id);

            $shift->update([
                'employee_id'    => $request->employee_id,
                'unit_kerja_id'  => $request->unit_kerja_id,
                'wahana_id'      => $request->wahana_id,
                'tanggal'        => $request->tanggal,
                'jenis_hari_id'  => $request->jenis_hari_id,
                'jam_mulai'      => $request->jam_mulai,
                'jam_selesai'    => $request->jam_selesai,
                'lama_jam'       => $lamaJam,
                'persentase_jam' => $persentase,
                'status'         => $request->status,
                'keterangan'     => $request->keterangan
            ]);

            // Update bonus yang terkait shift ini (jika ada)
            $bonus = BonusKaryawan::where('shift_id', $shift->id)->first();
            if ($bonus) {
                $bonus->update([
                    'tanggal'       => $request->tanggal,
                    'jenis_hari_id' => $request->jenis_hari_id,
                    'status'        => 'Pending', // reset agar dihitung ulang
                    'keterangan'    => 'Bonus reset karena shift diperbarui'
                ]);
            }

            return redirect()->route('shift_karyawan.index')
                ->with('success', 'Shift karyawan berhasil diperbarui, bonus diset ke Pending.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal update shift: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $shift_karyawan = ShiftKaryawanWahana::findOrFail($id);

        $shift_karyawan->delete();

        return redirect()->route('shift_karyawan.index')->with('success', ' Data berhasil dihapus.');
    }
}

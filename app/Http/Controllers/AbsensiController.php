<?php

namespace App\Http\Controllers;

use App\Absensi;
use App\Employee;
use App\Jamkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    //
    public function form()
    {
        $absensis = Absensi::with('employee')->latest()->take(10)->get();
        return view('absensi.scan', compact('absensis'));
    }
    public function scan(Request $request)
    {
        $rfid = $request->input('rfid');
        if (!$rfid) {
            return back()->with('error', 'RFID tidak dikirim');
        }

        $tanggal = now()->toDateString();
        $jam     = now()->toTimeString();

        // Cari karyawan
        $karyawan = Employee::where('rfid_code', $rfid)->first();
        if (!$karyawan) {
            return back()->with('error', 'RFID tidak ditemukan');
        }

        // Ambil jam kerja default
        $jamKerja = Jamkerja::first();
        if (!$jamKerja) {
            return back()->with('error', 'Jam kerja belum disetting');
        }

        $statuses = Absensi::where('employee_id', $karyawan->id)
            ->whereDate('tanggal', $tanggal)
            ->pluck('status')
            ->toArray();

        // Logging isi status
        Log::info("Absensi scan - {$karyawan->nama_karyawan}", [
            'tanggal'   => $tanggal,
            'jam'       => $jam,
            'statuses'  => $statuses,
        ]);

        $jamSekarang = strtotime($jam);
        $jamMasuk    = strtotime($jamKerja->jam_masuk);
        $jamPulang   = strtotime($jamKerja->jam_keluar);
        $toleransi   = 3600; // 1 jam

        if (count($statuses) == 0) {
            // Absen pertama kali
            if ($jamSekarang <= $jamMasuk + $toleransi) {
                $status = 'Masuk';
                $pesan  = "Anda berhasil absen masuk tepat waktu.";
            } else {
                $status = 'Terlambat';
                $pesan  = "Anda berhasil absen masuk, tetapi terlambat.";
            }
        } elseif (in_array('Masuk', $statuses) || in_array('Terlambat', $statuses)) {
            // Sudah pernah absen masuk (tepat waktu atau terlambat)
            if ($jamSekarang >= $jamPulang - $toleransi) {
                $status = 'Pulang';
                $pesan  = "Anda berhasil absen pulang.";
            } else {
                return back()->with('error', 'Anda sudah absen masuk hari ini.');
            }
        } else {
            return back()->with('error', 'Anda sudah lengkap absen Masuk & Pulang hari ini.');
        }

        // Simpan absensi baru
        Absensi::create([
            'employee_id' => $karyawan->id,
            'tanggal'     => $tanggal,
            'jam'         => $jam,
            'status'      => $status,
        ]);

        Log::info("Absensi berhasil disimpan", [
            'employee_id' => $karyawan->id,
            'status'      => $status,
        ]);

        return back()->with('success', "{$karyawan->nama_karyawan} - $pesan");
    }
}

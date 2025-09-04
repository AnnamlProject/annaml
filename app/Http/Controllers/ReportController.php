<?php

namespace App\Http\Controllers;

use App\Absensi;
use App\chartOfAccount;
use App\UnitKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class ReportController extends Controller
{
    //
    public function reportAccount()
    {
        $chartOfAccounts = ChartOfAccount::with('klasifikasiAkun')
            ->orderBy('kode_akun')
            ->get();

        // DATA DUMMY (jika kosong)
        if ($chartOfAccounts->isEmpty()) {
            $chartOfAccounts = collect([
                (object)[
                    'kode_akun' => '10000',
                    'nama_akun' => 'AKTIVA',
                    'level_akun' => 'HEADER',
                    'tipe_akun' => 'ASET'
                ],
                (object)[
                    'kode_akun' => '11000',
                    'nama_akun' => 'AKTIVA LANCAR',
                    'level_akun' => 'GROUP ACCOUNT',
                    'tipe_akun' => 'ASET'
                ],
                (object)[
                    'kode_akun' => '11100',
                    'nama_akun' => 'KAS',
                    'level_akun' => 'ACCOUNT',
                    'tipe_akun' => 'ASET'
                ],
                (object)[
                    'kode_akun' => '11110',
                    'nama_akun' => 'KAS KECIL',
                    'level_akun' => 'SUB ACCOUNT',
                    'tipe_akun' => 'ASET'
                ],
            ]);
        }


        foreach ($chartOfAccounts as $coa) {
            // Normalisasi level akun (hapus isi dalam tanda kurung jika ada)
            $coa->level_akun = strtoupper(trim(preg_replace('/\s*\(.*?\)/', '', $coa->level_akun)));

            // Hitung indentasi level (untuk margin di blade)
            $coa->level_indent = $this->getLevelIndent($coa->kode_akun);

            // Deteksi apakah memiliki child
            $coa->has_child = $chartOfAccounts->contains(function ($child) use ($coa) {
                return $child->kode_akun !== $coa->kode_akun &&
                    $this->getLevelIndent($child->kode_akun) > $coa->level_indent &&
                    substr($child->kode_akun, 0, $this->getKodePrefixLength($coa->level_indent)) ===
                    substr($coa->kode_akun, 0, $this->getKodePrefixLength($coa->level_indent));
            });
        }

        return view('report.account', compact('chartOfAccounts'));
    }


    private function getLevelIndent($kodeAkun)
    {
        // Buang trailing zero
        $kodeSignifikan = rtrim($kodeAkun, '0');
        $panjang = strlen($kodeSignifikan);

        // Tentukan level berdasarkan panjang digit signifikan
        switch ($panjang) {
            case 1:
                return 0; // HEADER
            case 2:
                return 1; // GROUP
            case 3:
                return 2; // ACCOUNT
            default:
                return 3; // SUB ACCOUNT
        }
    }

    private function getKodePrefixLength($levelIndent)
    {
        // Kembalikan jumlah digit yang dipakai untuk identifikasi parent
        switch ($levelIndent) {
            case 0:
                return 1;
            case 1:
                return 2;
            case 2:
                return 3;
            default:
                return 4;
        }
    }

    public function reportKlasifikasi()
    {
        $data = chartOfAccount::with('klasifikasiAkun')->orderBy('kode_akun');
        return view('report.klasifikasi', compact('data'));
    }
    public function reportDepartemenAkun()
    {
        $akuns = \App\ChartOfAccount::orderBy('kode_akun')->get();
        $departemens = \App\Departement::orderBy('deskripsi')->get();

        // Ambil data relasi departemen-akun
        $relasi = \App\departemenAkun::get();

        // Buat array untuk keperluan cek cepat
        $relasiMap = [];
        foreach ($relasi as $item) {
            $relasiMap[$item->akun_id][$item->departemen_id] = true;
        }

        return view('report.departemen-akun', compact('akuns', 'departemens', 'relasiMap'));
    }
    public function filter()
    {
        $units = UnitKerja::all();
        return view('report.absensi.filter', compact('units'));
    }
    public function hasil(Request $request)
    {
        $filterType = $request->get('filter', 'custom');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $unitId    = $request->get('unit_id');

        // Logic tanggal otomatis
        if ($filterType === 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate   = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($filterType === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate   = Carbon::now()->endOfMonth()->toDateString();
        }

        // Fallback kalau custom tapi tidak isi tanggal
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate   = Carbon::now()->endOfMonth()->toDateString();
        }

        // Query absensi
        $query = Absensi::with(['employee.levelKaryawan', 'employee.unitKerja'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
            ->when($unitId, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('unit_kerja_id', $unitId)));

        $rekap = $query->get()
            ->groupBy('employee_id')
            ->map(function ($rows) {
                $pegawai = $rows->first()->employee;

                // Hari kerja (Masuk + Terlambat)
                $totalHari = $rows->whereIn('status', ['Masuk', 'Terlambat'])->count();

                // Hitung lembur (Lembur Masuk + Lembur Pulang per tanggal)
                $totalLembur = $rows->groupBy('tanggal')
                    ->map(function ($absenPerTanggal) {
                        $masuk  = $absenPerTanggal->firstWhere('status', 'Lembur Masuk');
                        $pulang = $absenPerTanggal->firstWhere('status', 'Lembur Pulang'); // perbaikan di sini

                        if ($masuk && $pulang) {
                            $masukJam  = \Carbon\Carbon::parse($masuk->jam);
                            $pulangJam = \Carbon\Carbon::parse($pulang->jam);

                            return $pulangJam->diffInMinutes($masukJam);
                        }
                        return 0;
                    })
                    ->sum(); // jumlahkan hasil per tanggal

                return [
                    'pegawai'      => $pegawai->nama_karyawan ?? '-',
                    'level'        => $pegawai->levelKaryawan->nama_level ?? '-',
                    'unit'         => $pegawai->unitKerja->nama_unit ?? '-',
                    'total_hari'   => $totalHari,
                    'total_lembur' => round($totalLembur / 60, 2), // dalam jam
                ];
            });

        $unitName = $unitId ? UnitKerja::find($unitId)->nama_unit : 'Semua Unit';

        return view('report.absensi.hasil', compact('rekap', 'startDate', 'endDate', 'filterType', 'unitName'));
    }
    public function exportPdf(Request $request)
    {
        // Ambil rekap ulang dari hasil()
        $rekapData = $this->getRekapData($request);

        $pdf = Pdf::loadView('report.absensi.pdf', $rekapData)
            ->setPaper('A4', 'landscape');

        return $pdf->download("rekap-absensi-{$rekapData['startDate']}-{$rekapData['endDate']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $rekapData = $this->getRekapData($request);

        return Excel::download(
            new \App\Exports\RekapAbsensiExport($rekapData['rekap']),
            "rekap-absensi-{$rekapData['startDate']}-{$rekapData['endDate']}.xlsx"
        );
    }

    // Helper untuk ambil data rekap biar tidak duplikasi
    private function getRekapData(Request $request)
    {
        $filterType = $request->get('filter', 'custom');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $unitId    = $request->get('unit_id');

        // Logic tanggal otomatis
        if ($filterType === 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate   = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($filterType === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate   = Carbon::now()->endOfMonth()->toDateString();
        }

        // Fallback kalau custom tapi tidak isi tanggal
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate   = Carbon::now()->endOfMonth()->toDateString();
        }

        // Query absensi
        $query = Absensi::with(['employee.levelKaryawan', 'employee.unitKerja'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
            ->when($unitId, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('unit_kerja_id', $unitId)));

        $rekap = $query->get()
            ->groupBy('employee_id')
            ->map(function ($rows) {
                $pegawai = $rows->first()->employee;

                $totalHari = $rows->whereIn('status', ['Masuk', 'Terlambat'])->count();

                $totalLembur = $rows->groupBy('tanggal')
                    ->map(function ($absenPerTanggal) {
                        $masuk  = $absenPerTanggal->firstWhere('status', 'Lembur Masuk');
                        $pulang = $absenPerTanggal->firstWhere('status', 'Lembur Pulang');

                        if ($masuk && $pulang) {
                            $masukJam  = \Carbon\Carbon::parse($masuk->jam);
                            $pulangJam = \Carbon\Carbon::parse($pulang->jam);

                            return $pulangJam->diffInMinutes($masukJam);
                        }
                        return 0;
                    })
                    ->sum();

                return [
                    'pegawai'      => $pegawai->nama_karyawan ?? '-',
                    'level'        => $pegawai->levelKaryawan->nama_level ?? '-',
                    'unit'         => $pegawai->unitKerja->nama_unit ?? '-',
                    'total_hari'   => $totalHari,
                    'total_lembur' => round($totalLembur / 60, 2), // dalam jam
                ];
            });

        $unitName = $unitId ? UnitKerja::find($unitId)->nama_unit : 'Semua Unit';

        return [
            'rekap'      => $rekap,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'filterType' => $filterType,
            'unitName'   => $unitName,
        ];
    }
}

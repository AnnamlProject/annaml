<?php

namespace App\Http\Controllers;

use App\chartOfAccount;
use Illuminate\Http\Request;

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
}

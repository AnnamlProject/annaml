<?php

namespace App\Imports;

use App\JenisHari;
use App\TransaksiWahana;
use App\UnitKerja;
use App\Wahana;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class TransaksiWahanaImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        // Cari ID unit kerja dari nama
        $jenis_hari = JenisHari::where('nama', $row['jenis_hari'])->first();
        $wahana = Wahana::where('nama_wahana', $row['wahana'])->first();
        $unitKerja = UnitKerja::where('nama_unit', $row['unit_kerja'])->first();

        if (!$unitKerja) {
            throw ValidationException::withMessages(['unit_kerja' => "unit kerja '{$row['unit_kerja']}' tidak ditemukan."]);
        }
        if (!$wahana) {
            throw ValidationException::withMessages(['wahana' => "wahana '{$row['wahana']}' tidak ditemukan."]);
        }
        if (!$jenis_hari) {
            throw ValidationException::withMessages(['jenis_hari' => "Jenis Hari '{$row['jenis_hari']}' tidak ditemukan."]);
        }

        return new TransaksiWahana([
            'wahana_id'   => $wahana->id ?? null,
            'unit_kerja_id' => $unitKerja->id ?? null, // bisa pakai null/0 jika tidak ditemukan
            'jenis_hari_id' => $jenis_hari->id ?? null,
            'tanggal'   => $this->transformDate($row['tanggal']),
            'realisasi'        => $row['realisasi'],
            'jumlah_pengunjung'     => $row['jumlah_pengunjung'] ?? null,

        ]);
    }
    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

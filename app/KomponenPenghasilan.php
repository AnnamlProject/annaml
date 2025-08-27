<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KomponenPenghasilan extends Model
{
    //
    protected $fillable = [
        'nama_komponen',
        'tipe',
        'kategori',
        'sifat',
        'periode_perhitungan',
        'status_komponen',
        'level_karyawan_id',
        'cek_komponen',
        'is_kehadiran'
    ];


    public function levelKaryawan()
    {
        return $this->belongsTo(LevelKaryawan::class);
    }
}

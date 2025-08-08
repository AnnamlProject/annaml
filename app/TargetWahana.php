<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetWahana extends Model
{
    //

    protected $fillable = [
        'wahana_id',
        'jenis_hari_id',
        'target_harian',
        'tahun',
        'bulan',
        'keterangan'
    ];

    public function wahana()
    {
        return $this->belongsTo(Wahana::class);
    }
    public function jenis_hari()
    {
        return $this->belongsTo(JenisHari::class);
    }
}

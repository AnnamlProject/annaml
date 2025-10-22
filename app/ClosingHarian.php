<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClosingHarian extends Model
{
    //
    protected $fillable =
    ['wahana_id', 'unit_kerja_id', 'tanggal', 'total_omset'];



    public function UnitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
    public function wahana()
    {
        return $this->belongsTo(Wahana::class, 'wahana_id');
    }
}

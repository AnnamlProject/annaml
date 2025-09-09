<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    //

    protected $fillable = [
        'nama_unit',
        'deskripsi',
        'urutan',
    ];
}

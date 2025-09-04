<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisHari extends Model
{
    //
    protected $fillable =
    [
        'nama',
        'deskripsi',
        'jam_mulai',
        'jam_selesai',
    ];
}

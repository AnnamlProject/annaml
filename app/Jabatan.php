<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    //

    protected $fillable = [
        'kd_jabatan',
        'nama_jabatan',
        'desc_jabatan'
    ];
}

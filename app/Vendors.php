<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    //
    protected $fillable = [
        'kd_vendor',
        'nama_vendors',
        'contact_person',
        'alamat',
        'telepon',
        'email',
        'payment_terms',
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxpayersProfile extends Model
{
    //
    protected $fillable = [
        'nama_perusahaan',
        'jalan',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'logo',
        'phone_number',
        'email',
        'bentuk_badan_hukum',
        'npwp',
        'klu_code',
        'klu_description',
        'tax_office'
    ];
}

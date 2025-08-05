<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
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
        'email'
    ];
    public function legalDocuments()
    {
        return $this->hasMany(LegalDocumentCompanyProfile::class);
    }
}

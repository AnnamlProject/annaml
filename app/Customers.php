<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    //
    protected $fillable = [
        'kd_customers',
        'nama_customers',
        'contact_person',
        'alamat',
        'telepon',
        'email',
        'limit_kredit',
        'payment_terms',
    ];
}

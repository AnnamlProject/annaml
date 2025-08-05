<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class linkedAccounts extends Model
{
    //
    // use HasFactory;
    protected $fillable = ['modul', 'kode', 'akun_id'];

    public function akun()
    {
        return $this->belongsTo(ChartOfAccount::class, 'akun_id');
    }
}

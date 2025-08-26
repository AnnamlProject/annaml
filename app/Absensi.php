<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    //
    protected $fillable =
    [
        'employee_id',
        'tanggal',
        'jam',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

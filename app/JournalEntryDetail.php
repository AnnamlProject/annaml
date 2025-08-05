<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalEntryDetail extends Model
{
    //
    protected $fillable = [
        'journal_entry_id',
        'departemen_akun_id',
        'debits',
        'credits',
        'comment',
        'kode_akun'
    ];
    // Relasi: setiap detail milik satu journal entry
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
    public function akun()
    {
        return $this->belongsTo(DepartemenAkun::class, 'departemen_akun_id');
    }


    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'kode_akun', 'kode_akun');
    }
    public function departemenAkun()
    {
        return $this->belongsTo(Departement::class, 'departemen_akun_id');
    }
}

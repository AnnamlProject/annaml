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

    protected static function booted()
    {
        // Trigger ketika data diubah atau dibuat
        static::saved(function ($detail) {
            self::handleStartNewYear($detail);
        });

        // Trigger ketika data dihapus
        static::deleted(function ($detail) {
            self::handleStartNewYear($detail);
        });
    }

    protected static function handleStartNewYear($detail)
    {
        // Ambil tahun transaksi
        $tahunTransaksi = \Carbon\Carbon::parse($detail->journalEntry->tanggal)->format('Y');


        // Tahun aktif sekarang
        $tahunSekarang = now()->format('Y');

        // Kalau transaksinya bukan tahun sebelumnya, tidak perlu update
        if ($tahunTransaksi != $tahunSekarang - 1) {
            return;
        }

        // Panggil service untuk update jurnal awal tahun
        app(\App\Services\StartNewYearService::class)
            ->updateLabaTahunBerjalan($tahunTransaksi);
    }

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
        return $this->belongsTo(DepartemenAkun::class, 'departemen_akun_id');
    }
}

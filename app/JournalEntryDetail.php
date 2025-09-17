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
        'kode_akun',
        'project_id'
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
        $tanggalTransaksi = \Carbon\Carbon::parse($detail->journalEntry->tanggal);
        $tahunTransaksi   = $tanggalTransaksi->format('Y');
        $tahunSekarang    = now()->format('Y');

        // Cari periode berjalan (misalnya dari tabel start_new_years)
        $periodeAktif = \App\StartNewYear::where('tahun', $tahunTransaksi)->first();

        // Kalau tidak ada periode, hentikan
        if (!$periodeAktif) {
            return;
        }

        // Jika tanggal transaksi = tanggal akhir periode, skip
        if ($tanggalTransaksi->isSameDay(\Carbon\Carbon::parse($periodeAktif->akhir_periode))) {
            return;
        }

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
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

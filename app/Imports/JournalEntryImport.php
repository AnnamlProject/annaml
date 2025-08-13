<?php

namespace App\Imports;

use App\chartOfAccount as AppChartOfAccount;
use App\JournalEntry;
use App\JournalEntryDetail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JournalEntryImport implements ToCollection, WithHeadingRow
{
    protected $grouped = [];
    protected $skippedGroups = [];

    public function collection(Collection $rows)
    {
        try {
            // Group rows by key: source|tanggal|comment_transaksi
            foreach ($rows as $row) {
                $key = $row['source'] . '|' . $row['tanggal'] . '|' . ($row['comment_transaksi'] ?? '');
                $this->grouped[$key][] = $row;
            }

            foreach ($this->grouped as $key => $groupRows) {
                $totalDebit = 0;
                $totalKredit = 0;
                $invalidKodeAkun = false;

                foreach ($groupRows as $row) {
                    // Validasi kode akun
                    if (!\App\ChartOfAccount::where('kode_akun', $row['kode_akun'])->exists()) {
                        $invalidKodeAkun = true;
                    }

                    $totalDebit += (float) ($row['debit'] ?? 0);
                    $totalKredit += (float) ($row['kredit'] ?? 0);
                }

                // Skip jika kode akun tidak valid
                if ($invalidKodeAkun) {
                    $this->skippedGroups[] = [
                        'key' => $key,
                        'reason' => 'Kode akun tidak sesuai dengan account yang tersedia.'
                    ];
                    continue;
                }

                // Skip jika hanya 1 baris dan debit/kredit kosong
                if (count($groupRows) === 1 && ($totalDebit == 0 || $totalKredit == 0)) {
                    $this->skippedGroups[] = [
                        'key' => $key,
                        'reason' => 'Hanya 1 baris dan tidak ada debit atau kredit.'
                    ];
                    continue;
                }

                // Skip jika total debit dan kredit tidak balance
                if (round($totalDebit, 2) !== round($totalKredit, 2)) {
                    $this->skippedGroups[] = [
                        'key' => $key,
                        'reason' => 'Total debit dan kredit tidak balance.'
                    ];
                    continue;
                }

                // Simpan journal entry
                [$source, $tanggal, $comment] = explode('|', $key);

                Log::info('Menyimpan JournalEntry:', [
                    'source' => $source,
                    'tanggal' => $tanggal,
                    'comment' => $comment,
                ]);

                $journalEntry = JournalEntry::create([
                    'source'  => $source,
                    'tanggal' => $this->transformDate($tanggal),
                    'comment' => $comment,
                ]);

                Log::info('JournalEntry disimpan dengan ID: ' . $journalEntry->id);

                // Simpan detailnya
                foreach ($groupRows as $row) {
                    Log::info('Menyimpan JournalEntryDetail:', [
                        'journal_entry_id' => $journalEntry->id,
                        'kode_akun' => $row['kode_akun'],
                        'debits' => $row['debit'] ?? 0,
                        'credits' => $row['kredit'] ?? 0,
                        'comment' => $row['comment_line'] ?? null,
                    ]);

                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'kode_akun'        => $row['kode_akun'],
                        'debits'           => $row['debit'] ?? 0,
                        'credits'          => $row['kredit'] ?? 0,
                        'comment'          => $row['comment_line'] ?? null,
                    ]);
                }
            }

            // Kirim pesan error jika ada group yang dilewatkan
            if (count($this->skippedGroups) > 0) {
                $pesan = 'Beberapa transaksi dilewati: <br><ul>';
                foreach ($this->skippedGroups as $group) {
                    $pesan .= "<li><b>{$group['key']}</b> - {$group['reason']}</li>";
                }
                $pesan .= '</ul>';

                Session::flash('error', $pesan);
            } else {
                Session::flash('success', 'Semua data berhasil diimport dan seimbang.');
            }
        } catch (\Exception $e) {
            Log::error('Import gagal: ' . $e->getMessage());
            Session::flash('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function getSkippedGroups()
    {
        return $this->skippedGroups;
    }

    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Gagal konversi tanggal: ' . $value);
            return null;
        }
    }
}

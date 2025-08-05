<?php

namespace App\Imports;

use App\JournalEntry;
use App\JournalEntryDetail;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JournalEntryImport implements ToCollection, WithHeadingRow
{
    protected $grouped = [];
    protected $skippedGroups = [];

    public function collection(Collection $rows)
    {
        // 1. Kelompokkan berdasarkan source|tanggal|comment
        foreach ($rows as $row) {
            $key = $row['source'] . '|' . $row['tanggal'] . '|' . ($row['comment_transaksi'] ?? '');
            $this->grouped[$key][] = $row;
        }

        // 2. Proses tiap group
        foreach ($this->grouped as $key => $groupRows) {
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($groupRows as $row) {
                $totalDebit += (float) ($row['debit'] ?? 0);
                $totalKredit += (float) ($row['kredit'] ?? 0);
            }

            // 3. Cek keseimbangan
            if (round($totalDebit, 2) !== round($totalKredit, 2)) {
                $this->skippedGroups[] = $key;
                continue;
            }

            // 4. Simpan jika balance
            [$source, $tanggal, $comment] = explode('|', $key);

            $journalEntry = JournalEntry::create([
                'source'  => $source,
                'tanggal' => $tanggal,
                'comment' => $comment,
            ]);

            foreach ($groupRows as $row) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'kode_akun'        => $row['kode_akun'],
                    'debits'           => $row['debit'] ?? 0,
                    'credits'          => $row['kredit'] ?? 0,
                    'comment'          => $row['comment_line'] ?? null,
                ]);
            }
        }

        // 5. Kirim notifikasi ke session kalau ada yang dilewati
        if (count($this->skippedGroups) > 0) {
            $pesan = 'Beberapa transaksi dilewati karena tidak balance: <br><ul>';
            foreach ($this->skippedGroups as $group) {
                $pesan .= "<li>$group</li>";
            }
            $pesan .= '</ul>';

            Session::flash('error', $pesan);
        } else {
            Session::flash('success', 'Semua data berhasil diimport dan seimbang.');
        }
    }
    public function getSkippedGroups()
    {
        return $this->skippedGroups;
    }
}

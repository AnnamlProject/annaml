<?php

namespace App\Imports;

use App\ChartOfAccount;
use App\DepartemenAkun;
use App\JournalEntry;
use App\JournalEntryDetail;
use App\Project;
use App\Services\StartNewYearService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;


class JournalEntryImport implements ToCollection, WithHeadingRow
{
    /** @var array<string, \App\ChartOfAccount>|\Illuminate\Support\Collection */
    protected $coaByCode;

    /** @var array<string, \App\ChartOfAccount>|\Illuminate\Support\Collection */
    protected $coaByNameNorm;

    protected $grouped = [];
    protected $skippedGroups = [];
    protected $importedYears = []; // Kumpulkan tahun untuk recalculate

    public function collection(Collection $rows)
    {
        try {
            Log::info('Jumlah rows diimport: ' . $rows->count());
            // 0) Preload COA
            $allCoa = ChartOfAccount::query()->get(['kode_akun', 'nama_akun']);

            // Map by code (PAKAI CLOSURE BUKAN fn)
            $this->coaByCode = $allCoa->keyBy(function ($a) {
                return trim((string) $a->kode_akun);
            });

            // Map by normalized name (PAKAI CLOSURE)
            $this->coaByNameNorm = $allCoa->keyBy(function ($a) {
                return $this->normalizeName($a->nama_akun);
            });


            // === 1) Group rows by key: source|tanggal|comment_transaksi ===
            foreach ($rows as $row) {
                Log::debug('Row dibaca:', $row->toArray());

                $key = ($row['source'] ?? '') . '|' . ($row['tanggal'] ?? '') . '|' . ($row['comment_transaksi'] ?? '');
                $this->grouped[$key][] = $row;
            }
            Log::info('Total group terbentuk: ' . count($this->grouped));

            // === 2) Proses per group ===
            foreach ($this->grouped as $key => $groupRows) {
                $totalDebit = 0;
                $totalKredit = 0;
                $invalidAkun = false;
                $invalidDepartemen = false;
                $akunMismatch = false;

                // Validasi awal (akun, departemen, total) ---
                foreach ($groupRows as $row) {
                    // ====== Resolve akun dari kode/nama ======
                    $kodeExcel = isset($row['kode_akun']) ? trim((string)$row['kode_akun']) : null;
                    $namaExcel = isset($row['nama_akun']) ? (string)$row['nama_akun'] : null;

                    $resolved = $this->resolveAccount($kodeExcel, $namaExcel); // return model atau null + flag mismatch

                    if (!$resolved) {
                        $invalidAkun = true;
                        $this->skippedGroups[] = [
                            'reason' => "Akun tidak ditemukan. Kode: '{$kodeExcel}', Nama: '{$namaExcel}'"
                        ];
                    } elseif ($resolved === 'MISMATCH') {
                        $akunMismatch = true;
                        $this->skippedGroups[] = [
                            'reason' => "Kode dan Nama akun tidak cocok. Kode: '{$kodeExcel}', Nama: '{$namaExcel}'"
                        ];
                    } else {
                        $row['_resolved_kode_akun'] = $resolved->kode_akun;
                    }


                    // ====== Validasi Departemen (opsional) ======
                    $departemenValue = $row['departemen'] ?? null;
                    if (!empty($departemenValue)) {
                        $departemenAkun = DepartemenAkun::whereHas('departemen', function ($q) use ($departemenValue) {
                            $q->where('deskripsi', $departemenValue);
                        })->first();

                        if (!$departemenAkun) {
                            $invalidDepartemen = true;
                            $this->skippedGroups[] = [
                                'reason' => "Departemen '{$departemenValue}' tidak terdaftar. Group transaksi dilewati."
                            ];
                        }
                    }

                    $totalDebit  += (float) ($row['debit'] ?? 0);
                    $totalKredit += (float) ($row['kredit'] ?? 0);
                }

                if ($invalidAkun) {
                    $this->skippedGroups[] = ['reason' => "Akun tidak ditemukan (kode/nama tidak cocok dengan master COA)."];
                    continue;
                }
                if ($akunMismatch) {
                    $this->skippedGroups[] = ['reason' => "Kode Akun dan Nama Akun tidak konsisten pada salah satu baris."];
                    continue;
                }
                if ($invalidDepartemen) {
                    continue;
                }

                if (count($groupRows) === 1 && ($totalDebit == 0 || $totalKredit == 0)) {
                    $this->skippedGroups[] = ['reason' => 'Hanya 1 baris dan tidak ada debit atau kredit.'];
                    continue;
                }

                if (round($totalDebit, 2) !== round($totalKredit, 2)) {
                    $this->skippedGroups[] = [
                        'reason' => "Total debit ($totalDebit) dan kredit ($totalKredit) tidak balance."
                    ];
                    continue;
                }

                // === Validasi backyear: hanya boleh 1 tahun ke belakang ===
                $periodeAktif = \DB::table('start_new_years')->where('status', 'Opening')->first();
                $tahunAktif = $periodeAktif ? $periodeAktif->tahun : (int) date('Y');
                $minYear = $tahunAktif - 1; // Backyear 1 tahun

                [$source, $tanggal, $comment] = explode('|', $key);
                $tanggalParsed = $this->transformDate($tanggal);
                $tahunInput = $tanggalParsed ? (int) date('Y', strtotime($tanggalParsed)) : null;

                if ($tahunInput && $tahunInput < $minYear) {
                    $this->skippedGroups[] = [
                        'reason' => "Transaksi tanggal {$tanggalParsed} dilewati: backyear maksimal 1 tahun (minimal tahun {$minYear})."
                    ];
                    continue;
                }

                // === 3) Simpan JournalEntry master ===
                [$source, $tanggal, $comment] = explode('|', $key);

                $journalEntry = JournalEntry::create([
                    'source'  => $source,
                    'tanggal' => $this->transformDate($tanggal),
                    'comment' => $comment,
                ]);

                Log::info('JournalEntry disimpan dengan ID: ' . $journalEntry->id);

                // === 4) Simpan detail (gunakan kode_akun hasil resolve) ===
                foreach ($groupRows as $row) {
                    $departemenValue = $row['departemen'] ?? null;
                    $departemenAkun = null;

                    if (!empty($departemenValue)) {
                        $departemenAkun = DepartemenAkun::whereHas('departemen', function ($q) use ($departemenValue) {
                            $q->where('deskripsi', $departemenValue);
                        })->first();
                    }

                    $kodeResolved = $row['_resolved_kode_akun']
                        ?? (isset($row['kode_akun']) ? trim((string)$row['kode_akun']) : null);

                    // ====== Cek project (opsional) ======
                    $projectValue = $row['specpose'] ?? null;
                    $project = null;
                    if (!empty($projectValue)) {
                        $project = Project::where('nama_project', $projectValue)->first();
                        if (!$project) {
                            $this->skippedGroups[] = [
                                'reason' => "Project '{$projectValue}' tidak ditemukan. Diset NULL."
                            ];
                        }
                    }

                    JournalEntryDetail::create([
                        'journal_entry_id'   => $journalEntry->id,
                        'departemen_akun_id' => $departemenAkun ? $departemenAkun->id : null,
                        'kode_akun'          => $kodeResolved,
                        'debits'             => $row['debit'] ?? 0,
                        'credits'            => $row['kredit'] ?? 0,
                        'comment'            => $row['comment_line'] ?? null,
                        'project_id'         => $project ? $project->id : null,
                    ]);
                }

                // Kumpulkan tahun untuk recalculate
                $this->importedYears[$tahunInput] = true;
            }

            // === 5) Auto-recalculate jurnal penutup untuk tahun yang sudah closing ===
            $this->recalculateClosingForImportedYears();

            // === 6) Feedback ===
            if (count($this->skippedGroups) > 0) {
                $pesan = 'Beberapa transaksi dilewati: <br><ul>';
                foreach ($this->skippedGroups as $group) {
                    $pesan .= "<li>{$group['reason']}</li>";
                }
                $pesan .= '</ul>';
                Session::flash('error', $pesan);
            } else {
                Session::flash('success', 'Semua data berhasil diimport dan seimbang.');
            }
        } catch (\Exception $e) {
            Log::error('Import gagal: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Session::flash('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Normalisasi nama akun: trim, collapse spaces, lower.
     */
    protected function normalizeName(?string $name): ?string
    {
        if ($name === null) return null;
        // hapus spasi dobel & trim
        $norm = preg_replace('/\s+/u', ' ', trim($name));
        return Str::lower($norm);
    }

    /**
     * Resolve akun dari (kode_akun?, nama_akun?)
     * Return:
     *  - ChartOfAccount model jika ketemu & konsisten
     *  - 'MISMATCH' jika kode & nama ada tapi tidak cocok
     *  - null jika tidak ketemu
     */
    protected function resolveAccount(?string $kodeExcel, ?string $namaExcel)
    {
        $kodeExcel = $kodeExcel ? trim($kodeExcel) : null;
        $namaNorm  = $this->normalizeName($namaExcel);

        // 1) Jika ada kode → ambil by code
        if ($kodeExcel) {
            $byCode = $this->coaByCode[$kodeExcel] ?? null;
            if (!$byCode) return null;

            // Jika ada nama juga → cek konsistensi
            if ($namaNorm) {
                $namaDbNorm = $this->normalizeName($byCode->nama_akun);
                if ($namaDbNorm !== $namaNorm) {
                    return 'MISMATCH';
                }
            }
            return $byCode;
        }

        // 2) Tidak ada kode, tapi ada nama → cari by normalized exact name
        if ($namaNorm) {
            $byName = $this->coaByNameNorm[$namaNorm] ?? null;
            return $byName ?: null;
        }

        // 3) Tidak ada keduanya
        return null;
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

    /**
     * Auto-recalculate jurnal penutup untuk tahun yang sudah closing
     * Efisien: hanya recalculate tahun yang unik dan sudah closing
     */
    private function recalculateClosingForImportedYears()
    {
        foreach (array_keys($this->importedYears) as $tahun) {
            if (!$tahun) continue;

            // Cek apakah tahun sudah di-closing
            $isClosed = DB::table('start_new_years')
                ->where('tahun', $tahun)
                ->where('status', 'Closing')
                ->exists();

            if ($isClosed) {
                try {
                    $service = new StartNewYearService();
                    $service->updateLabaTahunBerjalan($tahun);
                    Log::info("Recalculated closing entries for year {$tahun}");
                } catch (\Exception $e) {
                    Log::warning('Gagal recalculate closing entries: ' . $e->getMessage());
                }
            }
        }
    }
}

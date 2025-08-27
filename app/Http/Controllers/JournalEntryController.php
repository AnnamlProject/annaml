<?php

namespace App\Http\Controllers;

use App\DepartemenAkun;
use App\JournalEntry;
use App\JournalEntryDetail;
use App\StartNewYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntryController extends Controller
{
    //

    public function index()
    {
        $data = JournalEntry::orderBy('source', 'desc')->get();
        return view('journal_entry.index', compact('data'));
    }
    public function create()
    {
        $departemenAkun = DepartemenAkun::all();
        $periodeBuku = StartNewYear::all();
        return view('journal_entry.create', compact('departemenAkun', 'periodeBuku'));
    }
    public function getAutoData(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $kodeAkun = $request->get('kode_akun');

        $periode = DB::table('start_new_years')->where('status', 'Opening')->first();

        // Kalau periode aktif tidak ada
        if (!$periode || !$tanggal) {
            return response()->json(['success' => false]);
        }

        $tahun = $periode->tahun;

        // Kalau tanggal bukan akhir periode → langsung return tanpa entries
        if ($tanggal != $periode->akhir_periode) {
            return response()->json(['success' => false]);
        }

        // Query dasar
        $query = DB::table('journal_entry_details as d')
            ->join('journal_entries as j', 'd.journal_entry_id', '=', 'j.id')
            ->whereYear('j.tanggal', $tahun);

        if ($kodeAkun) {
            $result = $query
                ->where('d.kode_akun', $kodeAkun)
                ->select(
                    'd.kode_akun',
                    DB::raw('SUM(d.debits) as total_debit'),
                    DB::raw('SUM(d.credits) as total_credit')
                )
                ->groupBy('d.kode_akun')
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => true,
                    'total_debit' => 0,
                    'total_credit' => 0
                ]);
            }

            $tipeAkun = DB::table('chart_of_accounts')
                ->where('kode_akun', $kodeAkun)
                ->value('tipe_akun');

            if (in_array($tipeAkun, ['Pendapatan', 'Beban'])) {
                $debitAkhir = $result->total_credit ?? 0;
                $creditAkhir = $result->total_debit ?? 0;
            } else {
                $debitAkhir = $result->total_debit ?? 0;
                $creditAkhir = $result->total_credit ?? 0;
            }

            return response()->json([
                'success' => true,
                'total_debit' => $debitAkhir,
                'total_credit' => $creditAkhir
            ]);
        }

        // Ambil hanya akun Pendapatan dan Beban
        $entries = $query
            ->join('chart_of_accounts as coa', 'd.kode_akun', '=', 'coa.kode_akun')
            ->whereIn('coa.tipe_akun', ['Pendapatan', 'Beban']) // 🔹 filter di sini
            ->select(
                'd.kode_akun',
                'coa.nama_akun',
                'coa.tipe_akun',
                DB::raw('SUM(d.debits) as total_debit'),
                DB::raw('SUM(d.credits) as total_credit')
            )
            ->groupBy('d.kode_akun', 'coa.nama_akun', 'coa.tipe_akun')
            ->get();


        // Balikkan untuk Pendapatan dan Beban
        $entries = $entries->map(function ($item) {
            $tipeAkun = DB::table('chart_of_accounts')
                ->where('kode_akun', $item->kode_akun)
                ->value('tipe_akun');

            if (in_array($tipeAkun, ['Pendapatan', 'Beban'])) {
                $tmp = $item->total_debit;
                $item->total_debit = $item->total_credit;
                $item->total_credit = $tmp;
            }
            return $item;
        });

        $totalDebit = $entries->sum('total_debit');
        $totalCredit = $entries->sum('total_credit');

        if ($totalDebit > $totalCredit) {
            $debitAkhir = 0;
            $creditAkhir = $totalDebit - $totalCredit;
        } elseif ($totalCredit > $totalDebit) {
            $debitAkhir = $totalCredit - $totalDebit;
            $creditAkhir = 0;
        } else {
            $debitAkhir = 0;
            $creditAkhir = 0;
        }

        $akunLaba = DB::table('chart_of_accounts')->where('level_akun', 'X')->first();

        if ($akunLaba) {
            $entries->push((object)[
                'kode_akun' => $akunLaba->kode_akun,
                'nama_akun' => $akunLaba->nama_akun,
                'total_debit' => $debitAkhir,
                'total_credit' => $creditAkhir
            ]);
        }

        return response()->json([
            'success' => true,
            'entries' => $entries
        ]);
    }


    public function store(Request $request)
    {
        // Bersihkan dan filter input
        $cleanedItems = collect($request->items)->filter(function ($item) {
            return isset($item['kode_akun']) && $item['kode_akun'] !== '';
        })->map(function ($item) {
            // Buang separator angka (titik/koma/spasi)
            $item['debits'] = isset($item['debits']) && $item['debits'] !== '' ? str_replace(['.', ','], '', $item['debits']) : null;
            $item['credits'] = isset($item['credits']) && $item['credits'] !== '' ? str_replace(['.', ','], '', $item['credits']) : null;
            return $item;
        })->values()->all();

        // Gabungkan kembali ke dalam request
        $request->merge(['items' => $cleanedItems]);

        // Validasi
        $request->validate([
            'source' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'comment' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kode_akun' => 'required|string',
            'items.*.departemen_akun_id' => 'nullable|exists:departemen_akuns,id',
            'items.*.debits' => 'nullable|numeric',
            'items.*.credits' => 'nullable|numeric',
            'items.*.comment' => 'nullable|string',
        ]);

        // Hitung total debit & kredit
        $totalDebit = collect($cleanedItems)->sum(function ($item) {
            return (float) $item['debits'];
        });

        $totalKredit = collect($cleanedItems)->sum(function ($item) {
            return (float) $item['credits'];
        });

        // Cek kesamaan debit dan kredit
        if ($totalDebit != $totalKredit) {
            return back()->withInput()->with('error', 'Total Debit (' . number_format($totalDebit, 0, ',', '.') . ') dan Total Kredit (' . number_format($totalKredit, 0, ',', '.') . ') harus sama!');
        }

        // Simpan ke database
        DB::beginTransaction();
        try {
            $entry = JournalEntry::create([
                'source' => $request->source,
                'tanggal' => $request->tanggal,
                'comment' => $request->comment,
            ]);

            foreach ($request->items as $item) {
                JournalEntryDetail::create([
                    'journal_entry_id'   => $entry->id,
                    'kode_akun'          => $item['kode_akun'],
                    'departemen_akun_id' => $item['departemen_akun_id'] ?? null,
                    'debits'             => $item['debits'] ?? 0,
                    'credits'            => $item['credits'] ?? 0,
                    'comment'            => $item['comment'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('journal_entry.index')->with('success', 'Journal entry berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan journal entry: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan journal entry: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $journal = JournalEntry::with(['details.chartOfAccount', 'details.departemenAkun'])->findOrFail($id);
        return view('journal_entry.show', compact('journal'));
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Temukan journal entry
            $entry = JournalEntry::findOrFail($id);

            // Hapus detail terlebih dahulu
            JournalEntryDetail::where('journal_entry_id', $entry->id)->delete();

            // Hapus journal entry
            $entry->delete();

            DB::commit();

            return redirect()->route('journal_entry.index')
                ->with('success', 'Journal entry berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Gagal menghapus journal entry: ' . $e->getMessage());

            return redirect()->route('journal_entry.index')
                ->with('error', 'Gagal menghapus journal entry: ' . $e->getMessage());
        }
    }
    public function showFilterForm()
    {
        return view('journal_entry.filter_journal_entry');
    }

    public function journalEntryFilter(Request $request)
    {
        $query = JournalEntry::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $entries = $query->get();

        return view('journal_entry.filter_journal_entry', [
            'entries' => $entries,
            'not_found' => $entries->isEmpty() ? 'Data tidak ditemukan untuk filter yang dipilih.' : null,
        ]);
    }

    public function journalEntryShow()
    {
        return view('journal_entry.view_journal_entry');
    }

    public function journalEntryView(Request $request)
    {
        $query = JournalEntry::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $entries = $query->get();

        return view('journal_entry.view_journal_entry', [
            'entries' => $entries,
            'not_found' => $entries->isEmpty() ? 'Data tidak ditemukan untuk filter yang dipilih.' : null,
        ]);
    }
    public function edit($id)
    {
        $journalEntry = JournalEntry::with('details.chartOfAccount')->findOrFail($id);

        return view('journal_entry.edit', compact('journalEntry'));
    }
    public function update(Request $request, $id)
    {
        // Bersihkan dan filter input
        $cleanedItems = collect($request->items)->filter(function ($item) {
            return isset($item['kode_akun']) && $item['kode_akun'] !== '';
        })->map(function ($item) {
            $item['debits'] = isset($item['debits']) && $item['debits'] !== '' ? str_replace(['.', ','], '', $item['debits']) : null;
            $item['credits'] = isset($item['credits']) && $item['credits'] !== '' ? str_replace(['.', ','], '', $item['credits']) : null;
            return $item;
        })->values()->all();

        // Gabungkan kembali ke dalam request
        $request->merge(['items' => $cleanedItems]);

        // Validasi
        $request->validate([
            'source' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'comment' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kode_akun' => 'required|string',
            'items.*.departemen_akun_id' => 'nullable|exists:departemen_akuns,id',
            'items.*.debits' => 'nullable|numeric',
            'items.*.credits' => 'nullable|numeric',
            'items.*.comment' => 'nullable|string',
        ]);
        // Simpan ke database
        DB::beginTransaction();
        try {
            $entry = JournalEntry::findOrFail($id);

            // Update header
            $entry->update([
                'source' => $request->source,
                'tanggal' => $request->tanggal,
                'comment' => $request->comment,
            ]);

            // Hapus detail lama
            $entry->details()->delete(); // pastikan relasi `details()` ada di model

            // Simpan item baru
            foreach ($request->items as $item) {
                $entry->details()->create([
                    'departemen_akun_id' => $item['departemen_akun_id'] ?? null,
                    'kode_akun'          => $item['kode_akun'],
                    'debits'             => $item['debits'] ?? 0,
                    'credits'            => $item['credits'] ?? 0,
                    'comment'            => $item['comment'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('journal_entry.index')->with('success', 'Journal entry berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal update journal entry: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal update journal entry: ' . $e->getMessage());
        }
    }
}

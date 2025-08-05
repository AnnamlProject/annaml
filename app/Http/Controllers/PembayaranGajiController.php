<?php

namespace App\Http\Controllers;

use App\Employee;
use App\komposisi_gaji;
use App\komposisi_gaji_detail;
use App\KomposisiGaji;
use App\KomposisiGajiDetail;
use App\pembayaran_gaji;
use App\pembayaran_gaji_detail;
use App\PembayaranGaji;
use App\PembayaranGajiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranGajiController extends Controller
{
    //
    public function index()
    {
        $data = PembayaranGaji::latest()->paginate(10);
        return view('pembayaran_gaji.index', compact('data'));
    }
    public function create()
    {
        $karyawan = Employee::all();
        return view('pembayaran_gaji.create', compact('karyawan'));
    }
    public function getKomposisiGajiByKaryawan($id)
    {
        $komposisi = KomposisiGaji::where('kode_karyawan', $id)->first();

        if (!$komposisi) {
            return response()->json([]);
        }

        $details = KomposisiGajiDetail::with('komponen')
            ->where('komposisi_gaji_id', $komposisi->id)
            ->orderBy('urut') // opsional
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->komponen->id,
                    'nama_komponen' => $item->komponen->nama_komponen,
                    'tipe' => $item->komponen->tipe ?? '-',
                    'periode_perhitungan' => $item->komponen->periode_perhitungan ?? '-',
                    'nilai' => $item->nilai,
                    'jumlah_hari' => $item->jumlah_hari ?? 0,
                    'potongan' => $item->potongan,
                ];
            });

        return response()->json($details);
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_karyawan' => 'required|exists:employees,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'tanggal_pembayaran' => 'required|date',
            'komponen' => 'required|array',
            'komponen.*.kode_komponen' => 'required|exists:komponen_penghasilans,id',
            'komponen.*.nilai' => 'nullable|numeric',
            'komponen.*.jumlah_hari' => 'nullable|numeric',
            'komponen.*.potongan' => 'nullable|numeric',
            'komponen.*.total' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // Simpan pembayaran_gajis
            $pembayaran = PembayaranGaji::create([
                'kode_karyawan' => $request->kode_karyawan,
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
            ]);

            // Simpan detailnya
            foreach ($request->komponen as $index => $komponen) {
                PembayaranGajiDetail::create([
                    'kode_pembayaran_id' => $pembayaran->id,
                    'kode_komponen' => $komponen['kode_komponen'],
                    'nilai' => $komponen['nilai'] ?? 0,
                    'jumlah_hari' => $komponen['jumlah_hari'] ?? 0,
                    'potongan' => $komponen['potongan'] ?? 0,
                    'urut' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('pembayaran_gaji.index')
                ->with('success', 'Pembayaran gaji berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage())->withInput();
        }
    }
    public function show($id)
    {
        $pembayaran = PembayaranGaji::with('employee')->findOrFail($id);
        $details = PembayaranGajiDetail::where('kode_pembayaran_id', $id)->with('komponen')->get();

        return view('pembayaran_gaji.show', compact('pembayaran', 'details'));
    }
    public function edit($id)
    {
        // Ambil data pembayaran gaji berdasarkan ID
        $pembayaran = PembayaranGaji::findOrFail($id);

        // Ambil semua karyawan untuk dropdown
        $karyawan = Employee::all();

        // Ambil detail komponen penghasilan yang terkait
        $details = PembayaranGajiDetail::where('kode_pembayaran_id', $id)
            ->with('komponen') // pastikan ada relasi komponen di model
            ->get();

        return view(
            'pembayaran_gaji.edit',
            compact('pembayaran', 'karyawan', 'details')
        );
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date',
            'tanggal_pembayaran' => 'required|date',
            'komponen.*.nilai' => 'nullable|numeric',
            'komponen.*.jumlah_hari' => 'nullable|numeric',
            'komponen.*.potongan' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // Update data pembayaran utama
            $pembayaran = PembayaranGaji::findOrFail($id);
            $pembayaran->update([
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
            ]);

            // Update detail komponen satu per satu
            if ($request->has('komponen')) {
                foreach ($request->komponen as $komponen) {
                    if (isset($komponen['id'])) {
                        $detail = PembayaranGajiDetail::find($komponen['id']);

                        if ($detail) {
                            $detail->update([
                                'kode_komponen' => $komponen['kode_komponen'],
                                'nilai' => $komponen['nilai'] ?? 0,
                                'jumlah_hari' => $komponen['jumlah_hari'] ?? 0,
                                'potongan' => $komponen['potongan'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('pembayaran_gaji.index')->with('success', 'Data pembayaran gaji berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $pembayaran = \App\pembayaranGaji::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran_gaji.index')->with('success', 'Pembayaran gaji berhasil dihapus.');
    }
}

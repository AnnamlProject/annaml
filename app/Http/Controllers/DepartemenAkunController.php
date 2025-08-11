<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use Illuminate\Http\Request;

class DepartemenAkunController extends Controller
{
    //
    public function search(Request $request)
    {
        $items = chartOfAccount::with('departemenAkun.departemen')
            ->where(function ($query) use ($request) {
                $query->where('kode_akun', 'like', '%' . $request->q . '%')
                    ->orWhere('nama_akun', 'like', '%' . $request->q . '%');
            })
            // Filter agar tidak menampilkan akun tipe Header/Sub/Tipe Akun
            ->whereNotIn('level_akun', ['HEADER', 'GROUP ACCOUNT', 'Level Akun'])
            ->get();

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'departemen_id' => optional($item->departemenAkun->first())->departemen_id,
                'nama_departemen' => optional(optional($item->departemenAkun->first())->departemen)->deskripsi ?? '-',
                'daftar_departemen' => $item->departemenAkun->map(function ($d) {
                    return [
                        'id' => $d->departemen_id,
                        'deskripsi' => optional($d->departemen)->deskripsi ?? '-'
                    ];
                })
            ];
        }));
    }
}

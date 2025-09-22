<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BukuBesarExport implements FromView
{
    protected $rows, $saldoAwalPerAkun, $groupedByAccount, $totalByType, $start_date, $end_date;

    public function __construct($rows, $saldoAwalPerAkun, $groupedByAccount, $totalByType, $start_date, $end_date)
    {
        $this->rows             = $rows;
        $this->saldoAwalPerAkun = $saldoAwalPerAkun;
        $this->groupedByAccount = $groupedByAccount;
        $this->totalByType      = $totalByType;
        $this->start_date       = $start_date;
        $this->end_date         = $end_date;
    }

    public function view(): View
    {
        return view('buku_besar.export_excel', [
            'rows'             => $this->rows,
            'saldoAwalPerAkun' => $this->saldoAwalPerAkun,
            'groupedByAccount' => $this->groupedByAccount,
            'totalByType'      => $this->totalByType,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
        ]);
    }
}

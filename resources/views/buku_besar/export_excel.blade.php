<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold;">
                Laporan Buku Besar
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">
                Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
            </th>
        </tr>
        <tr>
            <th>Kode Akun</th>
            <th>Nama Akun</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedByAccount as $namaAkun => $akunRows)
            <tr>
                <td colspan="6" style="font-weight: bold; background: #f2f2f2;">
                    {{ $namaAkun }}
                </td>
            </tr>

            {{-- Saldo awal jika ada --}}
            @php
                $kodeAkun = $akunRows->first()->kode_akun ?? null;
                $saldoAwal = $startingBalances[$kodeAkun] ?? 0;
            @endphp
            <tr>
                <td>{{ $kodeAkun }}</td>
                <td>Saldo Awal</td>
                <td>{{ $start_date }}</td>
                <td>-</td>
                <td>{{ $saldoAwal > 0 ? number_format($saldoAwal, 2, ',', '.') : '' }}</td>
                <td>{{ $saldoAwal < 0 ? number_format(abs($saldoAwal), 2, ',', '.') : '' }}</td>
            </tr>

            {{-- Rincian transaksi --}}
            @foreach ($akunRows as $row)
                <tr>
                    <td>{{ $row->kode_akun }}</td>
                    <td>{{ $row->chartOfAccount->nama_akun ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $row->comment ?? '-' }}</td>
                    <td>{{ $row->debits > 0 ? number_format($row->debits, 2, ',', '.') : '' }}</td>
                    <td>{{ $row->credits > 0 ? number_format($row->credits, 2, ',', '.') : '' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

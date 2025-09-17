<table>
    <tr>
        <th colspan="3" style="font-size:16px; font-weight:bold;">LAPORAN NERACA</th>
    </tr>
    <tr>
        <td colspan="3">Per {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}</td>
    </tr>
    <tr></tr>

    @foreach (['Aset', 'Kewajiban', 'Ekuitas'] as $tipe)
        @if (!empty($neraca[$tipe]))
            <tr>
                <th colspan="3" style="background:#f0f0f0;">{{ strtoupper($tipe) }}</th>
            </tr>
            @php $total = 0; @endphp
            @foreach ($neraca[$tipe] as $akun)
                <tr>
                    <td>{{ $akun['kode_akun'] }}</td>
                    <td>{{ $akun['nama_akun'] }}</td>
                    <td align="right">{{ number_format($akun['saldo'], 2, ',', '.') }}</td>
                </tr>
                @php $total += $akun['saldo']; @endphp
            @endforeach
            <tr>
                <td colspan="2"><strong>TOTAL {{ strtoupper($tipe) }}</strong></td>
                <td align="right"><strong>{{ number_format($total, 2, ',', '.') }}</strong></td>
            </tr>
            <tr></tr>
        @endif
    @endforeach

    {{-- <tr>
        <td colspan="2"><strong>Total Aset</strong></td>
        <td align="right"><strong>{{ number_format($grandTotalAset, 2, ',', '.') }}</strong></td>
    </tr> --}}
    <tr>
        <td colspan="2"><strong>TOTAL KEWAJIBAN DAN EKUITAS</strong></td>
        <td align="right"><strong>{{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 2, ',', '.') }}</strong>
        </td>
    </tr>
</table>

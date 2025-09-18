<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px;">
                LAPORAN LABA RUGI
            </th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                Periode {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') }}
                s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y') }}
            </th>
        </tr>
    </thead>
    <tbody>
        {{-- =======================
             Bagian PENDAPATAN
        ======================== --}}
        @if (!empty($groupsPendapatan))
            <tr>
                <td colspan="2" style="font-weight:bold;">PENDAPATAN</td>
            </tr>
            @foreach ($groupsPendapatan as $group)
                <tr>
                    <td colspan="2" style="font-weight:bold;">{{ $group['group'] }}</td>
                </tr>
                @foreach ($group['akun'] as $akun)
                    <tr>
                        <td>{{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}</td>
                        <td style="text-align: right;">{{ number_format($akun['saldo'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight:bold;">
                    <td>SUBTOTAL {{ $group['group'] }}</td>
                    <td style="text-align: right;">{{ number_format($group['saldo_group'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="font-weight:bold; border-top: 2px solid #000;">
                <td>TOTAL PENDAPATAN</td>
                <td style="text-align: right;">{{ number_format($totalPendapatan, 2, ',', '.') }}</td>
            </tr>
        @endif

        {{-- ===================
             Bagian BEBAN
        ==================== --}}
        @if (!empty($groupsBeban))
            <tr>
                <td colspan="2" style="font-weight:bold; padding-top:10px;">BEBAN</td>
            </tr>
            @foreach ($groupsBeban as $group)
                <tr>
                    <td colspan="2" style="font-weight:bold;">{{ $group['group'] }}</td>
                </tr>
                @foreach ($group['akun'] as $akun)
                    <tr>
                        <td>{{ $akun['kode_akun'] }} - {{ $akun['nama_akun'] }}</td>
                        <td style="text-align: right;">{{ number_format($akun['saldo'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight:bold;">
                    <td>SUBTOTAL {{ $group['group'] }}</td>
                    <td style="text-align: right;">{{ number_format($group['saldo_group'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="font-weight:bold; border-top: 2px solid #000;">
                <td>TOTAL BEBAN</td>
                <td style="text-align: right;">{{ number_format($totalBeban, 2, ',', '.') }}</td>
            </tr>
        @endif

        {{-- ==========================
             RINGKASAN AKHIR
        =========================== --}}
        <tr>
            <td colspan="2" style="padding-top:10px;"></td>
        </tr>
        <tr style="font-weight:bold;">
            <td>LABA SEBELUM PAJAK</td>
            <td style="text-align: right;">{{ number_format($labaSebelumPajak, 2, ',', '.') }}</td>
        </tr>
        <tr style="font-weight:bold;">
            <td>BEBAN PAJAK PENGHASILAN</td>
            <td style="text-align: right;">{{ number_format($bebanPajak, 2, ',', '.') }}</td>
        </tr>
        <tr style="font-weight:bold;">
            <td>LABA BERSIH SETELAH PAJAK</td>
            <td style="text-align: right;">{{ number_format($labaSetelahPajak, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

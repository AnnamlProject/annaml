<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h2,
        h3 {
            margin: 0;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 10px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 6px;
        }

        th {
            text-align: left;
            background: #f0f0f0;
        }

        td.text-right {
            text-align: right;
        }

        td.text-left {
            text-align: left;
        }

        .section-title {
            font-weight: bold;
            background: #ddd;
            padding: 5px;
        }

        .total-row {
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .final {
            font-weight: bold;
            font-size: 13px;
            background: #eee;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <h2>LAPORAN LABA RUGI</h2>
    <div class="subtitle">
        Periode: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') }}
        s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y') }}
    </div>

    {{-- =======================
        Bagian Pendapatan
    ======================== --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" class="section-title">PENDAPATAN</th>
            </tr>
        </thead>
        <tbody>
            @php $pendapatan = array_filter($incomeData, fn($row) => strtolower($row['tipe_akun']) === 'pendapatan'); @endphp
            @forelse($pendapatan as $row)
                <tr>
                    <td class="text-left">{{ $row['kode_akun'] }} - {{ $row['nama_akun'] }}</td>
                    <td class="text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Tidak ada data pendapatan</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td>TOTAL PENDAPATAN</td>
                <td class="text-right">{{ number_format($totalPendapatan, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- =======================
        Bagian Beban
    ======================== --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" class="section-title">BEBAN</th>
            </tr>
        </thead>
        <tbody>
            @php $beban = array_filter($incomeData, fn($row) => strtolower($row['tipe_akun']) === 'beban'); @endphp
            @forelse($beban as $row)
                <tr>
                    <td class="text-left">{{ $row['kode_akun'] }} - {{ $row['nama_akun'] }}</td>
                    <td class="text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Tidak ada data beban</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td>TOTAL BEBAN</td>
                <td class="text-right">{{ number_format($totalBeban, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- =======================
        Laba Bersih
    ======================== --}}
    <table>
        <tbody>
            <tr class="final">
                <td>LABA SEBELUM PAJAK PENGHASILAN</td>
                <td class="text-right">{{ number_format($labaBersih, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Footer halaman --}}
    <div class="footer">
        Dicetak: {{ now()->format('d/m/Y H:i') }} — Halaman <span class="pagenum"></span>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Neraca - {{ $tanggalAkhir }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h3,
        h4 {
            margin: 0;
            padding: 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 4px;
            vertical-align: top;
        }

        .border {
            border: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            background: #f0f0f0;
        }

        .w-50 {
            width: 50%;
        }
    </style>
</head>

<body>
    <h3>{{ config('app.name') }}</h3>
    <h4>Laporan Neraca<br>Per {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}</h4>

    <table class="border">
        <tr>
            {{-- Kolom Aset --}}
            <td class="w-50 border" style="vertical-align: top;">
                <h4 style="text-align:left;">ASET</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    @foreach ($neraca['Aset'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['nama_akun'] }}</td>
                            <td class="text-right">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bold">
                        <td>Total Aset</td>
                        <td class="text-right">{{ number_format($grandTotalAset, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>

            {{-- Kolom Kewajiban + Ekuitas --}}
            <td class="w-50 border" style="vertical-align: top;">
                <h4 style="text-align:left;">KEWAJIBAN</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    @foreach ($neraca['Kewajiban'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['nama_akun'] }}</td>
                            <td class="text-right">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bold">
                        <td>Total Kewajiban</td>
                        <td class="text-right">{{ number_format($grandTotalKewajiban, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <h4 style="text-align:left; margin-top:10px;">EKUITAS</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    @foreach ($neraca['Ekuitas'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['nama_akun'] }}</td>
                            <td class="text-right">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bold">
                        <td>Total Ekuitas</td>
                        <td class="text-right">{{ number_format($grandTotalEkuitas, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>
    <table style="width:100%;">
        <tr>
            <td class="w-50 bold">Total Aset</td>
            <td class="w-50 bold text-right">{{ number_format($grandTotalAset, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="w-50 bold">Total Kewajiban + Ekuitas</td>
            <td class="w-50 bold text-right">
                {{ number_format($grandTotalKewajiban + $grandTotalEkuitas, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="mb-6 mt-4 flex justify-end gap-2">
            <a href="{{ route('income_statement_departement.export', ['start_date' => $start_date, 'end_date' => $end_date, 'format' => 'excel']) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <a href="{{ route('income_statement_departement.export', ['start_date' => $start_date, 'end_date' => $end_date, 'format' => 'pdf']) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
        </div>
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">Departmental Income Statement</h1>
            <p class="text-gray-600">Periode: {{ $start_date }} s/d {{ $end_date }}</p>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="text-center">
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Total</th>
                        @foreach ($departemens as $dept)
                            <th>{{ $dept }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incomeStatement as $row)
                        <tr>
                            <td class="text-center">{{ $row['kode_akun'] }}</td>
                            <td>{{ $row['nama_akun'] }}</td>
                            <td class="text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                            @foreach ($departemens as $dept)
                                <td class="text-right">{{ number_format($row['per_departemen'][$dept] ?? 0, 2, ',', '.') }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

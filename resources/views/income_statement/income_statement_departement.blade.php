@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">Departmental Income Statement</h1>
            <p class="text-gray-600">Periode: {{ $start_date }} s/d {{ $end_date }}</p>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr>
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
                            <td>{{ $row['kode_akun'] }}</td>
                            <td>{{ $row['nama_akun'] }}</td>
                            <td>{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                            @foreach ($departemens as $dept)
                                <td>{{ number_format($row['per_departemen'][$dept] ?? 0, 2, ',', '.') }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

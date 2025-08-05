@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded px-6 py-4">
            <h2 class="text-2xl font-bold mb-4">Trial Balance</h2>

            <form method="GET" action="{{ route('trial_balance.trial_balance_report') }}" class="mb-4">
                <div class="flex items-center space-x-4">
                    <label for="end_date">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ request('end_date') ?? \Carbon\Carbon::now()->toDateString() }}"
                        class="border border-gray-300 rounded px-2 py-1">
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Filter</button>
                </div>
            </form>

            <table class="w-full table-auto border border-collapse mt-4 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">Account</th>
                        <th class="border px-3 py-2 text-right">Debet</th>
                        <th class="border px-3 py-2 text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDebet = 0;
                        $totalKredit = 0;
                    @endphp
                    @forelse ($trialBalances as $account)
                        @php
                            $debet = $account['saldo_debit'];
                            $kredit = $account['saldo_kredit'];
                            $totalDebet += $debet;
                            $totalKredit += $kredit;
                        @endphp
                        <tr>
                            <td class="border px-3 py-1">{{ $account['kode_akun'] }} - {{ $account['nama_akun'] }}</td>
                            <td class="border px-3 py-1 text-right">{{ number_format($debet, 2) }}</td>
                            <td class="border px-3 py-1 text-right">{{ number_format($kredit, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border px-3 py-2 text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-semibold bg-gray-200">
                        <td colspan="1" class="border px-3 py-2 text-right">Total</td>
                        <td class="border px-3 py-2 text-right">{{ number_format($totalDebet, 2) }}</td>
                        <td class="border px-3 py-2 text-right">{{ number_format($totalKredit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

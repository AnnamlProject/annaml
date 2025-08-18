@extends('layouts.app')
@section('content')
    <div class="max-w-full mx-auto py-10 sm:px-6 lg:px-8">

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Detail Sales Deposit</h2>

            {{-- Informasi Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><strong>Deposit Number:</strong> {{ $deposit->deposit_no }}</div>
                <div><strong>Deposit Date:</strong> {{ $deposit->deposit_date }}</div>
                <div><strong>Customer:</strong> {{ $deposit->customer->nama_customers ?? '-' }}</div>
                <div><strong>Payment Method:</strong> {{ $deposit->paymentMethod->nama_jenis ?? '-' }}</div>
                <div><strong>Deposit To Account:</strong> {{ $deposit->account->nama_akun ?? '-' }}</div>
                <div><strong>Deposit Amount:</strong> Rp {{ number_format($deposit->deposit_amount, 2, ',', '.') }}
                </div>
                <div><strong>Deposit Reference:</strong> {{ $deposit->deposit_reference ?? '-' }}</div>
                <div><strong>Comment:</strong> {{ $deposit->comment ?? '-' }}</div>
            </div>

            {{-- Tabel Detail --}}
            <h3 class="text-xl font-semibold mt-10 mb-4 text-gray-800">Detail Penggunaan Deposit</h3>

            <div class="overflow-x-auto border rounded">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-3 py-2 text-left">Invoice Number</th>
                            <th class="px-3 py-2 text-left">Invoice Date</th>
                            <th class="px-3 py-2 text-right">Original Amount</th>
                            <th class="px-3 py-2 text-right">Amount Owing</th>
                            <th class="px-3 py-2 text-right">Discount Available</th>
                            <th class="px-3 py-2 text-right">Discount Taken</th>
                            <th class="px-3 py-2 text-right">Amount Used</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($deposit->details as $detail)
                            <tr>
                                <td class="px-3 py-2">{{ $detail->invoice->invoice_number ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $detail->invoice_date }}</td>
                                <td class="px-3 py-2 text-right">
                                    {{ number_format($detail->original_amount, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">
                                    {{ number_format($detail->amount_owing, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">
                                    {{ number_format($detail->discount_available, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">
                                    {{ number_format($detail->discount_taken, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-semibold">
                                    {{ number_format($detail->used_amount, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-3 text-center text-gray-500">Tidak ada penggunaan
                                    deposit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol kembali --}}
            <div class="mt-6">
                <a href="{{ route('sales_deposits.index') }}"
                    class="inline-block px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('content')
    <div class="py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                @if (session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('receipts.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label>No. Receipt</label>
                            <input type="text" name="receipt_number"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label>Tanggal</label>
                            <input type="date" name="date"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label>Customer</label>
                            <select name="customer_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->nama_customers ?? $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Deposit To</label>
                            <select name="deposit_to_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label>Comment</label>
                            <textarea name="comment"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                rows="2"></textarea>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-2">💳 Invoice yang Dibayar</h3>
                    <div class="overflow-x-auto mb-4">
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tgl Invoice</th>
                                    <th>Original</th>
                                    <th>Owing</th>
                                    <th>Disc Avail</th>
                                    <th>Disc Taken</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $i => $inv)
                                    <tr class="border-t">
                                        <td>
                                            <input type="hidden" name="details[{{ $i }}][sales_invoice_id]"
                                                value="{{ $inv->id }}">
                                            {{ $inv->invoice_number }}
                                        </td>
                                        <td>
                                            <input type="date" name="details[{{ $i }}][invoice_date]"
                                                class="border rounded w-full" value="{{ $inv->invoice_date }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="details[{{ $i }}][original_amount]"
                                                class="border rounded w-full" value="{{ $inv->total_amount ?? 0 }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="details[{{ $i }}][amount_owing]"
                                                class="border rounded w-full" value="{{ $inv->sisa_tagihan ?? 0 }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="details[{{ $i }}][discount_available]"
                                                class="border rounded w-full" value="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="details[{{ $i }}][discount_taken]"
                                                class="border rounded w-full" value="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="details[{{ $i }}][amount_received]"
                                                class="border rounded w-full" value="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            💾 Simpan Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

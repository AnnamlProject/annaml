@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-xl">
                <form method="POST" action="{{ route('sales_deposits.update', $sales_deposits->id) }}">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-100 p-4 text-sm text-red-700 rounded mb-4">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $sales_deposits->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Deposit To</label>
                            <select name="account_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($account as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $sales_deposits->account_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Deposit No</label>
                            <input type="text" name="deposit_no"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $sales_deposits->deposit_no }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Customer</label>
                            <select name="customers_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($customer as $cust)
                                    <option value="{{ $cust->id }}"
                                        {{ $sales_deposits->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Deposit Date</label>
                            <input type="date" name="deposit_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $sales_deposits->deposit_date }}" required>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Deposit Amount</label>
                            <input type="text" name="deposit_amount"
                                class="w-full number-format border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('deposit_amount', number_format($sales_deposits->deposit_amount, 2, ',', '.')) }}"
                                required>
                        </div>

                    </div>

                    {{-- TABEL ITEM --}}
                    <div class="mt-8">
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="w-full text-sm text-gray-700">
                                <thead class="bg-gray-100 text-left">
                                    <tr>
                                        <th class="px-3 py-2">Invoice Date</th>
                                        <th class="px-3 py-2">Invoice / Deposit</th>
                                        <th class="px-3 py-2 text-right">Original Amount</th>
                                        <th class="px-3 py-2 text-right">Amount Owing</th>
                                        <th class="px-3 py-2 text-right">Discount Available</th>
                                        <th class="px-3 py-2 text-right">Discount Taken</th>
                                        <th class="px-3 py-2 text-right">Amount Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales_deposits->details as $index => $item)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="date" name="items[{{ $index }}][invoice_date]"
                                                    value="{{ $item->invoice_date }}" class="w-full border px-2 py-1" />
                                            </td>
                                            <td class="px-3 py-2">
                                                <select name="items[{{ $index }}][sales_invoice_id]"
                                                    class="w-full border px-2 py-1">
                                                    <option value="">-- Pilih Invoice --</option>
                                                    @foreach ($sales_invoices as $inv)
                                                        <option value="{{ $inv->id }}"
                                                            {{ $inv->id == $item->sales_invoice_id ? 'selected' : '' }}>
                                                            {{ $inv->invoice_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[{{ $index }}][original_amount]"
                                                    class="w-full border px-2 py-1 text-right" step="0.01"
                                                    value="{{ $item->original_amount }}"></td>
                                            <td><input type="number" name="items[{{ $index }}][amount_owing]"
                                                    class="w-full border px-2 py-1 text-right" step="0.01"
                                                    value="{{ $item->amount_owing }}"></td>
                                            <td><input type="number" name="items[{{ $index }}][discount_available]"
                                                    class="w-full border px-2 py-1 text-right" step="0.01"
                                                    value="{{ $item->discount_available }}"></td>
                                            <td><input type="number" name="items[{{ $index }}][discount_taken]"
                                                    class="w-full border px-2 py-1 text-right" step="0.01"
                                                    value="{{ $item->discount_taken }}"></td>
                                            <td><input type="number" name="items[{{ $index }}][amount_received]"
                                                    class="w-full border px-2 py-1 text-right" step="0.01"
                                                    value="{{ $item->used_amount }}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Info Tambahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Deposit Reference</label>
                            <input type="text" name="deposit_reference"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $sales_deposits->deposit_reference }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Comment</label>
                            <textarea name="comment" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $sales_deposits->comment }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            💾 Update
                        </button>
                        <a href="{{ route('sales_deposits.index') }}"
                            class="px-6 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.number-format');

        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });
        });
    });
</script>

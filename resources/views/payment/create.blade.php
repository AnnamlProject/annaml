@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($payment) ? route('payment.update', $payment->id) : route('payment.store') }}">
                    @csrf
                    @if (isset($payment))
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama payment_asset -->
                        <div class="mb-4">
                            <label for="nama_metode" class="block text-gray-700 font-medium mb-1">Payment Method
                            </label>
                            <select name="jenis_pembayaran_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Payment Method--</option>
                                @foreach ($jenis_pembayaran as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('jenis_pembayaran_id', $payment->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- from account --}}
                        <div class="mb-4 mt-4">
                            <label for="from_account" class="block text-gray-700 font-medium mb-1">From Account
                            </label>
                            <input type="text" id="name" name="from_account" required
                                value="{{ old('from_account', $payment->from_account ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('from_account')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- source --}}
                        <div class="mb-4 mt-4">
                            <label for="source" class="block text-gray-700 font-medium mb-1">Source
                            </label>
                            <input type="text" id="name" name="source" required
                                value="{{ old('source', $payment->source ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('source')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="vendor" class="block text-gray-700 font-medium mb-1">Vendor
                            </label>
                            <select name="vendor_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih --</option>
                                @foreach ($vendor as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('vendor_id', $payment->vendor_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_vendors }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="payment_date" class="block text-gray-700 font-medium mb-1">Payment Date
                            </label>
                            <input type="date" id="name" name="payment_date" required
                                value="{{ old('payment_date', $payment->payment_date ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('payment_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Type" class="block text-gray-700 font-medium mb-1">Type
                            </label>
                            <select name="type" id="type" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="Invoice"
                                    {{ old('type', $data->type ?? '') == 'Invoice' ? 'selected' : '' }}>
                                    Invoice</option>
                                <option value="Other" {{ old('type', $data->type ?? '') == 'Other' ? 'selected' : '' }}>
                                    Other</option>

                            </select>
                            @error('type')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold mb-4">Order Items</h3>

                        <!-- Tabel untuk Type: Invoice -->
                        <div id="invoice-table" class="hidden">
                            <h3 class="text-lg font-semibold mb-4">Invoice Payment Table</h3>
                            <table class="w-full border">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-2 py-1">Due Date</th>
                                        <th class="border px-2 py-1">Invoice/Prepayment</th>
                                        <th class="border px-2 py-1">Original Amount</th>
                                        <th class="border px-2 py-1">Amount Owing</th>
                                        <th class="border px-2 py-1">Discount Available</th>
                                        <th class="border px-2 py-1">Discount Taken</th>
                                        <th class="border px-2 py-1">Payment Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-body">
                                    @for ($i = 0; $i < 10; $i++)
                                        <tr>
                                            <td class="border px-2 py-1"><input type="date"
                                                    name="invoice_details[{{ $i }}][due_date]"
                                                    class="w-full border"></td>
                                            <td class="border px-2 py-1"><input type="text"
                                                    name="invoice_details[{{ $i }}][invoice_number]"
                                                    class="w-full border"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="invoice_details[{{ $i }}][original_amount]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="invoice_details[{{ $i }}][amount_owing]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="invoice_details[{{ $i }}][discount_available]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="invoice_details[{{ $i }}][discount_taken]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="invoice_details[{{ $i }}][payment_amount]"
                                                    class="w-full border text-right"></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <button type="button" id="add-invoice-row"
                                class="mt-2 bg-green-600 text-white px-4 py-1 rounded">+ Add Row</button>
                        </div>

                        <!-- Tabel untuk Type: Other -->
                        <div id="other-table" class="hidden mt-6">
                            <h3 class="text-lg font-semibold mb-4">Other Payment Table</h3>
                            <table class="w-full border">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-2 py-1">Account</th>
                                        <th class="border px-2 py-1">Description</th>
                                        <th class="border px-2 py-1">Amount</th>
                                        <th class="border px-2 py-1">Tax</th>
                                        <th class="border px-2 py-1">Allocation</th>
                                    </tr>
                                </thead>
                                <tbody id="other-body">
                                    @for ($i = 0; $i < 10; $i++)
                                        <tr>
                                            <td class="border px-2 py-1">
                                                <select name="other_details[{{ $i }}][account]"
                                                    class="w-full border">
                                                    <option value="">-- Select Account --</option>
                                                    @foreach ($account as $a)
                                                        <option value="{{ $a->id }}">{{ $a->kode_akun }} -
                                                            {{ $a->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border px-2 py-1"><input type="text"
                                                    name="other_details[{{ $i }}][description]"
                                                    class="w-full border"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="other_details[{{ $i }}][amount]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="number" step="0.01"
                                                    name="other_details[{{ $i }}][tax]"
                                                    class="w-full border text-right"></td>
                                            <td class="border px-2 py-1"><input type="text"
                                                    name="other_details[{{ $i }}][allocation]"
                                                    class="w-full border"></td>
                                        </tr>
                                    @endfor
                                </tbody>

                            </table>
                            <button type="button" id="add-other-row"
                                class="mt-2 bg-green-600 text-white px-4 py-1 rounded">+ Add Row</button>
                        </div>

                        <!-- Comment -->
                        <div class="mb-4 md:col-span-2 mt-6">
                            <label for="comment" class="block text-gray-700 font-medium mb-1">Comment</label>
                            <textarea id="comment" name="comment" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comment', $payment->comment ?? '') }}</textarea>
                            @error('comment')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($payment) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('payment.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JQUERY DULU -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const invoiceTable = document.getElementById('invoice-table');
            const otherTable = document.getElementById('other-table');
            const invoiceBody = document.getElementById('invoice-body');
            const otherBody = document.getElementById('other-body');
            const addInvoiceBtn = document.getElementById('add-invoice-row');
            const addOtherBtn = document.getElementById('add-other-row');

            let invoiceIndex = 10;
            let otherIndex = 10;

            function toggleTable(value) {
                if (value === 'Invoice') {
                    invoiceTable.classList.remove('hidden');
                    otherTable.classList.add('hidden');
                } else if (value === 'Other') {
                    invoiceTable.classList.add('hidden');
                    otherTable.classList.remove('hidden');
                } else {
                    invoiceTable.classList.add('hidden');
                    otherTable.classList.add('hidden');
                }
            }

            typeSelect.addEventListener('change', function() {
                toggleTable(this.value);
            });

            toggleTable(typeSelect.value); // initial load

            addInvoiceBtn?.addEventListener('click', function() {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="border px-2 py-1"><input type="date" name="invoice_details[${invoiceIndex}][due_date]" class="w-full border"></td>
                <td class="border px-2 py-1"><input type="text" name="invoice_details[${invoiceIndex}][invoice_number]" class="w-full border"></td>
                <td class="border px-2 py-1"><input type="number" step="0.01" name="invoice_details[${invoiceIndex}][original_amount]" class="w-full border text-right"></td>
                <td class="border px-2 py-1"><input type="number" step="0.01" name="invoice_details[${invoiceIndex}][amount_owing]" class="w-full border text-right"></td>
                <td class="border px-2 py-1"><input type="number" step="0.01" name="invoice_details[${invoiceIndex}][discount_available]" class="w-full border text-right"></td>
                <td class="border px-2 py-1"><input type="number" step="0.01" name="invoice_details[${invoiceIndex}][discount_taken]" class="w-full border text-right"></td>
                <td class="border px-2 py-1"><input type="number" step="0.01" name="invoice_details[${invoiceIndex}][payment_amount]" class="w-full border text-right"></td>
            `;
                invoiceBody.appendChild(row);
                invoiceIndex++;
            });

            const accountOptions = `@foreach ($account as $a)
    <option value="{{ $a->id }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
@endforeach`;

            addOtherBtn?.addEventListener('click', function() {
                const row = document.createElement('tr');
                row.innerHTML = `
        <td class="border px-2 py-1">
            <select name="other_details[${otherIndex}][account]" class="w-full border">
                <option value="">-- Select Account --</option>
                ${accountOptions}
            </select>
        </td>
        <td class="border px-2 py-1"><input type="text" name="other_details[${otherIndex}][description]" class="w-full border"></td>
        <td class="border px-2 py-1"><input type="number" step="0.01" name="other_details[${otherIndex}][amount]" class="w-full border text-right"></td>
        <td class="border px-2 py-1"><input type="number" step="0.01" name="other_details[${otherIndex}][tax]" class="w-full border text-right"></td>
        <td class="border px-2 py-1"><input type="text" name="other_details[${otherIndex}][allocation]" class="w-full border"></td>
    `;
                otherBody.appendChild(row);
                otherIndex++;
            });
        });
    </script>
@endsection

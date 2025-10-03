@extends('layouts.app')
@section('content')
    <div class="py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($sales_deposits) ? route('sales_deposits.update', $sales_deposits->id) : route('sales_deposits.store') }}">
                    @csrf
                    @if (isset($sales_deposits))
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

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        {{-- payment method --}}
                        <div class="mb-4">
                            <label for="nama_metode" class="block text-gray-700 font-medium mb-1">Payment Method
                            </label>
                            <select name="jenis_pembayaran_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Payment Method--</option>
                                @foreach ($jenis_pembayaran as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('jenis_pembayaran_id', $sales_deposits->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nama_metode" class="block text-gray-700 font-medium mb-1">Deposit To
                            </label>
                            <select name="account_id" id="account_header_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih--</option>
                                @foreach ($account as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('account_id', $sales_deposits->account_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- customers --}}
                        <div class="mb-4">
                            <label for="customers" class="block text-gray-700 font-medium mb-1">Customers
                            </label>
                            <select name="customers_id" id="customers_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Customers--</option>
                                @foreach ($customer as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('customer_id', $sales_deposits->customer_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- deposit number --}}
                        <div class="mb-4">
                            <label for="deposit_no" class="block text-gray-700 font-medium mb-1">Deposits
                                Number</label>
                            <input type="text" id="deposit_no" name="deposit_no"
                                value="{{ old('deposit_no', $sales_deposits->deposit_no ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            {{-- <label class="inline-flex items-center mt-2">
                                <input type="checkbox" id="auto_generate" name="auto_generate" value="1"
                                    class="form-checkbox text-blue-600" onchange="toggleAutoGenerate()">
                                <span class="ml-2 text-sm text-gray-700">Generate Invoice Number secara otomatis</span>
                            </label> --}}

                            @error('deposit_no')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- deposit date --}}
                        <div class="mb-4">
                            <label for="deposit_date" class="block text-gray-700 font-medium mb-1">Date
                            </label>
                            <input type="date" id="name" name="deposit_date" required
                                value="{{ old('deposit_date', $sales_deposits->deposit_date ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('deposit_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div class="mt-10">

                        <!-- Scrollable Table -->
                        <div class="overflow-x-auto border rounded-lg shadow-sm mt-6">
                            <table class="w-full text-sm text-gray-700" id="item-table">
                                <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Invoice Date</th>
                                        <th class="px-3 py-2 text-left">Invoice / Deposit</th>
                                        <th class="px-3 py-2 text-right">Original Amount</th>
                                        <th class="px-3 py-2 text-right">Amount Owing</th>
                                        <th class="px-3 py-2 text-right">Discount Available</th>
                                        <th class="px-3 py-2 text-right">Discount Taken</th>
                                        <th class="px-3 py-2 text-right">Amount Received</th>
                                    </tr>
                                </thead>

                                <tbody id="invoice-rows" class="divide-y">
                                    <tr>
                                        <td class="px-3 py-2">
                                            <input type="date" name="items[0][invoice_date]"
                                                class="w-full border rounded px-2 py-1" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <select name="items[0][sales_invoice_id]"
                                                class="w-full border rounded px-2 py-1">
                                                <option value="">-- Pilih Invoice --</option>
                                                @foreach ($sales_invoices as $inv)
                                                    <option value="{{ $inv->id }}">{{ $inv->invoice_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="items[0][original_amount]"
                                                class="w-full border rounded px-2 py-1 text-right" step="0.01" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="items[0][amount_owing]"
                                                class="w-full border rounded px-2 py-1 text-right" step="0.01" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="items[0][discount_available]"
                                                class="w-full border rounded px-2 py-1 text-right" step="0.01" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="items[0][discount_taken]"
                                                class="w-full border rounded px-2 py-1 text-right" step="0.01" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="items[0][amount_received]"
                                                class="w-full border rounded px-2 py-1 text-right" step="0.01" />
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot class="bg-gray-50 border-t">
                                    <tr>
                                        <td colspan="6" class="px-3 py-2  text-right font-semibold text-sm">Deposit
                                            Amount:</td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="deposit_amount" step="0.01"
                                                class="w-full border number-format rounded px-2 py-1 text-right font-semibold" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="px-3 py-2 text-right font-semibold text-sm">Total
                                            Amount Received:</td>
                                        <td class="px-3 py-2">
                                            <input type="number" name="total_amount_received" step="0.01"
                                                class="w-full border rounded px-2 py-1 text-right font-semibold bg-gray-100"
                                                readonly />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mb-4 mt-4">
                            <label for="deposit_reference" class="block text-gray-700 font-medium mb-1">Deposit
                                Reference No
                            </label>
                            <input type="text" id="name" name="deposit_reference"
                                value="{{ old('deposit_reference', $sales_deposits->deposit_reference ?? '') }}"
                                class="w-1/3 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('deposit_reference')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 md:col-span-2 ">
                            <label for="comment" class="block text-gray-700 font-medium mb-1">Comment
                            </label>
                            <textarea id="comment" name="comment" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comment', $sales_deposits->comment ?? '') }}</textarea>
                            @error('comment')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($sales_deposits) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('sales_deposits.index') }}"
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
        $(document).ready(function() {
            $('#account_header_id').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#customers_id').select2({
                placeholder: "-- Customers --",
                ajax: {
                    url: '{{ route('customers.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        }; // query keyword
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(customer) {
                                return {
                                    id: customer.id,
                                    text: customer.nama_customers
                                };
                            })
                        };
                    },
                    cache: true
                },
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        function toggleAutoGenerate() {
            const checkbox = document.getElementById('auto_generate');
            const invoiceInput = document.getElementById('invoice_number');

            if (checkbox.checked) {
                invoiceInput.readOnly = true;
                invoiceInput.value = 'Auto-generated'; // opsional: tampilkan teks dummy
            } else {
                invoiceInput.readOnly = false;
                invoiceInput.value = '';
            }
        }

        // Jalankan saat halaman dimuat
        window.onload = function() {
            toggleAutoGenerate();

            // Tambahkan value agar checkbox bisa dikenali server
            const autoCheckbox = document.getElementById('auto_generate');
            autoCheckbox.name = "auto_generate";
            autoCheckbox.value = 1;
        };
    </script>
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
@endsection

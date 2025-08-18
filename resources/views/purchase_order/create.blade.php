@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($purchase_order) ? route('purchase_order.update', $purchase_order->id) : route('purchase_order.store') }}">
                    @csrf
                    @if (isset($purchase_order))
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

                        <!-- Nama purchase_order_asset -->

                        <div class="mb-4">
                            <label for="customers" class="block text-gray-700 font-medium mb-1">Customers
                            </label>
                            <select name="customer_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Customers--</option>
                                @foreach ($customer as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('customer_id', $purchase_order->customer_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nama_metode" class="block text-gray-700 font-medium mb-1">Payment Method
                            </label>
                            <select name="jenis_pembayaran_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Payment Method--</option>
                                @foreach ($jenis_pembayaran as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('jenis_pembayaran_id', $purchase_order->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- deskripsi purchase_order_asset -->
                        <div class="mb-4 md:col-span-2">
                            <label for="shipping_address" class="block text-gray-700 font-medium mb-1">Shipping
                                Address</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('shipping_address', $purchase_order->shipping_address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="order_number" class="block text-gray-700 font-medium mb-1">Order Number
                            </label>
                            <input type="text" id="name" name="order_number" required
                                value="{{ old('order_number', $purchase_order->order_number ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('order_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="date_order" class="block text-gray-700 font-medium mb-1">Date Order
                            </label>
                            <input type="date" id="name" name="date_order" required
                                value="{{ old('date_order', $purchase_order->date_order ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('date_order')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="shipping_date" class="block text-gray-700 font-medium mb-1">Shipping Date
                            </label>
                            <input type="date" id="name" name="shipping_date" required
                                value="{{ old('shipping_date', $purchase_order->shipping_date ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('shipping_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <!-- Order Items Table -->
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold mb-4">Order Items</h3>

                        <!-- Scrollable Table -->
                        <div class="overflow-x-auto border rounded-lg shadow-sm">
                            <table class="min-w-[1300px] table-auto border-collapse text-sm text-left">
                                <thead class="bg-gray-100 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="border px-4 py-2 w-56">Item</th>
                                        <th class="border px-4 py-2 w-24 text-center">Qty</th>
                                        <th class="border px-4 py-2 w-24 text-center">Order</th>
                                        <th class="border px-4 py-2 w-28 text-center">Back Order</th>
                                        <th class="border px-4 py-2 w-28">Unit</th>
                                        <th class="border px-4 py-2 w-64">Item Desription</th>
                                        <th class="border px-4 py-2 w-32 text-right">Price</th>
                                        <th class="border px-4 py-2 w-32 text-right">Tax</th>
                                        <th class="border px-4 py-2 w-32 text-right">Tax Amount</th>
                                        <th class="border px-4 py-2 w-36 text-right">Amount</th>
                                        <th class="border px-4 py-2 w-40">Account</th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body" class="bg-white">
                                    <!-- Baris dinamis dari JS akan dimasukkan di sini -->
                                </tbody>


                            </table>
                        </div>

                        <!-- Button Tambah Baris -->
                        <div class="mt-4">
                            <button type="button" id="add-row"
                                class="px-5 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition">
                                + Tambah Baris
                            </button>
                        </div>

                        <div class="mb-4 mt-4">
                            <label for="freight" class="block text-gray-700 font-medium mb-1">Freight
                            </label>
                            <input type="number" id="name" name="freight" required
                                value="{{ old('freight', $purchase_order->freight ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('freight')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 mt-4">
                            <label for="early_payment_terms" class="block text-gray-700 font-medium mb-1">Early
                                Payments Terms
                            </label>
                            <input type="text" id="name" name="early_payment_terms" required
                                value="{{ old('early_payment_terms', $purchase_order->early_payment_terms ?? '') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('early_payment_terms')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 md:col-span-2 ">
                            <label for="messages" class="block text-gray-700 font-medium mb-1">Messages
                            </label>
                            <textarea id="messages" name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('messages', $purchase_order->messages ?? '') }}</textarea>
                            @error('messages')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>




                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($purchase_order) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('purchase_order.index') }}"
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
        let rowIndex = 0;

        function generateRow(index) {
            return `
        <tr class="item-row" data-index="${index}">
            <td class="border px-2 py-1">
                <select name="items[${index}][item_id]" class="item-select w-full border rounded" data-index="${index}"></select>
            </td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][quantity]" class="qty-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][order]" class="order-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][back_order]" class="back-${index} w-full border rounded"  /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][unit]" class="unit-${index} w-full border rounded"  /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][description]" class="desc-${index} w-full border rounded"  /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][price]" class="purchase-${index} w-full border rounded"  /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][tax]" class="tax-${index} w-full border rounded" value="0" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][tax_amount]" class="tax_amount-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][amount]" class="amount-${index} w-full border rounded text-right"  /></td>
            <td class="border px-2 py-1">
                <input type="text" class="w-full border rounded bg-gray-100 account-name-${index}" readonly />
                <input type="hidden" name="items[${index}][account]" class="account-id-${index}" />
            </td>
            <td class="border px-2 py-1 text-center">
                <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" data-index="${index}">X</button>
            </td>
        </tr>`;
        }

        function attachSelect2(index) {
            $(`select[data-index="${index}"]`).select2({
                placeholder: 'Cari item...',
                ajax: {
                    url: '/search-item',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: `${item.item_number} - ${item.item_name}`,
                            item_name: item.item_name,
                            unit: item.unit,
                            purchase_price: item.purchase_price,
                            tax_rate: item.tax_rate,
                            account_id: item.account_id,
                            account_name: item.account_name,
                            stock_quantity: item.stock_quantity // ✅ tambahkan ini
                        }))
                    }),
                    cache: true
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $(`.desc-${index}`).val(data.item_name);
                $(`.unit-${index}`).val(data.unit);
                $(`.purchase-${index}`).val(data.purchase_price);
                $(`.tax-${index}`).val(data.tax_rate + '%');
                $(`.account-name-${index}`).val(data.account_name);
                $(`.account-id-${index}`).val(data.account_id);
                $(`.qty-${index}`).val(data.stock_quantity);

                calculateAmount(index);
            });
        }

        function attachEvents(index) {
            $(`.qty-${index}, .order-${index}, .disc-${index}`).on('input', function() {
                calculateAmount(index);
                calculateBackOrder(index);
            });
        }

        function calculateBackOrder(index) {
            const qty = parseFloat($(`.qty-${index}`).val()) || 0;
            const order = parseFloat($(`.order-${index}`).val()) || 0;
            const backOrder = qty - order;
            $(`.back-${index}`).val(backOrder >= 0 ? backOrder : 0);
        }

        function calculateAmount(index) {
            const qty = parseFloat($(`.qty-${index}`).val()) || 0;
            const base_price = parseFloat($(`.base-${index}`).val()) || 0;
            const disc = parseFloat($(`.disc-${index}`).val()) || 0;

            const price = (qty * base_price) - disc;
            $(`.price-${index}`).val(price.toFixed(2));
            $(`.amount-${index}`).val(price.toFixed(2));

            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                const index = $(this).data('index');
                const amount = parseFloat($(`.amount-${index}`).val()) || 0;
                total += amount;
            });
            $('#grand-total').val(total.toFixed(2));
        }

        function addRow() {
            const newRow = generateRow(rowIndex);
            $('#item-table-body').append(newRow);
            attachSelect2(rowIndex);
            attachEvents(rowIndex);
            rowIndex++;
        }

        $('#add-row').on('click', function() {
            addRow();
        });

        $(document).on('click', '.remove-row', function() {
            const index = $(this).data('index');
            $(`tr[data-index="${index}"]`).remove();
            updateTotal();
        });

        $(document).ready(function() {
            addRow();
        });

        function cleanNumber(value) {
            return parseFloat(value.toString().replace(/[^\d.-]/g, '')) || 0;
        }

        $('form').on('submit', function() {
            const total = cleanNumber($('#grand-total').val());
            $('#grand-total').val(total.toFixed(2));

            $('.item-row').each(function() {
                const index = $(this).data('index');

                const price = cleanNumber($(`.price-${index}`).val());
                const amount = cleanNumber($(`.amount-${index}`).val());
                const base = cleanNumber($(`.base-${index}`).val());
                const tax = cleanNumber($(`.tax-${index}`).val());

                $(`.price-${index}`).val(price.toFixed(2));
                $(`.amount-${index}`).val(amount.toFixed(2));
                $(`.base-${index}`).val(base.toFixed(2));
                $(`.tax-${index}`).val(tax.toFixed(2));
            });
        });
    </script>
@endsection

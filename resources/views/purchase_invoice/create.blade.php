@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($purchase_invoice) ? route('purchase_invoice.update', $purchase_invoice->id) : route('purchase_invoice.store') }}">
                    @csrf
                    @if (isset($purchase_invoice))
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
                        <div class="mb-4">
                            <label for="invoice_number" class="block text-gray-700 font-medium mb-1">Invoice
                                Number</label>
                            <input type="text" id="invoice_number" name="invoice_number"
                                value="{{ old('invoice_number', $purchase_invoice->invoice_number ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" id="auto_generate" name="auto_generate" value="1"
                                    class="form-checkbox text-blue-600" onchange="toggleAutoGenerate()">
                                <span class="ml-2 text-sm text-gray-700">Generate Invoice Number secara otomatis</span>
                            </label>

                            @error('invoice_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Nama purchase_invoice_asset -->
                        <div class="mb-4">
                            <label for="nama_metode" class="block text-gray-700 font-medium mb-1">Payment Method
                            </label>
                            <select name="jenis_pembayaran_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Payment Method--</option>
                                @foreach ($jenis_pembayaran as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('jenis_pembayaran_id', $purchase_invoice->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pembayaran_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="customers" class="block text-gray-700 font-medium mb-1">Customers
                            </label>
                            <select name="customer_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">-- Customers--</option>
                                @foreach ($customer as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('customer_id', $purchase_invoice->customer_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- deskripsi purchase_invoice_asset -->
                        <div class="mb-4 md:col-span-2">
                            <label for="shipping_address" class="block text-gray-700 font-medium mb-1">Shipping
                                Address</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('shipping_address', $purchase_invoice->shipping_address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="use-order-number" class="mr-2">
                                Pakai Order Number
                            </label>
                        </div>
                        <div id="order-number-wrapper" class="mb-4 hidden">
                            <label for="purchase_order_id" class="block text-gray-700 font-medium mb-1">Order Number</label>
                            <select name="purchase_order_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($purchase_order as $data)
                                    <option value="{{ $data->id }}"
                                        {{ old('purchase_order_id', $purchase_invoice->purchase_order_id ?? '') == $data->id ? 'selected' : '' }}>
                                        {{ $data->order_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-4">
                            <label for="date_invoice" class="block text-gray-700 font-medium mb-1">Invoice Date
                            </label>
                            <input type="date" id="name" name="date_invoice" required
                                value="{{ old('date_invoice', $purchase_invoice->date_invoice ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('date_invoice')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="shipping_date" class="block text-gray-700 font-medium mb-1">Shipping Date
                            </label>
                            <input type="date" id="name" name="shipping_date" required
                                value="{{ old('shipping_date', $purchase_invoice->shipping_date ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            <table class="w-full mt-6 text-sm border" id="item-table">
                                <thead class="bg-gray-100 text-left">
                                    <tr>
                                        <th class="px-2 py-1">Item Number</th>
                                        <th class="px-2 py-1">Qty</th>
                                        <th class="px-2 py-1">Order</th>
                                        <th class="px-2 py-1">Back Order</th>
                                        <th class="px-2 py-1">Unit</th>
                                        <th class="px-2 py-1">Description</th>
                                        <th class="px-2 py-1">Price</th>
                                        <th class="px-2 py-1">Tax</th>
                                        <th class="px-2 py-1">Tax Amount</th>
                                        <th class="px-2 py-1">Amount</th>
                                        <th class="px-2 py-1">Account</th>
                                        <th class="px-2 py-1">Project</th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    <!-- Akan diisi lewat JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-4 mt-4">
                            <label for="freight" class="block text-gray-700 font-medium mb-1">Freight
                            </label>
                            <input type="number" id="name" name="freight" required
                                value="{{ old('freight', $purchase_invoice->freight ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('freight')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 mt-4">
                            <label for="early_payment_terms" class="block text-gray-700 font-medium mb-1">Early
                                Payments Terms
                            </label>
                            <input type="text" id="name" name="early_payment_terms" required
                                value="{{ old('early_payment_terms', $purchase_invoice->early_payment_terms ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('early_payment_terms')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 md:col-span-2 ">
                            <label for="messages" class="block text-gray-700 font-medium mb-1">Messages
                            </label>
                            <textarea id="messages" name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('messages', $purchase_invoice->messages ?? '') }}</textarea>
                            @error('messages')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($purchase_invoice) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('purchase_invoice.index') }}"
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
            const useOrderCheckbox = document.getElementById('use-order-number');
            const orderWrapper = document.getElementById('order-number-wrapper');
            const selectOrder = document.querySelector('select[name="purchase_order_id"]');
            const tbody = document.getElementById('items-body');
            let rowIndex = 0;

            // Toggle mode PO / Manual
            useOrderCheckbox.addEventListener('change', function() {
                tbody.innerHTML = '';
                if (this.checked) {
                    orderWrapper.classList.remove('hidden');
                } else {
                    orderWrapper.classList.add('hidden');
                    addEmptyRow();
                }
            });

            // Load item dari PO
            selectOrder.addEventListener('change', function() {
                const orderId = this.value;
                tbody.innerHTML = '';

                if (!orderId) return;

                fetch(`/purchase_invoice/get-items/${orderId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.items.forEach(item => {
                            addRowFromPO(item);
                        });
                    });
            });

            // Fungsi tambah row kosong (manual)
            function addEmptyRow() {
                const index = rowIndex++;
                const row = `
        <tr class="item-row" data-index="${index}">
            <td class="border px-2 py-1">
                <select name="items[${index}][item_id]" class="item-select w-full border rounded" data-index="${index}"></select>
            </td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][quantity]" class="qty-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][order]" class="order-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][back_order]" class="back-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][unit]" class="unit-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][item_description]" class="desc-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][price]" class="purchase-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1">
                <select name="items[${index}][tax]" class="tax-${index} w-full border rounded">
                    <option value="0">0%</option>
                    <option value="11">11%</option>
                    <option value="12">12%</option>
                </select>
            </td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][tax_amount]" class="tax_amount-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][amount]" class="amount-${index} w-full border rounded text-right" /></td>
            <td class="border px-2 py-1">
                <input type="text" class="w-full border rounded bg-gray-100 account-name-${index}" readonly />
                <input type="hidden" name="items[${index}][account_id]" class="account-id-${index}" />
            </td>
                   <td class="border px-2 py-1">
                        <select name="items[${index}][project_id]" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Project --</option>
                            @foreach ($project as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_project }} </option>
                            @endforeach
                        </select>
                        </td>
                    
            <td class="border px-2 py-1 text-center">
                <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" data-index="${index}">X</button>
            </td>
     
        </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
                attachSelect2(index);
            }

            // Fungsi tambah row dari PO
            function addRowFromPO(item) {
                const index = rowIndex++;
                const row = `
        <tr class="item-row" data-index="${index}">
            <td class="border px-2 py-1">
                <input type="hidden" name="items[${index}][item_id]" value="${item.id}">
                ${item.item_number}
            </td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][quantity]" value="${item.quantity}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][order]" value="${item.order}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][back_order]" value="${item.back_order}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][unit]" value="${item.unit}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][item_description]" value="${item.description}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][price]" value="${item.price}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" name="items[${index}][tax]" value="${item.tax}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][tax_amount]" value="${item.tax_amount}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="number" step="0.01" name="items[${index}][amount]" value="${item.amount}" class="w-full border rounded" /></td>
            <td class="border px-2 py-1">
                <input type="text" value="${item.account_name}" class="w-full border rounded bg-gray-100" readonly />
                <input type="hidden" name="items[${index}][account_id]" value="${item.account_id}" />
            </td>
                               <td class="border px-2 py-1">
        <select name="items[${index}][project_id]" 
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Project --</option>
        @foreach ($project as $item)
            <option value="{{ $item->id }}">{{ $item->nama_project }} </option>
        @endforeach
        </select>
        </td>
                <td class="border px-2 py-1 text-center">
                    <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" data-index="${index}">X</button>
                </td>
            </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            }

            // Attach Select2 untuk row manual
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
                                stock_quantity: item.stock_quantity
                            }))
                        }),
                        cache: true
                    }
                }).on('select2:select', function(e) {
                    const data = e.params.data;
                    $(`.desc-${index}`).val(data.item_name);
                    $(`.unit-${index}`).val(data.unit);
                    $(`.purchase-${index}`).val(data.purchase_price);
                    $(`.tax-${index}`).val(data.tax_rate);
                    $(`.account-name-${index}`).val(data.account_name);
                    $(`.account-id-${index}`).val(data.account_id);
                    $(`.qty-${index}`).val(data.stock_quantity);
                });
            }

            // Event hapus row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            // Tambah baris pertama manual saat load jika PO tidak dipilih
            if (!useOrderCheckbox.checked) {
                addEmptyRow();
            }
        });
    </script>

@endsection

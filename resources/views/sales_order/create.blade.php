@extends('layouts.app')

@section('content')

    <div class="py-4">
        <div class="w-full px-2">
            <div class="bg-white shadow rounded p-4">

                <!-- Judul Menu -->
                <h2 class="text-lg font-bold mb-4">Sales Order</h2>

                <form method="POST"
                    action="{{ isset($sales_order) ? route('sales_order.update', $sales_order->id) : route('sales_order.store') }}">
                    @csrf
                    @if (isset($sales_order))
                        @method('PUT')
                    @endif

                    <!-- Error -->
                    @if ($errors->any())
                        <div class="mb-2 text-red-600 bg-red-100 p-2 rounded text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Grid -->
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <!-- Customers -->
                        <div>
                            <label class="block font-medium mb-1">Customers</label>
                            <select name="customer_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                <option value="">-- Customers --</option>
                                @foreach ($customer as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('customer_id', $sales_order->customer_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment -->
                        <div>
                            <label class="block font-medium mb-1">Payment Method</label>
                            <select name="jenis_pembayaran_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                <option value="">-- Payment Method --</option>
                                @foreach ($jenis_pembayaran as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('jenis_pembayaran_id', $sales_order->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee -->
                        <div>
                            <label class="block font-medium mb-1">Employee</label>
                            <select name="sales_person_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                <option value="">-- Employee --</option>
                                @foreach ($employee as $level)
                                    <option value="{{ $level->id }}"
                                        {{ old('employee_id', $sales_order->employee_id ?? '') == $level->id ? 'selected' : '' }}>
                                        {{ $level->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Order Number -->
                        <div>
                            <label class="block font-medium mb-1">Order Number</label>
                            <input type="text" name="order_number"
                                value="{{ old('order_number', $sales_order->order_number ?? '') }}"
                                class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>

                        <!-- Date Order -->
                        <div>
                            <label class="block font-medium mb-1">Date Order</label>
                            <input type="date" name="date_order"
                                value="{{ old('date_order', $sales_order->date_order ?? '') }}"
                                class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>

                        <!-- Shipping Date -->
                        <div>
                            <label class="block font-medium mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                value="{{ old('shipping_date', $sales_order->shipping_date ?? '') }}"
                                class="w-full border rounded px-2 py-1 text-sm" required>
                        </div>

                        <!-- Shipping Address -->
                        <div class="col-span-3">
                            <label class="block font-medium mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2" class="w-full border rounded px-2 py-1 text-sm">{{ old('shipping_address', $sales_order->shipping_address ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div class="mt-4">
                        <h3 class="font-semibold text-sm mb-2">Order Items</h3>
                        <div class="overflow-x-auto border rounded">
                            <table class="w-full border-collapse border">
                                <thead>
                                    <tr>
                                        <th class="border px-2 py-1 w-80">Item</th>
                                        <th class="border px-2 py-1">Qty</th>
                                        <th class="border px-2 py-1">Order</th>
                                        <th class="border px-2 py-1">BackOrder</th>
                                        <th class="border px-2 py-1">Unit</th>
                                        <th class="border px-2 py-1">Desc</th>
                                        <th class="border px-2 py-1">Base Price</th>
                                        <th class="border px-2 py-1">Disc</th>
                                        <th class="border px-2 py-1">Price</th>
                                        <th class="border px-2 py-1">Amount</th>
                                        <th class="border px-2 py-1">Tax Rate</th>
                                        <th class="border px-2 py-1">Tax Value</th>
                                        <th class="border px-2 py-1">Final</th>
                                        <th class="border px-2 py-1">Account</th>
                                        <th class="border px-2 py-1">#</th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9" class="text-right font-bold border px-2 py-1">Subtotal</td>
                                        <td colspan="2" class="border px-2 py-1">
                                            <input type="text" id="subtotal"
                                                class="w-full border rounded text-right bg-gray-100" readonly>
                                        </td>
                                        <td colspan="3" class="border px-2 py-1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-right font-bold border px-2 py-1">Total Pajak</td>
                                        <td colspan="2" class="border px-2 py-1">
                                            <input type="text" id="total-tax"
                                                class="w-full border rounded text-right bg-gray-100" readonly>
                                        </td>
                                        <td colspan="3" class="border px-2 py-1"></td>
                                    </tr>
                                    <!-- Freight row -->
                                    <tr>
                                        <td colspan="9" class="text-right font-bold border px-2 py-1">Freight</td>
                                        <td colspan="2" class="border px-2 py-1">
                                            <input type="text" name="freight" id="freight"
                                                class="w-full border rounded text-right"
                                                value="{{ old('freight', $sales_order->freight ?? '') }}">
                                        </td>
                                        <td colspan="3" class="border px-2 py-1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-right font-bold border px-2 py-1">Grand Total</td>
                                        <td colspan="2" class="border px-2 py-1">
                                            <input type="text" id="grand-total"
                                                class="w-full border rounded text-right bg-gray-100 font-bold" readonly>
                                        </td>
                                        <td colspan="3" class="border px-2 py-1"></td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>
                        <button type="button" id="add-row"
                            class="mt-2 px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">+ Add
                            Row</button>
                    </div>
                    <!-- Bottom Form -->
                    <div class="grid grid-cols-3 gap-4 text-sm mt-4">
                        <div>
                            <label class="block font-medium mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                value="{{ old('early_payment_terms', $sales_order->early_payment_terms ?? '') }}"
                                class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block font-medium mb-1">Messages</label>
                            <textarea name="messages" rows="2" class="w-full border rounded px-2 py-1 text-sm">{{ old('messages', $sales_order->messages ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 flex space-x-2">
                        <button type="submit"
                            class="px-4 py-1 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">
                            {{ isset($sales_order) ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('sales_order.index') }}"
                            class="px-4 py-1 bg-gray-300 text-sm text-gray-700 rounded hover:bg-gray-400">Cancel</a>
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
            <td class="border px-2 py-1"><input type="number" name="items[${index}][back_order]" class="back-${index} w-full border rounded" readonly /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][unit]" class="unit-${index} w-full border rounded" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][description]" class="desc-${index} w-full border rounded" /></td>

            <td class="border px-2 py-1"><input type="text" name="items[${index}][base_price]" class="base-${index} w-full border rounded text-right" /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][discount]" class="disc-${index} w-full border rounded text-right" value="0" /></td>

            <td class="border px-2 py-1"><input type="text" name="items[${index}][price]" class="price-${index} w-full border rounded text-right" readonly /></td>
            <td class="border px-2 py-1"><input type="text" name="items[${index}][amount]" class="amount-${index} w-full border rounded text-right" readonly /></td>

            <!-- Tax dropdown -->
            <td class="border px-2 py-1">
                <select name="items[${index}][tax_rate]" class="tax-${index} w-full border rounded">
                    <option value="0">Tanpa Pajak</option>
                    <option value="11">11%</option>
                    <option value="12">12%</option>
                </select>
            </td>

            <!-- Nilai pajak -->
            <td class="border px-2 py-1">
                <input type="text" name="items[${index}][tax_value]" class="taxval-${index} w-full border rounded text-right" readonly />
            </td>

            <!-- Final (amount + tax) -->
            <td class="border px-2 py-1">
                <input type="text" name="items[${index}][final]" class="final-${index} w-full border rounded text-right" readonly />
            </td>

            <td class="border px-2 py-1">
                <input type="text" class="w-full border rounded bg-gray-100 account-name-${index}" />
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
                            base_price: item.base_price,
                            tax_rate: item.tax_rate, // asumsikan 0/11/12 dari server
                            account_id: item.account_id,
                            account_name: item.account_name
                        }))
                    }),
                    cache: true
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $(`.desc-${index}`).val(data.item_name);
                $(`.unit-${index}`).val(data.unit);
                $(`.base-${index}`).val(formatNumber(data.base_price));

                // Set dropdown pajak dengan value numerik (0/11/12)
                const rate = (typeof data.tax_rate !== 'undefined' && data.tax_rate !== null) ?
                    String(parseFloat(String(data.tax_rate).toString().replace('%', '')) || 0) :
                    '0';
                $(`.tax-${index}`).val(rate).trigger('change');

                $(`.account-name-${index}`).val(data.account_name);
                $(`.account-id-${index}`).val(data.account_id);

                calculateAmount(index);
            });
        }

        function attachEvents(index) {
            // Input numerik yang mempengaruhi amount
            $(`.qty-${index}, .order-${index}, .disc-${index}, .base-${index}`)
                .on('input', function() {
                    calculateAmount(index);
                    calculateBackOrder(index);
                })
                .on('blur', function() {
                    // Format ulang angka saat selesai input
                    const cls = $(this).attr("class");
                    let val = parseNumber($(this).val());
                    if (cls.includes("base-") || cls.includes("disc-")) {
                        $(this).val(formatNumber(val));
                    }
                });

            // Dropdown pajak harus trigger hitung ulang
            $(`.tax-${index}`).on('change', function() {
                calculateAmount(index);
            });
        }

        function calculateBackOrder(index) {
            const qty = parseFloat($(`.qty-${index}`).val()) || 0;
            const order = parseFloat($(`.order-${index}`).val()) || 0;
            const backOrder = qty - order;
            $(`.back-${index}`).val(backOrder >= 0 ? backOrder : 0);
        }

        function calculateAmount(index) {
            const order = parseFloat($(`.order-${index}`).val()) || 0;
            const base_price = parseNumber($(`.base-${index}`).val());
            const disc = parseNumber($(`.disc-${index}`).val());

            // Subtotal kotor
            const subtotal = order * base_price;

            // Total setelah diskon (clamp minimal 0)
            const amount = Math.max(subtotal - disc, 0);

            // Harga akhir per unit (sesuai definisi "Price")
            const pricePerUnit = order > 0 ? amount / order : 0;

            $(`.price-${index}`).val(formatNumber(pricePerUnit));
            $(`.amount-${index}`).val(formatNumber(amount));

            // Pajak dihitung dari amount (setelah diskon)
            const taxRate = parseFloat($(`.tax-${index}`).val()) || 0; // 0 / 11 / 12
            const taxValue = (amount * taxRate) / 100;
            const finalValue = amount + taxValue;

            $(`.taxval-${index}`).val(formatNumber(taxValue));
            $(`.final-${index}`).val(formatNumber(finalValue));

            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            let totalTax = 0;
            let grandTotal = 0;

            $('.item-row').each(function() {
                const index = $(this).data('index');
                const amount = parseNumber($(`.amount-${index}`).val());
                const taxVal = parseNumber($(`.taxval-${index}`).val());
                const finalVal = parseNumber($(`.final-${index}`).val());

                total += amount;
                totalTax += taxVal;
                grandTotal += finalVal;
            });

            // Freight
            const freight = parseNumber($('#freight').val());

            // Update fields
            $('#subtotal').val(formatNumber(total));
            $('#total-tax').val(formatNumber(totalTax));
            $('#grand-total').val(formatNumber(grandTotal + freight));
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
        $('#freight').on('input', function() {
            updateTotal();
        }).on('blur', function() {
            let val = parseNumber($(this).val());
            $(this).val(formatNumber(val));
        });


        $(document).on('click', '.remove-row', function() {
            const index = $(this).data('index');
            $(`tr[data-index="${index}"]`).remove();
            updateTotal();
        });

        $(document).ready(function() {
            addRow();
        });

        // Format angka tampilan
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0, // ✅ tanpa angka di belakang koma
                maximumFractionDigits: 0
            }).format(Number(num) || 0);
        }

        // Parse dari "1.234,56" -> 1234.56
        function parseNumber(value) {
            if (value === null || typeof value === 'undefined') return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
        }

        // Saat submit: kirim angka mentah (decimal) ke server
        $('form').on('submit', function() {
            // Jika punya grand total, ubah ke decimal 2 angka
            if ($('#grand-total').length) {
                const total = parseNumber($('#grand-total').val());
                $('#grand-total').val(total.toFixed(2));
            }

            $('.item-row').each(function() {
                const index = $(this).data('index');

                const price = parseNumber($(`.price-${index}`).val());
                const amount = parseNumber($(`.amount-${index}`).val());
                const base = parseNumber($(`.base-${index}`).val());
                const discount = parseNumber($(`.disc-${index}`).val());
                const taxVal = parseNumber($(`.taxval-${index}`).val());
                const finalVal = parseNumber($(`.final-${index}`).val());
                // NOTE: .tax-${index} adalah <select>, jangan diubah ke toFixed!
                // Biarkan value-nya tetap "0" / "11" / "12"

                $(`.price-${index}`).val(price.toFixed(2));
                $(`.amount-${index}`).val(amount.toFixed(2));
                $(`.base-${index}`).val(base.toFixed(2));
                $(`.disc-${index}`).val(discount.toFixed(2));
                $(`.taxval-${index}`).val(taxVal.toFixed(2));
                $(`.final-${index}`).val(finalVal.toFixed(2));
            });
        });
    </script>

@endsection

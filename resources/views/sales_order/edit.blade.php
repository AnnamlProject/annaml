@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-xl">
                <form method="POST" action="{{ route('sales_order.update', $salesOrder->id) }}">
                    @csrf
                    @method('PUT')

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
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select id="jenis_pembayaran_id" name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $salesOrder->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="account-wrapper" class="hidden">
                            <label class="font-medium text-gray-700 block mb-1">Account</label>
                            <select id="account_id" name="payment_method_account_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                <option value="">-- Pilih Account --</option>
                            </select>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Location Inventory</label>
                            <select name="location_id" id="location_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($location_inventory as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ $salesOrder->location_id == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->kode_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Customer</label>
                            <select name="customer_id" id="customer_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}"
                                        {{ $salesOrder->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Order Number</label>
                            <input type="text" name="order_number"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesOrder->order_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Date Order</label>
                            <input type="date" name="date_order"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesOrder->date_order }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesOrder->shipping_date }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Sales Person</label>
                            <select name="employee_id" id="employee_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ $salesOrder->employee_id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $salesOrder->shipping_address }}</textarea>
                        </div>
                    </div>

                    {{-- TABEL ITEM --}}
                    <div class="mt-8">
                        <h3 class="font-semibold text-lg mb-2">🛒 Order Items</h3>
                        <div class="overflow-auto">
                            <table class="w-full border text-sm text-left shadow-md">
                                <thead class="bg-blue-100 text-gray-700">
                                    <tr>
                                        <th class="p-2">Item</th>
                                        <th>Qty</th>
                                        <th>Order</th>
                                        <th>Back Order</th>
                                        <th>Unit</th>
                                        <th>Description</th>
                                        <th>Base Price</th>
                                        <th>Discount</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                        <th>Tax %</th>
                                        <th>Tax Value</th>
                                        <th>Final</th>
                                        <th>Account</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body">
                                    @foreach ($salesOrder->details as $i => $detail)
                                        <tr class="item-row bg-white even:bg-gray-50 border-b"
                                            data-index="{{ $i }}">
                                            <!-- Item ID -->
                                            <td>
                                                <input type="hidden" name="items[{{ $i }}][item_id]"
                                                    value="{{ $detail->item_id }}">
                                                <input type="text" class="w-full border rounded"
                                                    value="{{ $detail->item_description }}">
                                            </td>

                                            <!-- Quantity -->
                                            <td>
                                                <input type="number" name="items[{{ $i }}][quantity]"
                                                    class="w-full border rounded qty-{{ $i }}"
                                                    value="{{ $detail->quantity }}"
                                                    oninput="calculateBackOrder({{ $i }}); calculateAmount({{ $i }});">
                                            </td>

                                            <!-- Order -->
                                            <td>
                                                <input type="number" name="items[{{ $i }}][order]"
                                                    class="w-full border rounded order-{{ $i }}"
                                                    value="{{ $detail->order }}"
                                                    oninput="calculateBackOrder({{ $i }}); calculateAmount({{ $i }});">
                                            </td>

                                            <!-- Back Order -->
                                            <td>
                                                <input type="number" readonly
                                                    class="w-full border rounded bg-gray-100 back-{{ $i }}"
                                                    name="items[{{ $i }}][back_order]"
                                                    value="{{ $detail->back_order }}">
                                            </td>

                                            <!-- Unit -->
                                            <td>
                                                <input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    name="items[{{ $i }}][unit]" value="{{ $detail->unit }}">
                                            </td>

                                            <!-- Description -->
                                            <td>
                                                <input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    name="items[{{ $i }}][description]"
                                                    value="{{ $detail->item_description }}">
                                            </td>

                                            <!-- Base Price -->
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 base-display-{{ $i }}"
                                                    value="{{ number_format($detail->base_price, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][base_price]"
                                                    class="base-hidden-{{ $i }}"
                                                    value="{{ $detail->base_price }}">
                                            </td>

                                            <!-- Discount -->
                                            <td>
                                                <input type="text"
                                                    class="w-full border rounded disc-display-{{ $i }}"
                                                    value="{{ number_format($detail->discount, 0, '.', ',') }}"
                                                    oninput="calculateAmount({{ $i }}); this.value = this.value.replace(/[^0-9,]/g,'');">
                                                <input type="hidden" name="items[{{ $i }}][discount]"
                                                    class="disc-hidden-{{ $i }}"
                                                    value="{{ $detail->discount }}">
                                            </td>

                                            <!-- Price -->
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 price-display-{{ $i }}"
                                                    value="{{ number_format($detail->price, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][price]"
                                                    class="price-hidden-{{ $i }}"
                                                    value="{{ $detail->price }}">
                                            </td>

                                            <!-- Amount -->
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 amount-display-{{ $i }}"
                                                    value="{{ number_format($detail->amount, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][amount]"
                                                    class="amount-hidden-{{ $i }}"
                                                    value="{{ $detail->amount }}">
                                            </td>

                                            <!-- Tax -->
                                            <td>
                                                <select name="items[{{ $i }}][tax_id]"
                                                    class="w-full border rounded tax-{{ $i }}">
                                                    <option value="">-- Pilih Pajak --</option>
                                                    @foreach ($sales_taxes as $tax)
                                                        <option value="{{ $tax->id }}"
                                                            data-rate="{{ $tax->rate }}"
                                                            {{ $detail->tax_id == $tax->id ? 'selected' : '' }}>
                                                            ({{ $tax->rate }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <!-- Tax Value -->
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 taxval-display-{{ $i }}"
                                                    value="{{ number_format($detail->tax ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][tax_value]"
                                                    class="taxval-hidden-{{ $i }}"
                                                    value="{{ $detail->tax ?? 0 }}">
                                            </td>

                                            <!-- Final -->
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 final-display-{{ $i }}"
                                                    value="{{ number_format($detail->final ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][final]"
                                                    class="final-hidden-{{ $i }}"
                                                    value="{{ $detail->final ?? 0 }}">
                                            </td>

                                            <!-- Account -->
                                            <td>
                                                <input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    value="{{ optional($detail->account)->nama_akun }}">
                                                <input type="hidden" name="items[{{ $i }}][account]"
                                                    value="{{ $detail->account_id }}">
                                            </td>

                                            <!-- Remove -->
                                            <td class="text-center">
                                                <button type="button" class="remove-row text-red-500 font-bold"
                                                    data-index="{{ $i }}">×</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                                                value="{{ old('freight', $salesOrder->freight ?? '') }}">
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
                    </div>

                    {{-- Info Tambahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesOrder->early_payment_terms }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Messages</label>
                            <textarea name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $salesOrder->messages }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            💾 Update
                        </button>
                        <a href="{{ route('sales_order.index') }}"
                            class="px-6 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function parseNumber(val) {
            if (!val) return 0;
            return parseFloat(val.toString().replace(/,/g, '')) || 0;
        }

        function calculateAmount(index) {
            let qty = parseNumber(document.querySelector(`.qty-${index}`).value);
            let order = parseNumber(document.querySelector(`.order-${index}`).value);
            let backOrder = order - qty;
            if (backOrder < 0) backOrder = 0;
            document.querySelector(`.back-${index}`).value = backOrder;

            let basePrice = parseNumber(document.querySelector(`.base-hidden-${index}`).value);
            let discount = parseNumber(document.querySelector(`.disc-display-${index}`).value);

            // update hidden discount
            document.querySelector(`.disc-hidden-${index}`).value = discount;

            let price = basePrice - discount;
            document.querySelector(`.price-display-${index}`).value = formatNumber(price);
            document.querySelector(`.price-hidden-${index}`).value = price;

            let amount = qty * price;
            document.querySelector(`.amount-display-${index}`).value = formatNumber(amount);
            document.querySelector(`.amount-hidden-${index}`).value = amount;

            // Pajak
            let taxSelect = document.querySelector(`.tax-${index}`);
            let rate = parseFloat(taxSelect.selectedOptions[0]?.dataset.rate || 0);
            let taxValue = amount * (rate / 100);
            document.querySelector(`.taxval-display-${index}`).value = formatNumber(taxValue);
            document.querySelector(`.taxval-hidden-${index}`).value = taxValue;

            // Final
            let final = amount + taxValue;
            document.querySelector(`.final-display-${index}`).value = formatNumber(final);
            document.querySelector(`.final-hidden-${index}`).value = final;

            // Update footer
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0,
                totalTax = 0,
                grandTotal = 0;

            document.querySelectorAll('tbody tr.item-row').forEach(row => {
                let index = row.dataset.index;
                subtotal += parseNumber(document.querySelector(`.amount-hidden-${index}`).value);
                totalTax += parseNumber(document.querySelector(`.taxval-hidden-${index}`).value);
            });

            grandTotal = subtotal + totalTax + parseNumber(document.getElementById('freight').value);

            document.getElementById('subtotal').value = formatNumber(subtotal);
            document.getElementById('total-tax').value = formatNumber(totalTax);
            document.getElementById('grand-total').value = formatNumber(grandTotal);
        }

        // Pastikan setiap kali freight berubah -> update grand total
        document.getElementById('freight').addEventListener('input', calculateTotals);

        // Inisialisasi awal
        document.addEventListener('DOMContentLoaded', calculateTotals);
    </script>

    <script>
        $(document).ready(function() {
            function initSelect2(selector, url, mapper, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(mapper)
                            };
                        },
                        cache: true
                    },
                    allowClear: true,
                    width: '100%'
                });
            }

            // ✅ Customers
            initSelect2(
                '#customer_id',
                '{{ route('customers.search') }}',
                function(customer) {
                    return {
                        id: customer.id,
                        text: customer.nama_customers
                    };
                },
                "-- Customers --"
            );

            // ✅ Employees
            initSelect2(
                '#employee_id',
                '{{ route('employee.search') }}',
                function(employee) {
                    return {
                        id: employee.id,
                        text: employee.nama_karyawan
                    };
                },
                "-- Employees --"
            );
        });
    </script>
    <script>
        const $pmSelect = $('#jenis_pembayaran_id');
        const $account = $('#account_id');
        const $wrapper = $('#account-wrapper');

        function clearAccounts() {
            $account.empty().append('<option value="">-- Pilih Account --</option>');
            $wrapper.addClass('hidden');
        }

        function loadAccounts(pmId) {
            if (!pmId) {
                clearAccounts();
                return;
            }

            $.getJSON("{{ route('payment-methods.accounts', ['id' => 'PM_ID']) }}".replace('PM_ID', pmId))
                .done(function(res) {
                    clearAccounts();
                    (res.accounts || []).forEach(function(a) {
                        const text = `${a.kode_akun || '-'} - ${a.nama_akun || '-'}`;
                        $account.append(`<option value="${a.account_id}">${text}</option>`);
                    });

                    // ✅ Preselect account lama
                    const oldVal =
                        "{{ old('payment_method_account_id', $salesOrder->payment_method_account_id ?? '') }}";
                    if (oldVal) $account.val(oldVal);

                    $wrapper.removeClass('hidden');
                })
                .fail(function() {
                    clearAccounts();
                    alert('Gagal memuat account untuk Payment Method ini.');
                });
        }

        // Event change
        $pmSelect.on('change', function() {
            loadAccounts($(this).val());
        });

        // ✅ Auto load saat edit
        if ($pmSelect.val()) {
            loadAccounts($pmSelect.val());
        }

        function formatNumber(num) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function calculateBackOrder(index) {
            const qty = parseFloat(document.querySelector(`.qty-${index}`).value) || 0;
            const order = parseFloat(document.querySelector(`.order-${index}`).value) || 0;
            const back = qty - order;
            document.querySelector(`.back-${index}`).value = back;
        }

        function calculateAmount(index) {
            const order = parseFloat(document.querySelector(`.order-${index}`).value) || 0;
            const basePrice = parseFloat(document.querySelector(`.base-display-${index}`).value.replace(/,/g, '')) || 0;
            const discount = parseFloat(document.querySelector(`.disc-display-${index}`).value.replace(/,/g, '')) || 0;
            const taxPercent = parseFloat(document.querySelector(`.tax-${index}`).value) || 0;

            // price per item (after discount)
            const price = order * basePrice - discount;
            const amount = price;
            const taxValue = amount * (taxPercent / 100);
            const final = amount + taxValue;

            // Update input tampilan (format ribuan)
            document.querySelector(`.price-display-${index}`).value = formatNumber(price);
            document.querySelector(`.amount-display-${index}`).value = formatNumber(amount);
            document.querySelector(`.taxval-display-${index}`).value = formatNumber(taxValue);
            document.querySelector(`.final-display-${index}`).value = formatNumber(final);

            // Update hidden input untuk backend (angka murni)
            document.querySelector(`.price-hidden-${index}`).value = price;
            document.querySelector(`.amount-hidden-${index}`).value = amount;
            document.querySelector(`.taxval-hidden-${index}`).value = taxValue;
            document.querySelector(`.final-hidden-${index}`).value = final;
            document.querySelector(`.base-hidden-${index}`).value = basePrice;
            document.querySelector(`.disc-hidden-${index}`).value = discount;
        }

        // Tambahkan listener untuk setiap baris
        document.querySelectorAll('.item-row').forEach(row => {
            const index = row.dataset.index;
            row.querySelectorAll('.qty-' + index + ', .order-' + index + ', .disc-display-' + index + ', .tax-' +
                    index)
                .forEach(input => {
                    input.addEventListener('input', function() {
                        calculateBackOrder(index);
                        calculateAmount(index);
                    });
                });
        });
    </script>

@endsection

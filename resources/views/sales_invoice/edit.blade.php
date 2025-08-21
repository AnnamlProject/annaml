@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-xl">
                <form method="POST" action="{{ route('sales_invoice.update', $salesInvoice->id) }}">
                    @csrf
                    @method('PUT')

                    @if (session('error'))
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Invoice Number</label>
                            <input type="text" name="invoice_number"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->invoice_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $salesInvoice->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Customer</label>
                            <select name="customers_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}"
                                        {{ $salesInvoice->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $salesInvoice->shipping_address }}</textarea>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Order Number</label>
                            <input type="hidden" name="sales_order_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->sales_order_id }}" required>
                            <input type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->salesOrder->order_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Invoice Date</label>
                            <input type="date" name="invoice_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->invoice_date }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->shipping_date }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Sales Person</label>
                            <select name="employee_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ $salesInvoice->employee_id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
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
                                        <th>Project</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body">
                                    @foreach ($salesInvoice->details as $i => $detail)
                                        <tr class="item-row bg-white even:bg-gray-50 border-b"
                                            data-index="{{ $i }}">
                                            <!-- Item ID -->
                                            <td>
                                                <input type="hidden" name="items[{{ $i }}][item_id]"
                                                    value="{{ $detail->item_id }}">
                                                <input type="text" class="w-full border rounded"
                                                    value="{{ $detail->item->item_name }}">
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
                                                <input type="number" name="items[{{ $i }}][order_quantity]"
                                                    class="w-full border rounded order-{{ $i }}"
                                                    value="{{ $detail->order_quantity }}"
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
                                                    value="{{ $detail->description }}">
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
                                                <select class="w-full border rounded tax-{{ $i }}"
                                                    name="items[{{ $i }}][tax]"
                                                    onchange="calculateAmount({{ $i }});">
                                                    <option value="0" {{ $detail->tax == 0 ? 'selected' : '' }}>Tidak
                                                    </option>
                                                    <option value="11" {{ $detail->tax == 11 ? 'selected' : '' }}>11%
                                                    </option>
                                                    <option value="12" {{ $detail->tax == 12 ? 'selected' : '' }}>12%
                                                    </option>
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

                                            <td>
                                                <select name="items[{{ $i }}][project]" id="ptkp_id"
                                                    class="w-full border rounded bg-gray-100">
                                                    <option value="">-- Pilih Project --</option>
                                                    @foreach ($project as $g)
                                                        <option value="{{ $g->id }}"
                                                            {{ isset($salesInvoice) && $salesInvoice->project_id == $g->id ? 'selected' : '' }}>
                                                            {{ $g->nama_project }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[{{ $i }}][project]"
                                                    value="{{ $detail->project_id }}">
                                            </td>

                                            <!-- Remove -->
                                            <td class="text-center">
                                                <button type="button" class="remove-row text-red-500 font-bold"
                                                    data-index="{{ $i }}">×</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Info Tambahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Freight</label>
                            <input type="text" name="freight"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->freight }}">
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $salesInvoice->early_payment_terms }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Messages</label>
                            <textarea name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $salesInvoice->messages }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            💾 Update
                        </button>
                        <a href="{{ route('sales_invoice.index') }}"
                            class="px-6 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
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

@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-xl">
                <form method="POST" action="{{ route('purchase_order.update', $purchaseOrder->id) }}">
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


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $purchaseOrder->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Customer</label>
                            <select name="customer_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}"
                                        {{ $purchaseOrder->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">{{ $purchaseOrder->shipping_address }}</textarea>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Order Number</label>
                            <input type="text" name="order_number"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseOrder->order_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Date Order</label>
                            <input type="date" name="date_order"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseOrder->date_order }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseOrder->shipping_date }}" required>
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
                                        <th>Price</th>
                                        <th>Tax</th>
                                        <th>Tax Amount</th>
                                        <th>Amount</th>
                                        <th>Account</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body">
                                    @foreach ($purchaseOrder->details as $i => $detail)
                                        <tr class="bg-white even:bg-gray-50 border-b">
                                            <td><input type="hidden" name="items[{{ $i }}][item_id]"
                                                    class="w-full border rounded" value="{{ $detail->item_id }}">
                                                <input type="text" class="w-full border rounded"
                                                    value="{{ $detail->item_description }}">
                                            </td>
                                            <td><input type="number" name="items[{{ $i }}][quantity]"
                                                    class="w-full border rounded qty-{{ $i }}"
                                                    value="{{ $detail->quantity }}"
                                                    oninput="calculateBackOrder({{ $i }}); calculateAmount({{ $i }});">
                                            </td>
                                            <td><input type="number" name="items[{{ $i }}][order]"
                                                    class="w-full border rounded order-{{ $i }}"
                                                    value="{{ $detail->order }}"
                                                    oninput="calculateBackOrder({{ $i }}); calculateAmount({{ $i }});">
                                            </td>
                                            <td><input type="number" readonly
                                                    class="w-full border rounded bg-gray-100 back-{{ $i }}"
                                                    name="items[{{ $i }}][back_order]"
                                                    value="{{ $detail->back_order }}"></td>
                                            <td><input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    name="items[{{ $i }}][unit]" value="{{ $detail->unit }}">
                                            </td>
                                            <td><input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    name="items[{{ $i }}][description]"
                                                    value="{{ $detail->item_description }}"></td>


                                            <td><input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 price-display-{{ $i }}"
                                                    value="{{ number_format($detail->price ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][price]"
                                                    class="price-hidden-{{ $i }}" value="{{ $detail->price }}">
                                            </td>
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
                                            <td><input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 tax_amount-display-{{ $i }}"
                                                    value="{{ number_format($detail->tax_amount ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][tax_amount]"
                                                    class="tax_amount-hidden-{{ $i }}"
                                                    value="{{ $detail->tax_amount }}">
                                            </td>
                                            <td><input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 amount-display-{{ $i }}"
                                                    name="items[{{ $i }}][amount]"
                                                    value="{{ number_format($detail->amount ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][amount]"
                                                    class="amount-hidden-{{ $i }}"
                                                    value="{{ $detail->amount }}">
                                            </td>
                                            <td>
                                                <input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    value="{{ optional($detail->account)->nama_akun }}">
                                                <input type="hidden" name="items[{{ $i }}][account]"
                                                    value="{{ $detail->account_id }}">
                                            </td>
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
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseOrder->freight }}">
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseOrder->early_payment_terms }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Messages</label>
                            <textarea name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">{{ $purchaseOrder->messages }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            💾 Update
                        </button>
                        <a href="{{ route('purchase_order.index') }}"
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
            const taxPercent = parseFloat(document.querySelector(`.tax-${index}`).value) || 0;
            const price = parseFloat(document.querySelector(`.price-hidden-${index}`).value) || 0;

            // Hitung jumlah
            const baseAmount = order * price;
            const taxAmount = baseAmount * (taxPercent / 100);
            const final = baseAmount + taxAmount;

            // Update tampilan
            document.querySelector(`.price-display-${index}`).value = formatNumber(price);
            document.querySelector(`.amount-display-${index}`).value = formatNumber(final);
            document.querySelector(`.tax_amount-display-${index}`).value = formatNumber(taxAmount);

            // Update hidden input untuk submit ke backend
            document.querySelector(`.price-hidden-${index}`).value = price;
            document.querySelector(`.amount-hidden-${index}`).value = final;
            document.querySelector(`.tax_amount-hidden-${index}`).value = taxAmount;
        }

        // Tambahkan listener ke setiap baris
        document.querySelectorAll('.item-row').forEach(row => {
            const index = row.dataset.index;
            [
                `.qty-${index}`,
                `.order-${index}`,
                `.tax-${index}`
            ].forEach(selector => {
                const input = document.querySelector(selector);
                if (input) {
                    input.addEventListener('input', function() {
                        calculateBackOrder(index);
                        calculateAmount(index);
                    });
                }
            });
        });
    </script>

@endsection

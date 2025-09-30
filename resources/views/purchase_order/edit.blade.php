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

                    <!-- FORM HEADER -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select id="jenis_pembayaran_id" name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $purchaseOrder->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="account-wrapper" class="hidden">
                            <label class="font-medium text-gray-700 block mb-1">Account</label>
                            <select id="account_id" name="account_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                <option value="">-- Pilih Account --</option>
                            </select>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Location Inventory</label>
                            <select name="location_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                @foreach ($locationInventory as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ $purchaseOrder->location_id == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->kode_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Vendor</label>
                            <select name="vendor_id" id="vendor_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ $purchaseOrder->vendor_id == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->nama_vendors }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Order Number</label>
                            <input type="text" name="order_number"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50"
                                value="{{ $purchaseOrder->order_number }}" readonly>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Date Order</label>
                            <input type="date" name="date_order"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50"
                                value="{{ $purchaseOrder->date_order }}" required>
                        </div>

                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50"
                                value="{{ $purchaseOrder->shipping_date }}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">{{ $purchaseOrder->shipping_address }}</textarea>
                        </div>
                    </div>

                    <!-- TABEL ITEM -->
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
                                            <td>
                                                <input type="hidden" name="items[{{ $i }}][item_id]"
                                                    value="{{ $detail->item_id }}">
                                                <input type="text" class="w-full border rounded bg-gray-100"
                                                    value="{{ $detail->item_description }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $i }}][quantity]"
                                                    class="w-full border rounded qty-{{ $i }}"
                                                    value="{{ $detail->quantity }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $i }}][order]"
                                                    class="w-full border rounded order-{{ $i }}"
                                                    value="{{ $detail->order }}">
                                            </td>
                                            <td>
                                                <input type="number" readonly
                                                    class="w-full border rounded bg-gray-100 back-{{ $i }}"
                                                    name="items[{{ $i }}][back_order]"
                                                    value="{{ $detail->back_order }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $i }}][unit]"
                                                    class="w-full border rounded" value="{{ $detail->unit }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $i }}][description]"
                                                    class="w-full border rounded bg-gray-100" readonly
                                                    value="{{ $detail->item_description }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="items[{{ $i }}][price]"
                                                    class="w-full border rounded price-hidden-{{ $i }}"
                                                    value="{{ $detail->price }}">
                                            </td>
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
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 tax_amount-display-{{ $i }}"
                                                    value="{{ number_format($detail->tax_amount ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][tax_amount]"
                                                    class="tax_amount-hidden tax_amount-hidden-{{ $i }}"
                                                    value="{{ $detail->tax_amount }}">
                                            </td>
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border rounded bg-gray-100 amount-display-{{ $i }}"
                                                    value="{{ number_format($detail->amount ?? 0, 0, '.', ',') }}">
                                                <input type="hidden" name="items[{{ $i }}][amount]"
                                                    class="amount-hidden amount-hidden-{{ $i }}"
                                                    value="{{ $detail->amount }}">
                                            </td>
                                            <td>
                                                <input type="text" readonly class="w-full border rounded bg-gray-100"
                                                    value="{{ optional($detail->account)->nama_akun }}">
                                                <input type="hidden" name="items[{{ $i }}][account_id]"
                                                    value="{{ $detail->account_id }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="remove-row text-red-500 font-bold"
                                                    data-index="{{ $i }}">×</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td class="pr-3 text-right font-semibold">Subtotal :</td>
                                        <td><input type="text" id="subtotal" readonly
                                                class="w-32 border rounded text-right px-2 py-1 bg-gray-100"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td class="pr-3 text-right font-semibold">Total Tax :</td>
                                        <td><input type="text" id="grand-tax" readonly
                                                class="w-32 border rounded text-right px-2 py-1 bg-gray-100"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td class="pr-3 text-right font-semibold">Freight :</td>
                                        <td>
                                            <input type="hidden" id="freight" name="freight"
                                                value="{{ $purchaseOrder->freight ?? 0 }}">
                                            <input type="text" id="freight-display"
                                                class="w-32 border rounded text-right px-2 py-1 bg-gray-100"
                                                value="{{ number_format($purchaseOrder->freight ?? 0, 0, '.', ',') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td class="pr-3 text-right font-semibold">Grand Total :</td>
                                        <td><input type="text" id="grand-total" readonly
                                                class="w-32 border rounded text-right px-2 py-1 bg-gray-100 font-bold">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Info Tambahan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50"
                                value="{{ $purchaseOrder->early_payment_terms }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Messages</label>
                            <textarea name="messages" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">{{ $purchaseOrder->messages }}</textarea>
                        </div>
                    </div>

                    <!-- Tombol -->
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#vendor_id').select2({
                placeholder: "-- Vendor --",
                ajax: {
                    url: '{{ route('vendors.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        }; // query keyword
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(vendor) {
                                return {
                                    id: vendor.id,
                                    text: vendor.nama_vendors
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
        document.addEventListener("DOMContentLoaded", function() {
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
                        const oldVal = "{{ old('account_id', $purchaseOrder->account_id ?? '') }}";
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

            window.calculateBackOrder = function(index) {
                const qty = parseFloat($(`.qty-${index}`).val()) || 0;
                const order = parseFloat($(`.order-${index}`).val()) || 0;
                $(`.back-${index}`).val(qty - order);
            }

            window.calculateAmount = function(index) {
                const order = parseFloat($(`.order-${index}`).val()) || 0;
                const price = parseFloat($(`.price-hidden-${index}`).val()) || 0;
                const taxPercent = parseFloat($(`.tax-${index} option:selected`).data('rate')) || 0;

                const baseAmount = order * price;
                const taxAmount = baseAmount * (taxPercent / 100);

                $(`.amount-display-${index}`).val(formatNumber(baseAmount));
                $(`.tax_amount-display-${index}`).val(formatNumber(taxAmount));

                $(`.amount-hidden-${index}`).val(baseAmount);
                $(`.tax_amount-hidden-${index}`).val(taxAmount);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0,
                    totalTax = 0;

                $('.amount-hidden').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                $('.tax_amount-hidden').each(function() {
                    totalTax += parseFloat($(this).val()) || 0;
                });

                const freight = parseFloat($('#freight').val()) || 0;
                const grandTotal = subtotal + totalTax + freight;

                $('#subtotal').val(formatNumber(subtotal));
                $('#grand-tax').val(formatNumber(totalTax));
                $('#grand-total').val(formatNumber(grandTotal));
                $('#freight-display').val(formatNumber(freight));
            }

            // Delegation: realtime update
            $(document).on('input',
                'input[name^="items"][name$="[quantity]"], input[name^="items"][name$="[order]"]',
                function() {
                    const index = $(this).closest('tr').index();
                    calculateBackOrder(index);
                    calculateAmount(index);
                });

            $(document).on('input', 'input[name^="items"][name$="[price]"]', function() {
                const index = $(this).closest('tr').index();
                calculateAmount(index);
            });

            $(document).on('change', 'select[name^="items"][name$="[tax_id]"]', function() {
                const index = $(this).closest('tr').index();
                calculateAmount(index);
            });

            $('#freight-display').on('input', function() {
                const val = this.value.replace(/,/g, '');
                const num = parseFloat(val) || 0;
                $('#freight').val(num);
                calculateTotals();
            });

            // Initial load
            @foreach ($purchaseOrder->details as $i => $detail)
                calculateAmount({{ $i }});
            @endforeach
            calculateTotals();
        });
    </script>
@endsection

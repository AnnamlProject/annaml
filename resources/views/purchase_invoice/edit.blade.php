@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl rounded-xl">
                <form method="POST" action="{{ route('purchase_invoice.update', $purchaseInvoice->id) }}">
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
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseInvoice->invoice_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Payment Method</label>
                            <select name="jenis_pembayaran_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">
                                <option value="">-- Pilih --</option>
                                @foreach ($jenis_pembayaran as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ $purchaseInvoice->jenis_pembayaran_id == $jenis->id ? 'selected' : '' }}>
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
                                        {{ $purchaseInvoice->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->nama_customers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Shipping Address</label>
                            <textarea name="shipping_address" rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">{{ $purchaseInvoice->shipping_address }}</textarea>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Order Number</label>
                            <input type="hidden" name="purchase_order_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseInvoice->purchase_order_id }}" required>
                            <input type="text" class=""
                                value="{{ $purchaseInvoice->purchaseOrder->order_number }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Invoice Date</label>
                            <input type="date" name="date_invoice"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseInvoice->date_invoice }}" required>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Shipping Date</label>
                            <input type="date" name="shipping_date"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseInvoice->shipping_date }}" required>
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
                                        <th>Project</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body">
                                    @foreach ($purchaseInvoice->details as $i => $detail)
                                        <tr class="bg-white even:bg-gray-50 border-b">
                                            <td><input type="hidden" name="items[{{ $i }}][item_id]"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ $detail->item_id }}">
                                                <input type="text"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ $detail->item->item_name }}">
                                            </td>
                                            <td><input type="number" name="items[{{ $i }}][quantity]"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ $detail->quantity }}"></td>
                                            <td><input type="number" name="items[{{ $i }}][quantity]"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ $detail->quantity }}"></td>
                                            <td><input type="number" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][back_order]"
                                                    value="{{ $detail->back_order }}"></td>
                                            <td><input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][unit]" value="{{ $detail->unit }}">
                                            </td>
                                            <td><input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][item_description]"
                                                    value="{{ $detail->item_description }}"></td>
                                            <td><input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][price]" value="{{ $detail->price }}">
                                            </td>
                                            <td><input type="number"
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][tax]" value="{{ $detail->tax }}">
                                            </td>
                                            <td><input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][tax_amount]"
                                                    value="{{ $detail->tax_amount }}"></td>
                                            <td><input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    name="items[{{ $i }}][amount]"
                                                    value="{{ $detail->amount }}"></td>
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ optional($detail->account)->nama_akun }}">
                                                <input type="hidden" name="items[{{ $i }}][account_id]"
                                                    value="{{ $detail->account_id }}">
                                            </td>
                                            <td>
                                                <input type="text" readonly
                                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                                    value="{{ optional($detail->project)->nama_project ?? 'Tidak Ada' }}">
                                                <input type="hidden" name="items[{{ $i }}][project_id]"
                                                    value="{{ $detail->project_id }} ">
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
                                value="{{ $purchaseInvoice->freight }}">
                        </div>
                        <div>
                            <label class="font-medium text-gray-700 block mb-1">Early Payment Terms</label>
                            <input type="text" name="early_payment_terms"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s"
                                value="{{ $purchaseInvoice->early_payment_terms }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="font-medium text-gray-700 block mb-1">Messages</label>
                            <textarea name="messages" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500s">{{ $purchaseInvoice->messages }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex justify-start space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            💾 Update
                        </button>
                        <a href="{{ route('purchase_invoice.index') }}"
                            class="px-6 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition">
                            ❌ Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

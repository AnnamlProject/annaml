@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="container mx-auto py-6">
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-2xl font-semibold mb-6">Purchase Order Details</h2>

                    <!-- Informasi Utama Sales Order -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <strong>Order Number:</strong>
                            <p>{{ $purchaseOrder->order_number }}</p>
                        </div>
                        <div>
                            <strong>Date Order:</strong>
                            <p>{{ \Carbon\Carbon::parse($purchaseOrder->date_order)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <strong>Shipping Date:</strong>
                            <p>{{ \Carbon\Carbon::parse($purchaseOrder->shipping_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <strong>Customer:</strong>
                            <p>{{ $purchaseOrder->customer->nama_customers ?? '-' }}</p>
                        </div>

                        <div>
                            <strong>Payment Method:</strong>
                            <p>{{ $purchaseOrder->jenisPembayaran->nama_jenis ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <strong>Shipping Address:</strong>
                            <p>{{ $purchaseOrder->shipping_address }}</p>
                        </div>
                        <div>
                            <strong>Freight:</strong>
                            <p>{{ number_format($purchaseOrder->freight, 2) }}</p>
                        </div>
                        <div>
                            <strong>Early Payment Terms:</strong>
                            <p>{{ $purchaseOrder->early_payment_terms }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <strong>Messages:</strong>
                            <p>{{ $purchaseOrder->messages }}</p>
                        </div>
                    </div>

                    <!-- Detail Items -->
                    <h3 class="text-xl font-semibold mb-2">Order Items</h3>
                    <div class="overflow-auto">
                        <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="border px-3 py-2">Item</th>
                                    <th class="border px-3 py-2">Qty</th>
                                    <th class="border px-3 py-2">Order</th>
                                    <th class="border px-3 py-2">Back Order</th>
                                    <th class="border px-3 py-2">Unit</th>
                                    <th class="border px-3 py-2">Description</th>
                                    <th class="border px-3 py-2">Base Price</th>
                                    <th class="border px-3 py-2">Discount</th>
                                    <th class="border px-3 py-2">Price</th>
                                    <th class="border px-3 py-2">Amount</th>
                                    <th class="border px-3 py-2">Tax</th>
                                    <th class="border px-3 py-2">Account</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder->details as $item)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $item->item->item_name ?? '-' }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->quantity }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->order }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->back_order }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->unit }}</td>
                                        <td class="border px-3 py-2">{{ $item->item_description }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->base_price, 2) }}
                                        </td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->discount, 2) }}
                                        </td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->price, 2) }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->amount, 2) }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->tax, 2) }}%</td>
                                        <td class="border px-3 py-2">{{ $item->account->nama_akun ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                        <a href="{{ route('purchase_order.edit', $purchaseOrder->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="{{ route('purchase_order.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

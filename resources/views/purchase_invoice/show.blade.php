@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="container mx-auto py-6">
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-2xl font-semibold mb-6">Sales Invoice Details</h2>

                    <!-- Informasi Utama Sales Order -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <strong>Invoice Number:</strong>
                            <p>{{ $purchaseInvoice->invoice_number }}</p>
                        </div>
                        <div>
                            <strong>Invoice Date:</strong>
                            <p>{{ \Carbon\Carbon::parse($purchaseInvoice->date_invoice)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <strong>Shipping Date:</strong>
                            <p>{{ \Carbon\Carbon::parse($purchaseInvoice->shipping_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <strong>Customer:</strong>
                            <p>{{ $purchaseInvoice->customer->nama_customers ?? '-' }}</p>
                        </div>
                        <div>
                            <strong>Payment Method:</strong>
                            <p>{{ $purchaseInvoice->jenisPembayaran->nama_jenis ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <strong>Shipping Address:</strong>
                            <p>{{ $purchaseInvoice->shipping_address }}</p>
                        </div>
                        <div>
                            <strong>Freight:</strong>
                            <p>{{ number_format($purchaseInvoice->freight, 2) }}</p>
                        </div>
                        <div>
                            <strong>Early Payment Terms:</strong>
                            <p>{{ $purchaseInvoice->early_payment_terms }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <strong>Messages:</strong>
                            <p>{{ $purchaseInvoice->messages }}</p>
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
                                    <th class="border px-3 py-2">Price</th>
                                    <th class="border px-3 py-2">Tax</th>
                                    <th class="border px-3 py-2">Tax Amount</th>
                                    <th class="border px-3 py-2">Amount</th>
                                    <th class="border px-3 py-2">Account</th>
                                    <th class="border px-3 py-2">Project</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseInvoice->details as $item)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $item->item->item_name ?? '-' }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->quantity }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->order }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->back_order }}</td>
                                        <td class="border px-3 py-2 text-center">{{ $item->unit }}</td>
                                        <td class="border px-3 py-2">{{ $item->item_description }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->price) }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->tax) }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->tax_amount) }}</td>
                                        <td class="border px-3 py-2 text-right">{{ number_format($item->amount) }}</td>
                                        <td class="border px-3 py-2">{{ $item->account->nama_akun ?? '-' }}</td>
                                        <td class="border px-3 py-2">{{ $item->project->nama_project ?? 'Tidak Ada' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
                        <a href="{{ route('purchase_invoice.edit', $purchaseInvoice->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="{{ route('purchase_invoice.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

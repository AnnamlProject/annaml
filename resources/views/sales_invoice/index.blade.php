@extends('layouts.app')
@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                <!-- Sticky Card Header -->
                @php
                    $themeColor = \App\Setting::get('theme_color', '#4F46E5');
                @endphp

                <div class="sticky top-0 z-20 px-6 py-5 border-b border-gray-100 flex justify-between items-center"
                    style="background: {{ $themeColor }};">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3 text-white text-xl"></i>
                        Sales Invoice
                    </h3>
                    <a href="{{ route('sales_invoice.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition-all">
                        <i class="fas fa-plus mr-2"></i> Add Sales Invoice
                    </a>
                </div>
            </div>

            <div class="relative overflow-x-auto" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Invoice Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Invoice Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Shipping Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Shipping Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sales Person</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Freight</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Early Payment Terms</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Messages</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($data as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $item->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $item->invoice_date }}</td>
                                <td class="px-6 py-4">{{ $item->shipping_date }}</td>
                                <td class="px-6 py-4">{{ $item->customer->nama_customers ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->jenisPembayaran->nama_jenis }}</td>
                                <td class="px-6 py-4">{{ $item->shipping_address }}</td>
                                <td class="px-6 py-4">{{ $item->salesPerson->nama_karyawan ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->freight }}</td>
                                <td class="px-6 py-4">{{ $item->early_payment_terms }}</td>
                                <td class="px-6 py-4">{{ $item->messages }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('sales_invoice.show', $item->id) }}"
                                            class="text-blue-500 hover:text-blue-700 p-2 rounded-full hover:bg-blue-50 transition-colors"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales_invoice.edit', $item->id) }}"
                                            class="text-yellow-500 hover:text-yellow-700 p-2 rounded-full hover:bg-yellow-50 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('sales_invoice.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">Belum ada Sales Invoice</p>
                                        <a href="{{ route('sales_invoice.create') }}"
                                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                            <i class="fas fa-plus mr-2"></i> Tambah
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    </div>

    <style>
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.9);
        }

        table thead {
            position: sticky;
            top: 68px;
            z-index: 10;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        tr {
            transition: background-color 0.2s ease;
        }
    </style>
@endsection

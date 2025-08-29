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
                        Inventory & Service
                    </h3>
                    <a href="{{ route('inventory.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition-all">
                        <i class="fas fa-plus mr-2"></i> Add Inventory & Service
                    </a>
                </div>

                <div class="bg-white shadow-md rounded overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-center text-sm font-medium">Item Number</th>
                                <th class="px-6 py-3 text-center text-sm font-medium"> Item Description</th>
                                <th class="px-6 py-3 text-center text-sm font-medium">Type</th>
                                <th class="px-6 py-3 text-center text-sm font-medium">Description</th>
                                <th class="px-6 py-3 text-center text-sm font-medium">Picture Path</th>
                                <th class="px-6 py-3 text-center text-sm font-medium">Thumbnail Path</th>
                                <th class="px-6 py-3 text-right text-sm font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $item->item_number }}</td>
                                    <td class="px-2 py-1 text-center">{{ $item->item_description ?? '-' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $item->type }}</td>
                                    <td class="px-2 py-1 text-center">{{ $item->description }}</td>
                                    <td class="px-2 py-1 text-center">{{ $item->picture_path }}</td>
                                    <td class="px-2 py-1 text-center">{{ $item->thumbnail_path }}</td>

                                    <td class="px-2 py-1 text-right space-x-2">
                                        <a href="{{ route('inventory.edit', $item->id) }}"
                                            class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data inventory.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="p-4 bg-white border-t">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

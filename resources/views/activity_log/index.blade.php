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
                        Log Activity
                    </h3>
                </div>
            </div>

            <!-- Filter Panel -->
            <div id="filterPanel" class="hidden px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex flex-wrap gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari..."
                            class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm w-64 shadow-sm" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-x-auto" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Activity</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User Agent</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($data as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-2 py-1 text-center text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-2 py-1 text-center">{{ $item->user->name }}</td>
                                <td class="px-2 py-1 text-center">{{ $item->activity ?? '-' }}</td>
                                <td class="px-2 py-1 text-center">{{ $item->ip_address ?? '-' }}</td>
                                <td class="px-2 py-1 text-center">{{ $item->user_agent ?? '-' }}</td>
                                <td class="px-2 py-1 text-center">{{ $item->created_at ?? '-' }}</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">Belum ada Activity Log</p>
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
    <!-- File Modal -->


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
    <script>
        document.getElementById('menu-button').addEventListener('click', function() {
            document.getElementById('dropdown-menu').classList.toggle('hidden');
        });

        window.addEventListener('click', function(e) {
            const button = document.getElementById('menu-button');
            const menu = document.getElementById('dropdown-menu');
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('tbody tr');

        function filterTable() {
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();

                const matchSearch = rowText.includes(searchValue);

                row.style.display = (matchSearch) ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
    </script>
@endsection

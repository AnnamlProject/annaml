@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div id="tabs" class="type-section">
                <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                    <li><a href="#select_item" class="tab-link active">Select Item</a></li>
                    <li><a href="#process_build" class="tab-link">Process Build</a></li>
                    <li><a href="#journal_report" class="tab-link">Journal Report</a></li>
                </ul>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST"
                    action="{{ isset($build_of_bom) ? route('build_of_bom.update', $build_of_bom->id) : route('build_of_bom.store') }}">
                    @csrf
                    @if (isset($build_of_bom))
                        @method('PUT')
                    @endif

                    <!-- Tab Content -->
                    <div id="select_item" class="tab-content">
                        <h2 class="text-lg font-semibold mb-4">Select Item</h2>
                        @include('build_of_bom.partials._select_item')
                    </div>

                    <div id="process_build" class="tab-content hidden">
                        <h2 class="text-lg font-semibold mb-4">Process Build</h2>
                        @include('build_of_bom.partials._select_item')
                    </div>

                    <div id="journal_report" class="tab-content hidden">
                        <h2 class="text-lg font-semibold mb-4">Journal Report</h2>
                        @include('build_of_bom.partials._select_item')
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            {{ isset($build_of_bom) ? 'Update' : 'Create' }} Build Of BOM
                        </button>
                        <a href="{{ route('build_of_bom.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Reset semua tab
                document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

                // Aktifkan tab yang diklik
                this.classList.add('active');
                const target = document.querySelector(this.getAttribute('href'));
                target.classList.remove('hidden');
            });
        });
    </script>
@endsection

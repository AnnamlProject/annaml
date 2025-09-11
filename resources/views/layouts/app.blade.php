<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('image/favicon/favicon.ico') }}">


    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Custom Styles --}}
    <style>
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05),
                0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        main::-webkit-scrollbar-thumb {
            background-color: purple;
        }

        main::-webkit-scrollbar-thumb:hover {
            background-color: purple;
        }

        main {
            scrollbar-color: #f3f4f6;
        }

        .btn-primary {
            background-color: blue;
            color: white;
        }

        .bg-primary {
            background-color: white;
        }
    </style>
</head>

<body class="m-0 p-0 font-sans antialiased bg-white text-gray-800 min-h-screen overflow-y-auto">

    {{-- Loading Overlay --}}
    {{-- <div id="loading-overlay"
        class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-md z-50 flex items-center justify-center hidden">
        <div class="flex flex-col items-center space-y-4 animate-pulse">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2
                         5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824
                         3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 font-medium">Mohon tunggu...</p>
        </div>
    </div> --}}

    {{-- Navbar --}}

    @include('components.navbar')


    {{-- Main Content --}}
    <main class="m-0 p-0">
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    {{-- Scripts --}}
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');

            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('transform');
            mobileMenu.classList.toggle('translate-x-full');
            backdrop.classList.toggle('hidden');

            document.body.classList.toggle('overflow-hidden', !mobileMenu.classList.contains('hidden'));
        }

        // Simulate loading
        document.addEventListener('DOMContentLoaded', () => {
            const loadingOverlay = document.getElementById('loading-overlay');
            loadingOverlay.classList.remove('hidden');
            setTimeout(() => {
                loadingOverlay.classList.add('hidden');
            }, 1000);
        });

        // Turbo Smooth Scroll (if using Turbo)
        document.addEventListener('turbo:render', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: `{!! session('error') !!}`,
                confirmButtonText: 'OK'
            });
        @endif
    </script>


    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
    {{-- Stack scripts --}}
    @stack('scripts')
</body>

</html>

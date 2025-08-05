@php
    use Carbon\Carbon;
    $hour = Carbon::now()->format('H');
    $user = Auth::user()->name;
    $secondary = \App\Setting::get('theme_secondary_color', '#4F46E5');

    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat pagi';
        $message = 'Semoga harimu menyenangkan dan produktif ya!';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat siang';
        $message = 'Jangan lupa makan siang dan istirahat sejenak.';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat sore';
        $message = 'Tetap semangat menyelesaikan pekerjaan hari ini!';
    } else {
        $greeting = 'Selamat malam';
        $message = 'Saatnya beristirahat, jaga kesehatan ya.';
    }
@endphp

@extends('layouts.app')

@section('content')
    <!-- Salam & Pesan -->
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-semibold mb-2">{{ $greeting }}, {{ $user }}!</h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ $message }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Statistik -->
    <div class="pb-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                <!-- Card: Total Customer -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex items-center">
                    <div class="icon-secondary rounded-full p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A4 4 0 017 16h10a4 4 0 011.879.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-300">Total Customer</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCustomers }}</div>
                    </div>
                </div>

                <!-- Card: Total Vendors -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex items-center">
                    <div class="icon-secondary rounded-full p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A4 4 0 017 16h10a4 4 0 011.879.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-300">Total Vendors</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalVendors }}</div>
                    </div>
                </div>

                <!-- Card: Total Account -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex items-center">
                    <div class="icon-secondary rounded-full p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A4 4 0 017 16h10a4 4 0 011.879.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-300">Total Account</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalAccount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tanggal -->
    <div class="pb-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Form Filter Tanggal -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                            Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                            Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 sm:text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="btn-secondary px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition">
                            Filter
                        </button>
                        @if (request()->has('start_date') || request()->has('end_date'))
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Chart Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Gender Chart -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow w-full">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 text-center">Perbandingan Gender
                    </h2>
                    <canvas id="genderChart" class="w-full h-64 mx-auto"></canvas>
                </div>
                <!-- Unit Kerja Chart -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow w-full">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 text-center">Perbandingan Unit
                        Kerja</h2>
                    <canvas id="unitKerjaChart" class="w-full h-64 mx-auto"></canvas>
                </div>
                <!-- Level Karyawan Chart -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow w-full">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 text-center">Perbandingan Level
                        Karyawan</h2>
                    <canvas id="levelKaryawanChart" class="w-full h-64 mx-auto"></canvas>
                </div>
            </div>
        </div>
    </div>



    <style>
        :root {
            --theme-secondary: {{ $secondary }};
        }

        .bg-secondary {
            background-color: var(--theme-secondary);
        }

        .text-secondary {
            color: var(--theme-secondary);
        }

        .btn-secondary {
            background-color: var(--theme-secondary);
            color: #fff;
        }

        .btn-secondary:hover {
            filter: brightness(0.9);
        }

        .icon-secondary {
            background-color: rgba(79, 70, 229, 0.1);
            /* fallback for light theme */
            color: var(--theme-secondary);
        }

        .dark .icon-secondary {
            background-color: var(--theme-secondary);
            color: #fff;
        }
    </style>




    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Chart: Gender
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            const genderChart = new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: @json([$jumlahLaki, $jumlahPerempuan]),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#333'
                            }
                        }
                    }
                }
            });

            // Chart: Unit Kerja
            const unitKerjaCtx = document.getElementById('unitKerjaChart').getContext('2d');
            const unitKerjaChart = new Chart(unitKerjaCtx, {
                type: 'bar',
                data: {
                    labels: @json($unitKerjaLabels),
                    datasets: [{
                        label: 'Jumlah per Unit Kerja',
                        data: @json($unitKerjaCounts),
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#333'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#333'
                            }
                        }
                    }
                }
            });
            // chart level karyawan
            const levelCtx = document.getElementById('levelKaryawanChart').getContext('2d');
            const levelChart = new Chart(levelCtx, {
                type: 'bar',
                data: {
                    labels: @json($levelKaryawanLabels),
                    datasets: [{
                        label: 'Jumlah per Level',
                        data: @json($levelKaryawanCounts),
                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed.y} orang`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection

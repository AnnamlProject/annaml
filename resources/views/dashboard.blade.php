@extends('layouts.app')

@section('content')
    @php
        use Carbon\Carbon;
        $hour = Carbon::now()->format('H');
        $user = Auth::user()->name;

        $userBg = \App\Setting::get('background', 'logo.jpg');
        $bgImage = 'storage/' . $userBg;

        if ($hour >= 5 && $hour < 11) {
            $greeting = "Selamat pagi, $user";
            $tagline = 'Awali hari dengan energi positif dan penuh semangat.';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = "Selamat siang, $user";
            $tagline = 'Tetap produktif di tengah hari yang cerah.';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = "Selamat sore, $user";
            $tagline = 'Waktunya menyelesaikan pekerjaan dengan tuntas.';
        } else {
            $greeting = "Selamat malam, $user";
            $tagline = 'Istirahat sejenak, persiapkan hari esok.';
        }
    @endphp

    <div class="relative w-full h-[85vh] flex items-center justify-center text-center text-white overflow-hidden">
        <!-- Background -->
        <img src="{{ asset($bgImage) }}" alt="Background"
            class="absolute inset-0 w-full h-full object-cover object-center animate-zoomBg will-change-transform" />

        <!-- Overlay gradasi -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-sky-500/80"></div>

        <!-- Partikel -->
        <div class="absolute inset-0 pointer-events-none">
            @for ($i = 0; $i < 30; $i++)
                <span class="absolute w-1 h-1 bg-white rounded-full opacity-70 animate-twinkle"
                    style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 5) }}s;"></span>
            @endfor
        </div>

        <!-- Konten -->
        <div class="relative px-6 max-w-2xl animate-textReveal z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 drop-shadow-xl">
                {{ $greeting }}
            </h1>
            <p class="text-lg md:text-xl mb-6 text-gray-200 drop-shadow-md">
                {{ $tagline }}
            </p>

        </div>
    </div>

    <style>
        @keyframes fadeSlide {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomBg {
            from {
                transform: scale(1);
            }

            to {
                transform: scale(1.1);
            }
        }

        @keyframes textReveal {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes glowButton {

            0%,
            100% {
                box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.9);
            }
        }

        .animate-fadeSlide {
            animation: fadeSlide 1s ease forwards;
        }

        .animate-zoomBg {
            animation: zoomBg 20s ease-in-out infinite alternate;
        }

        .animate-textReveal {
            animation: textReveal 1s ease-out;
        }

        .animate-twinkle {
            animation: twinkle 3s infinite ease-in-out;
        }

        .animate-glowButton {
            animation: glowButton 2s infinite ease-in-out;
        }
    </style>
@endsection

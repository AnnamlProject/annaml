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


    <div class="relative w-full h-screen flex items-start justify-end text-right text-white overflow-hidden p-0 pt-16"
        style="background-image: url('{{ asset($bgImage) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">


        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-sky-600/80"></div>

        <!-- Partikel -->
        <div class="absolute inset-0 pointer-events-none">
            @for ($i = 0; $i < 30; $i++)
                <span class="absolute w-1 h-1 bg-white rounded-full opacity-70 animate-twinkle"
                    style="
                    top: {{ rand(0, 100) }}%; 
                    left: {{ rand(0, 100) }}%; 
                    animation-delay: {{ rand(0, 5) }}s;
                "></span>
            @endfor
        </div>

        <!-- Konten -->
        <div class="relative max-w-xl z-10">
            <h1
                class="text-2xl md:text-2xl font-extrabold mb-4 drop-shadow-2xl 
            bg-clip-text text-transparent 
            bg-gradient-to-r from-sky-300 via-cyan-400 to-blue-500 
            animate-fadeInUp">
                {{ $greeting }}
            </h1>

            <p class="text-sm md:text-sm mb-6 text-gray-200 drop-shadow-md animate-fadeIn delay-300">
                <span class="px-2 py-1 rounded-lg backdrop-blur-sm">
                    {{ $tagline }}
                </span>
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

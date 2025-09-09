<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuangan</title>
    <link rel="shortcut icon" href="{{ asset('image/favicon_io/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    @php
        $themeColor = \App\Setting::get('theme_color', '#4F46E5');
        $secondaryColor = \App\Setting::get('theme_secondary_color', '#6366f1');
    @endphp

    <style>
        :root {
            --primary-color: {{ $themeColor }};
            --secondary-color: {{ $secondaryColor }};
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .bg-secondary {
            background-color: var(--secondary-color);
        }

        .text-secondary {
            color: var(--secondary-color);
        }

        .border-secondary {
            border-color: var(--secondary-color);
        }

        .hover\:bg-secondary:hover {
            background-color: var(--secondary-color);
        }

        .smooth-transition {
            transition: all 0.3s ease;
        }

        .error-shake {
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translateX(-1px);
            }

            20%,
            80% {
                transform: translateX(2px);
            }

            30%,
            50%,
            70% {
                transform: translateX(-3px);
            }

            40%,
            60% {
                transform: translateX(3px);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-white to-gray-100 min-h-screen flex items-center justify-center p-4 font-sans">

    <!-- Login Container -->
    <div class="w-full max-w-md z-10 animate__animated animate__fadeInUp">
        <div
            class="bg-white rounded-xl shadow-xl overflow-hidden smooth-transition hover:shadow-2xl hover:-translate-y-1">
            <!-- Header -->
            <div class="text-white text-center p-6 bg-primary">
                <div
                    class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-md mb-4 animate__bounceIn">
                    <img src="{{ asset('storage/' . \App\Setting::get('logo', 'default.png')) }}" alt="Logo"
                        class="h-12 w-12 transform hover:scale-110 smooth-transition">
                </div>
                <h4 class="text-xl font-bold mb-1 animate__fadeIn">Welcome Back!</h4>
                <p class="text-blue-100 animate__fadeIn animate__delay-1s">Please login to continue</p>
            </div>

            <!-- Body -->
            <div class="p-6">
                @if (session('status'))
                    <div id="statusMessage"
                        class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded-lg opacity-0">
                        <i class="bi bi-check-circle-fill mr-2"></i> {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div id="errorMessage"
                        class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg opacity-0">
                        <i class="bi bi-exclamation-triangle-fill mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div id="validationErrors"
                        class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg opacity-0">
                        @foreach ($errors->all() as $error)
                            <p><i class="bi bi-exclamation-circle-fill mr-2"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-secondary">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary smooth-transition placeholder-gray-400"
                            placeholder="Email">
                    </div>

                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-secondary">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary smooth-transition placeholder-gray-400"
                            placeholder="Password">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox"
                                class="h-4 w-4 text-secondary border-gray-300 rounded smooth-transition">
                            <label for="remember_me" class="ml-2 text-sm text-gray-700 smooth-transition">Remember
                                me</label>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-secondary hover:bg-opacity-90 text-white font-medium py-3 px-4 rounded-lg smooth-transition transform hover:-translate-y-0.5 shadow-md hover:shadow-lg flex items-center justify-center">
                        <i class="bi bi-box-arrow-in-right mr-2 smooth-transition"></i> Login
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-600 mt-6 text-sm animate__fadeIn animate__delay-1s">
            &copy; {{ date('Y') }} An Naml. All rights reserved.
        </p>
    </div>

    <script>
        // Smooth animations for error messages
        document.addEventListener('DOMContentLoaded', function() {
            // Animate error messages
            const errorMessage = document.getElementById('errorMessage');
            const validationErrors = document.getElementById('validationErrors');
            const statusMessage = document.getElementById('statusMessage');

            if (errorMessage) {
                errorMessage.classList.add('error-shake');
                setTimeout(() => {
                    errorMessage.classList.remove('opacity-0');
                    errorMessage.classList.add('opacity-100');
                }, 100);
            }

            if (validationErrors) {
                validationErrors.classList.add('error-shake');
                setTimeout(() => {
                    validationErrors.classList.remove('opacity-0');
                    validationErrors.classList.add('opacity-100');
                }, 100);
            }

            if (statusMessage) {
                setTimeout(() => {
                    statusMessage.classList.remove('opacity-0');
                    statusMessage.classList.add('opacity-100');
                }, 300);
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                if (!email || !password) {
                    e.preventDefault();
                    const errorDiv = document.createElement('div');
                    errorDiv.className =
                        'bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg error-shake';
                    errorDiv.innerHTML =
                        '<i class="bi bi-exclamation-triangle-fill mr-2"></i> Please fill in all fields';

                    const form = document.querySelector('form');
                    form.insertBefore(errorDiv, form.firstChild);

                    setTimeout(() => {
                        errorDiv.classList.add('opacity-100');
                    }, 100);
                }
            });
        });
    </script>
</body>

</html>

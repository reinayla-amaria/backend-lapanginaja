<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - LapanginAjaAa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-100 flex items-center justify-center min-h-screen p-4 md:p-8">

    <div
        class="bg-white rounded-[40px] shadow-2xl flex flex-col md:flex-row w-full max-w-6xl overflow-hidden min-h-[650px] relative">

        <div class="md:w-1/2 bg-white flex flex-col items-center justify-center p-12 relative z-10">

            <div class="absolute top-8 left-8 flex items-center gap-2">
                <div>
                    <h2 class="text-2xl font-bold text-blue-950 tracking-wider leading-none">LapanginAja</h2>
                    <p class="text-xs text-orange-500 font-bold tracking-widest mt-1 uppercase">Badminton Arena</p>
                </div>
            </div>

            <img src="https://img.icons8.com/color/256/badminton.png" alt="Badminton Sports"
                class="w-56 h-auto object-contain mt-10 drop-shadow-xl hover:scale-105 transition-transform duration-300">

            <p class="mt-12 text-center text-gray-500 font-medium px-8 text-sm max-w-sm leading-relaxed">
                Kelola lapangan badminton Anda dan pantau transaksi dengan mudah dalam satu aplikasi digital.
            </p>

            <div class="absolute -top-2 -bottom-2 left-full w-24 hidden md:block pointer-events-none -ml-2 text-white">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,0 C100,30 0,70 100,100 L0,100 L0,0 Z"></path>
                </svg>
            </div>
        </div>

        <div
            class="md:w-1/2 p-12 flex flex-col justify-center relative bg-gradient-to-br from-blue-950 via-blue-900 to-blue-950 z-0">
            <div class="max-w-md w-full mx-auto md:pl-12">
                <h2 class="text-4xl font-bold text-white mb-2">Login</h2>
                <p class="text-blue-200 mb-10 text-sm">Masuk untuk mengelola arena Anda.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-blue-200 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all shadow-inner"
                            placeholder="Masukkan Email">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                    </div>

                    <div class="mb-6 relative">
                        <label for="password" class="block text-sm font-medium text-blue-200 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all shadow-inner"
                                placeholder="Masukkan Password">

                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 px-4 flex items-center text-blue-400 hover:text-orange-400 transition-colors focus:outline-none">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
                    </div>

                    <div class="flex items-center justify-between mb-8">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-blue-800 bg-white/5 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-blue-950"
                                name="remember">
                            <span class="ms-2 text-sm text-blue-200">Ingat Saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-300 hover:text-orange-400 transition-colors"
                                href="{{ route('password.request') }}">Lupa Password?</a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-lg shadow-orange-500/30">
                        Masuk Sekarang
                    </button>
                </form>

                <p class="mt-8 text-center text-blue-300 text-sm">
                    Belum punya akun GOR?
                    <a href="{{ route('register') }}"
                        class="text-orange-400 hover:text-orange-300 font-bold ml-1 transition-colors">Daftar Mitra</a>
                </p>
            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Mitra - LapanginAja</title>
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
        class="bg-white rounded-[40px] shadow-2xl flex flex-col md:flex-row-reverse w-full max-w-6xl overflow-hidden min-h-[700px] relative">

        <div class="md:w-1/2 bg-white flex flex-col items-center justify-center p-12 relative z-10">
            <div class="absolute top-8 right-8 text-right">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LapanginAja" class="h-10 w-auto ml-auto">
                <p class="text-xs text-orange-500 font-bold tracking-widest mt-1 uppercase">Mitra Partner</p>
            </div>

            <img src="https://img.icons8.com/color/256/badminton-court.png" alt="Badminton Arena"
                class="w-64 h-auto object-contain mt-10 drop-shadow-xl hover:scale-105 transition-transform duration-300">

            <p class="mt-12 text-center text-gray-500 font-medium px-8 text-sm max-w-sm leading-relaxed">
                Bergabunglah sebagai Mitra dan mulailah kelola arena badminton Anda secara profesional.
            </p>

            <div class="absolute -top-2 -bottom-2 right-full w-24 hidden md:block pointer-events-none -mr-2 text-white">
                <svg class="h-full w-full rotate-180" viewBox="0 0 100 100" preserveAspectRatio="none"
                    fill="currentColor">
                    <path d="M0,0 C100,30 0,70 100,100 L0,100 L0,0 Z"></path>
                </svg>
            </div>
        </div>

        <div
            class="md:w-1/2 p-12 flex flex-col justify-center relative bg-gradient-to-br from-blue-950 via-blue-900 to-blue-950 z-0">
            <div class="max-w-md w-full mx-auto md:pr-12">
                <h2 class="text-4xl font-bold text-white mb-2">Register</h2>
                <p class="text-blue-200 mb-8 text-sm">Buat akun pengelola arena baru Anda.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-blue-200 mb-1">Nama Lengkap
                            Owner</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus
                            autocomplete="name"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner"
                            placeholder="Masukkan nama">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-blue-200 mb-1">Email Mitra</label>
                        <input id="email" type="email" name="email" :value="old('email')" required
                            autocomplete="username"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner"
                            placeholder="Masukkan Email">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-4 relative">
                        <label for="password" class="block text-sm font-medium text-blue-200 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner"
                                placeholder="Masukkan Password">
                            <button type="button" onclick="togglePassword('password', 'eye-1', 'eye-slash-1')"
                                class="absolute inset-y-0 right-0 px-4 flex items-center text-blue-400 hover:text-orange-400 transition-colors">
                                <svg id="eye-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eye-slash-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-8 relative">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-blue-200 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="w-full px-4 py-2.5 pr-12 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner"
                                placeholder="Masukkan Password Kembali">
                            <button type="button"
                                onclick="togglePassword('password_confirmation', 'eye-2', 'eye-slash-2')"
                                class="absolute inset-y-0 right-0 px-4 flex items-center text-blue-400 hover:text-orange-400 transition-colors">
                                <svg id="eye-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eye-slash-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-lg shadow-orange-500/30">
                        Daftar Akun Mitra
                    </button>
                </form>

                <p class="mt-6 text-center text-blue-300 text-sm">
                    Sudah punya akun Mitra?
                    <a href="{{ route('login') }}"
                        class="text-orange-400 hover:text-orange-300 font-bold ml-1 transition-colors">Login di sini</a>
                </p>
            </div>
        </div>

    </div>

    <script>
        function togglePassword(inputId, eyeId, slashId) {
            const pwdInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeId);
            const eyeSlashIcon = document.getElementById(slashId);

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
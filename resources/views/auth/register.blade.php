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
        class="bg-white rounded-[40px] shadow-2xl flex flex-col md:flex-row-reverse w-full max-w-6xl overflow-hidden min-h-[650px] relative">

        <div class="md:w-1/2 bg-white flex flex-col items-center justify-center p-12 relative overflow-hidden z-10">
            <div class="absolute top-8 right-8 text-right">
                <h2 class="text-2xl font-bold text-blue-950 tracking-wider leading-none">LapanginAja</h2>
                <p class="text-xs text-orange-500 font-bold tracking-widest mt-1 uppercase">Mitra Partner</p>
            </div>

            <img src="https://cdni.iconscout.com/illustration/premium/thumb/man-joining-the-badminton-club-7496735-6126601.png?f=webp"
                alt="Mitra Illustration" class="w-80 h-auto object-contain mt-10">

            <p class="mt-8 text-center text-gray-500 font-medium px-8 text-sm max-w-sm">
                Daftarkan arena Anda dan tingkatkan penyewaan ke level digital bersama LapanginAja.
            </p>

            <div class="absolute -left-1 inset-y-0 h-full w-24 hidden md:block">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-full w-full">
                    <path d="M100 0 L0 0 C0 0 20 20 20 50 C20 80 0 100 0 100 L100 100 Z" class="fill-blue-950"></path>
                </svg>
            </div>
        </div>

        <div
            class="md:w-1/2 p-12 flex flex-col justify-center relative bg-gradient-to-br from-blue-950 via-blue-900 to-blue-950">
            <div class="max-w-md w-full mx-auto">
                <h2 class="text-4xl font-bold text-white mb-2">Register Mitra</h2>
                <p class="text-blue-200 mb-8 text-sm">Buat akun pengelola arena baru Anda.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-blue-200 mb-1">Nama Lengkap
                            Owner</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus
                            autocomplete="name"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-blue-200 mb-1">Email Mitra</label>
                        <input id="email" type="email" name="email" :value="old('email')" required
                            autocomplete="username"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-blue-200 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-400 text-sm" />
                    </div>

                    <div class="mb-8">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-blue-200 mb-1">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-blue-800 text-white placeholder-blue-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <x-input-error :messages="$errors->get('password_confirmation')"
                            class="mt-1 text-red-400 text-sm" />
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-lg shadow-orange-500/30">
                        Daftar Akun Mitra
                    </button>
                </form>

                <p class="mt-6 text-center text-blue-300 text-sm">
                    Sudah punya akun Mitra?
                    <a href="{{ route('login') }}"
                        class="text-orange-400 hover:text-orange-300 font-bold ml-1 transition-colors">Login GOR</a>
                </p>
            </div>
        </div>

    </div>

</body>

</html>
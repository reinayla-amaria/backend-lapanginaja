<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lapingin.Aja') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 flex h-screen overflow-hidden">

    <aside class="w-64 bg-blue-800 text-white flex flex-col justify-between shadow-xl z-20">
        <div>
            <div class="p-6 text-center border-b border-blue-700">
                <h2 class="text-2xl font-bold tracking-wider">LapanginAja</h2>
                <p class="text-sm text-blue-300 mt-1">Badminton Arena</p>
            </div>

            <nav class="mt-6 flex flex-col gap-2 px-4">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-orange-500 font-bold' : 'hover:bg-blue-700' }}">
                    Dashboard
                </a>

                {{-- ================= MENU KHUSUS SUPER ADMIN ================= --}}
                @if(Auth::user()->role === 'admin')
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-blue-700">
                        Kelola Mitra
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-blue-700">
                        Pantau Transaksi
                    </a>
                @endif

                {{-- ================= MENU KHUSUS MITRA ================= --}}
                @if(Auth::user()->role === 'mitra')
                    <a href="{{ route('lapangan.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('lapangan.*') ? 'bg-orange-500 font-bold' : 'hover:bg-blue-700' }}">
                        Kelola Arena
                    </a>

                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-blue-700">
                        Jadwal Lapangan
                    </a>

                    <a href="{{ route('transaksi.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-blue-700">
                        Verifikasi Pesanan
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-4 border-t border-blue-700 bg-blue-900">
            <div class="mb-3">
                <p class="font-bold text-sm truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-blue-300 truncate">{{ Auth::user()->email }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left text-sm text-red-400 hover:text-red-300 font-bold py-2 flex items-center gap-2">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">

        <header class="bg-white shadow z-10">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">

                <div>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>

                <div class="text-right">
                    <span class="text-2xl font-bold text-orange-500">Hello,
                        {{ explode(' ', Auth::user()->name)[0] }}!</span>
                </div>
            </div>
        </header>

        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

</body>

</html>
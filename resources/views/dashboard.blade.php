<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight ml-4">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            {{-- 1. KOTAK UCAPAN SELAMAT DATANG (HELLO) --}}
            <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg mb-8">
                <div class="p-8 flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm md:text-base mb-1">Welcome back,</p>
                        <h3 class="text-orange-500 font-bold text-3xl">Hello, {{ Auth::user()->name }}! 👋</h3>
                    </div>
                    <div>
                        <span
                            class="bg-blue-600 text-white text-xs px-3 py-1 rounded-full uppercase tracking-wider font-bold shadow">
                            Akses: {{ Auth::user()->role }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 2. KONTEN KHUSUS SUPER ADMIN --}}
            @if(Auth::user()->role === 'admin')

                {{-- Tiga Kotak Statistik Admin --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Total Mitra</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $total_mitra }} <span
                                class="text-sm font-normal text-gray-400">Arena</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Total Penyewa</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $total_penyewa }} <span
                                class="text-sm font-normal text-gray-400">User</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Total Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $total_transaksi }} <span
                                class="text-sm font-normal text-gray-400">Sukses</span></p>
                    </div>
                </div>

                {{-- Tabel Riwayat Mitra --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Pendaftaran Mitra Baru</h3>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Nama Mitra</th>
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Email</th>
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Tanggal Gabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mitra_baru as $mitra)
                                    <tr>
                                        <td class="border-b py-3 px-4 font-medium">{{ $mitra->name }}</td>
                                        <td class="border-b py-3 px-4 text-blue-500">{{ $mitra->email }}</td>
                                        <td class="border-b py-3 px-4">{{ $mitra->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-8 text-gray-500">Belum ada mitra yang mendaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. KONTEN KHUSUS MITRA --}}
            @elseif(Auth::user()->role === 'mitra')

                {{-- Tiga Kotak Statistik Mitra --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Lapangan Aktif</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $lapangan_aktif }} <span
                                class="text-sm font-normal text-gray-400">Unit</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Pesanan Pending</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pesanan_pending }} <span
                                class="text-sm font-normal text-gray-400">Menunggu Bayar</span></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                        <p class="text-gray-500 text-sm font-bold uppercase">Pesanan Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pesanan_hari_ini }} <span
                                class="text-sm font-normal text-gray-400">Jadwal</span></p>
                    </div>
                </div>

                {{-- Tabel Riwayat Booking --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Pesanan Terbaru</h3>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Penyewa</th>
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Lapangan</th>
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Jadwal</th>
                                    <th class="border-b py-3 px-4 text-sm font-bold text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat_pesanan as $pesanan)
                                    <tr>
                                        <td class="border-b py-3 px-4">{{ $pesanan->user->name ?? 'User' }}</td>
                                        <td class="border-b py-3 px-4">{{ $pesanan->lapangan->nama_lapangan ?? 'Lapangan' }}</td><td class="border-b py-3 px-4">
                                            {{ \Carbon\Carbon::parse($pesanan->tanggal_main)->format('d M Y') }}<br>
                                            <span class="text-xs text-gray-500">{{ $pesanan->jam_mulai }} -
                                                {{ $pesanan->jam_selesai }}</span>
                                        </td>
                                        <td class="border-b py-3 px-4">
                                            @if($pesanan->status == 'pending')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pending</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Sukses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-500">Belum ada pesanan masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
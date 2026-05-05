<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Pesanan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">Pantau Transaksi</h2>
                <p class="text-base text-gray-500 mt-2">Daftar seluruh transaksi penyewaan LapanginAja.</p>
            </div>
            <a href="{{ route('transaksi.export') }}"
                class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-base font-bold transition-all shadow-sm">
                Unduh Laporan (CSV)
            </a>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">ID Pesanan
                            </th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Penyewa</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Lapangan &
                                Jadwal</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Total Bayar
                            </th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider text-center">
                                Status</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-5 px-6">
                                    <p class="text-base font-bold text-gray-800">#{{ $booking->id }}</p>
                                </td>
                                <td class="py-5 px-6">
                                    <p class="text-base font-bold text-gray-800">{{ $booking->user->name ?? 'Penyewa' }}</p>
                                </td>
                                <td class="py-5 px-6">
                                    <p class="text-base font-bold text-gray-800">{{ $booking->lapangan->nama_lapangan }}</p>
                                    <p class="text-sm text-blue-600 font-semibold">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_main)->format('d M Y') }} |
                                        {{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}
                                    </p>
                                </td>
                                <td class="py-5 px-6">
                                    <p class="text-base font-bold text-gray-800">
                                        {{-- Asumsi lu ada kolom total_harga di tabel bookings --}}
                                        Rp {{ number_format($booking->total_harga ?? 0, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    @if($booking->status == 'pending')
                                        <span
                                            class="inline-flex items-center justify-center bg-yellow-50 text-yellow-700 text-sm font-bold px-4 py-1.5 rounded-full border border-yellow-100">
                                            Pending
                                        </span>
                                    @elseif($booking->status == 'dibayar' || $booking->status == 'sukses')
                                        <span
                                            class="inline-flex items-center justify-center bg-green-50 text-green-700 text-sm font-bold px-4 py-1.5 rounded-full border border-green-100">
                                            Lunas
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center bg-red-50 text-red-700 text-sm font-bold px-4 py-1.5 rounded-full border border-red-100">
                                            Batal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <button
                                        class="text-blue-600 hover:text-blue-800 transition font-bold text-base">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-lg text-gray-500">
                                    Belum ada pesanan yang masuk nih bang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
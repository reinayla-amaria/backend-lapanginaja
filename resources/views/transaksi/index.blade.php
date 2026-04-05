<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Pesanan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b py-2">ID Pesanan</th>
                                <th class="border-b py-2">Penyewa</th>
                                <th class="border-b py-2">Lapangan</th>
                                <th class="border-b py-2">Jadwal Main</th>
                                <th class="border-b py-2">Status Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $booking)
                                <tr>
                                    <td class="border-b py-3 font-medium text-gray-700">#{{ $booking->id }}</td>
                                    <td class="border-b py-3">{{ $booking->user->name ?? 'Penyewa' }}</td>
                                    <td class="border-b py-3">{{ $booking->lapangan->nama_lapangan }}</td>
                                    <td class="border-b py-3">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_main)->format('d M Y') }} <br>
                                        <span class="text-xs text-gray-500">{{ $booking->jam_mulai }} -
                                            {{ $booking->jam_selesai }}</span>
                                    </td>
                                    <td class="border-b py-3">
                                        {{-- Logika warna status --}}
                                        @if($booking->status == 'pending')
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">Pending</span>
                                        @elseif($booking->status == 'dibayar' || $booking->status == 'sukses')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">Lunas</span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">Batal</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-500 font-medium">
                                        Belum ada pesanan yang masuk nih bang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
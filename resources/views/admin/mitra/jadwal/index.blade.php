<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800">Jadwal Lapangan Hari Ini</h2>
            <p class="text-base text-gray-500 mt-2">Pantau jadwal booking yang masuk khusus untuk GOR milik lu.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($lapangans as $lapangan)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-900 px-6 py-4">
                        <h3 class="text-lg font-bold text-white">{{ $lapangan->nama_lapangan }}</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($lapangan->bookings as $booking)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $booking->user->name ?? 'Penyewa' }}</p>
                                    <p class="text-xs text-blue-600 font-semibold">{{ $booking->jam_mulai }} - {{ $booking->jam_selesai }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $booking->status == 'sukses' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ strtoupper($booking->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-400 py-4">Belum ada jadwal hari ini.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-gray-500">
                    Belum ada data lapangan yang terdaftar nih.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
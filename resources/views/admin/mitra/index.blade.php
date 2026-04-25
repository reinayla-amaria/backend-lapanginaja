<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-800">Kelola Mitra Arena</h2>
                <p class="text-base text-gray-500 mt-2">Daftar seluruh pemilik GOR yang terdaftar di LapanginAja.</p>
            </div>
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-base font-bold transition-all shadow-sm">
                + Tambah Mitra
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Nama Mitra /
                                GOR</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Email Akun
                            </th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider text-center">
                                Total Lapangan</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider">Tanggal
                                Gabung</th>
                            <th class="py-5 px-6 text-sm font-bold text-gray-600 uppercase tracking-wider text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mitras as $mitra)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-5 px-6">
                                    <p class="text-lg font-bold text-gray-800">{{ $mitra->name }}</p>
                                </td>
                                <td class="py-5 px-6">
                                    <p class="text-base text-gray-600">{{ $mitra->email }}</p>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <span
                                        class="inline-flex items-center justify-center bg-blue-50 text-blue-700 text-sm font-bold px-4 py-1.5 rounded-full border border-blue-100">
                                        {{ $mitra->lapangans_count }} Arena
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-base text-gray-600">
                                    {{ $mitra->created_at->diffForHumans() }}
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <button
                                            class="text-blue-600 hover:text-blue-800 transition font-bold text-base">Detail</button>
                                        <button
                                            class="text-red-500 hover:text-red-700 transition font-bold text-base">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-lg text-gray-500">
                                    Belum ada mitra yang terdaftar nih bang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto bg-gray-50 min-h-screen">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Jadwal</h2>

            <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">Nama Lapangan</h3>

                <form action="{{ route('mitra.jadwal') }}" method="GET"
                    class="flex items-center gap-2 border border-gray-300 rounded-lg px-3 py-2 bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <input type="date" name="tanggal" value="{{ $tanggalPilih }}"
                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" onchange="this.form.submit()"
                        class="border-none focus:ring-0 text-sm font-bold text-gray-700 bg-transparent cursor-pointer p-0">
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-blue-100/50 text-gray-600 text-sm border-b border-gray-200">
                        <th class="py-4 px-4 font-bold">ID</th>
                        <th class="py-4 px-4 font-bold">Jenis Lapangan</th>
                        <th class="py-4 px-4 font-bold">Jam</th>
                        <th class="py-4 px-4 font-bold">Harga</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($slotJadwal as $slot)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-bold text-gray-800">
                                #{{ $slot->id_slot }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">
                                {{ $slot->lapangan->nama_lapangan }}
                            </td>
                            <td class="py-4 px-4 text-sm font-bold text-gray-700">
                                {{ $slot->jam }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">
                                Rp. {{ number_format($slot->harga, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-sm">
                                @if($slot->status == 'siap dipakai')
                                    <span class="text-green-600 font-medium">siap dipakai</span>
                                @else
                                    <span class="text-red-500 font-medium">di-booking</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $jamAwal = substr($slot->jam, 0, 5);
                                    $jamAkhir = substr($slot->jam, 8, 5);
                                @endphp
                                <button
                                    onclick="openModal('{{ $slot->lapangan->id }}', '{{ $jamAwal }}', '{{ $jamAkhir }}', '{{ $slot->status }}')"
                                    class="text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-gray-500">Belum ada data lapangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
<div id="modal-jadwal"
    class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Status Jadwal</h3>
            <button onclick="closeModal()"
                class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('mitra.jadwal.update') }}" method="POST">
            @csrf
            <input type="hidden" name="lapangan_id" id="modal-lapangan-id">
            <input type="hidden" name="tanggal" value="{{ $tanggalPilih }}">
            <input type="hidden" name="jam_mulai" id="modal-jam-mulai">
            <input type="hidden" name="jam_selesai" id="modal-jam-selesai">

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Jam Operasional</label>
                <input type="text" id="modal-jam-display"
                    class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-600 font-medium"
                    readonly>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Status Lapangan</label>
                <select name="status" id="modal-status"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500 font-medium">
                    <option value="siap dipakai">Siap Dipakai (Tersedia)</option>
                    <option value="maintenance">Maintenance (Sedang Perbaikan)</option>
                </select>
                <p class="text-xs text-orange-500 font-medium mt-2">*Pilih Maintenance untuk memblokir jadwal ini agar
                    tidak bisa dibooking penyewa.</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg font-bold transition-colors">Batal</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold transition-colors shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(lapanganId, jamMulai, jamSelesai, status) {
        document.getElementById('modal-lapangan-id').value = lapanganId;
        document.getElementById('modal-jam-mulai').value = jamMulai;
        document.getElementById('modal-jam-selesai').value = jamSelesai;
        document.getElementById('modal-jam-display').value = jamMulai + ' WIB  s/d  ' + jamSelesai + ' WIB';

        // Setel dropdown mau Maintenance atau Siap Dipakai
        let statusSelect = document.getElementById('modal-status');
        statusSelect.value = (status === 'siap dipakai') ? 'siap dipakai' : 'maintenance';

        // Munculin pop-up
        document.getElementById('modal-jadwal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-jadwal').classList.add('hidden');
    }
</script>
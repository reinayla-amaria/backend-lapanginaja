<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-7xl mx-auto bg-gray-50 min-h-screen">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Order</h2>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mb-4 border-b border-gray-200 pb-3 gap-4">
            
            <div class="flex space-x-6 text-sm font-bold">
                <a href="{{ url('/transaksi') }}" 
                   class="{{ empty($status) ? 'text-gray-800 border-b-2 border-gray-800' : 'text-gray-400 hover:text-gray-600 transition-colors' }} pb-3">
                    All Order
                </a>
                <a href="{{ url('/transaksi') }}?status=pending&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="{{ $status == 'pending' ? 'text-gray-800 border-b-2 border-gray-800' : 'text-gray-400 hover:text-gray-600 transition-colors' }} pb-3">
                    Pending
                </a>
                <a href="{{ url('/transaksi') }}?status=completed&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="{{ $status == 'completed' ? 'text-gray-800 border-b-2 border-gray-800' : 'text-gray-400 hover:text-gray-600 transition-colors' }} pb-3">
                    Completed
                </a>
            </div>

            <form action="{{ url('/transaksi') }}" method="GET" class="flex items-center gap-3">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                
                <div class="flex items-center bg-white border border-gray-300 rounded-lg px-3 py-1.5 shadow-sm">
                    <span class="text-xs text-gray-400 mr-2 font-bold">Dari</span>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-none focus:ring-0 p-0 text-gray-700 font-bold bg-transparent cursor-pointer">
                </div>
                <div class="flex items-center bg-white border border-gray-300 rounded-lg px-3 py-1.5 shadow-sm">
                    <span class="text-xs text-gray-400 mr-2 font-bold">Sampai</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-none focus:ring-0 p-0 text-gray-700 font-bold bg-transparent cursor-pointer">
                </div>
                <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white text-sm font-bold px-4 py-1.5 rounded-lg transition-colors shadow-sm">Filter</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-blue-100/50 text-gray-600 text-sm border-b border-gray-200">
                        <th class="py-4 px-4 font-bold">ID</th>
                        <th class="py-4 px-4 font-bold">Nama Lapangan</th>
                        <th class="py-4 px-4 font-bold">Tanggal & Jam</th>
                        <th class="py-4 px-4 font-bold">Jumlah</th>
                        <th class="py-4 px-4 font-bold">Harga</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    
                    @forelse($pesanans as $pesanan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-4 text-sm font-bold text-gray-800">#{{ $pesanan->id }}</td>
                        <td class="py-4 px-4 text-sm text-gray-600">{{ $pesanan->lapangan->nama_lapangan }}</td>
                        <td class="py-4 px-4 text-sm font-bold text-gray-700">
                            {{ \Carbon\Carbon::parse($pesanan->tanggal_main)->format('d M Y') }}, <br>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pesanan->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pesanan->jam_selesai)->format('H:i') }}</span>
                        </td>
                        <td class="py-4 px-4 text-sm font-bold text-gray-700">1</td>
                        <td class="py-4 px-4 text-sm text-gray-600">Rp. {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-sm">
                            @if($pesanan->status == 'pending')
                                <span class="text-orange-500 font-bold">Pending</span>
                            @elseif(in_array($pesanan->status, ['sukses', 'lunas', 'dibayar']))
                                <span class="text-green-500 font-bold">Completed</span>
                            @else
                                <span class="text-red-500 font-bold">Batal</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-gray-500 font-medium">Belum ada pesanan yang masuk nih bang.</td>
                        @empty
                    <tr>
                        <td colspan="7" class="py-16 text-gray-500 font-medium">Belum ada pesanan yang masuk nih bang.</td>
                    </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
             <a href="{{ url('/transaksi/export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors">
                 Unduh Laporan (CSV)
             </a>
        </div>

    </div>
</x-app-layout>
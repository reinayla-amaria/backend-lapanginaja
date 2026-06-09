<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold mb-6">Edit Transaksi #{{ $pesanan->id }}</h2>
            
            <form action="{{ route('admin.transaksi.update', $pesanan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pesanan</label>
                    <select name="status" class="w-full p-3 border border-gray-300 rounded-lg">
                        <option value="pending" {{ $pesanan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sukses" {{ $pesanan->status == 'sukses' ? 'selected' : '' }}>Completed</option>
                        <option value="batal" {{ $pesanan->status == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.transaksi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
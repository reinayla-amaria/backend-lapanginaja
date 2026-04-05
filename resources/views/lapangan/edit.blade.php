<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Lapangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('lapangan.update', $lapangan->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lapangan</label>
                            <input type="text" name="nama_lapangan" value="{{ $lapangan->nama_lapangan }}" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi / Keterangan</label>
                            <textarea name="lokasi" rows="3" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $lapangan->lokasi }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga per Jam (Rp)</label>
                            <input type="number" name="harga_per_jam" value="{{ $lapangan->harga_per_jam }}" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Foto Lapangan (Opsional)</label>

                            @if($lapangan->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $lapangan->foto) }}" alt="Foto Lama"
                                        class="w-32 h-32 object-cover rounded">
                                </div>
                            @endif

                            <input type="file" name="foto" accept="image/*"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <p class="text-xs text-gray-500 mt-1">*Kosongkan jika tidak ingin mengganti foto.</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition-colors">
                                Update Data
                            </button>
                            <a href="{{ route('lapangan.index') }}"
                                class="text-red-500 hover:text-red-700 font-semibold">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
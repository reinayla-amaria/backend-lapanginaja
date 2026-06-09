<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Lapangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('lapangan.create') }}"
                        class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 transition-colors">
                        + Tambah Lapangan
                    </a>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b py-2">Nama Lapangan</th>
                                <th class="border-b py-2">Lokasi</th>
                                <th class="border-b py-2">Harga / Jam</th>
                                <th class="border-b py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lapangans as $lapangan)
                                <tr>
                                    <td class="border-b py-2">{{ $lapangan->nama_lapangan }}</td>
                                    <td class="border-b py-2">{{ $lapangan->lokasi }}</td>
                                    <td class="border-b py-2">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                                    </td>
                                    <td class="border-b py-2">
                                        <div class="flex items-center gap-2">

                                            <a href="{{ route('lapangan.edit', $lapangan->id) }}"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-sm transition">
                                                Edit
                                            </a>

                                            <form action="{{ route('lapangan.destroy', $lapangan->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin mau hapus lapangan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm shadow-sm transition duration-150 ease-in-out">
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Belum ada data lapangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
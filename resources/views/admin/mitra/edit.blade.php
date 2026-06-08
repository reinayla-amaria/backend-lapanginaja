<x-app-layout>
    <div class="py-8 px-6 lg:px-8 max-w-3xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800">Edit Data Mitraa</h2>
            <p class="text-base text-gray-500 mt-2">Update informasi akun pemilik GOR.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('mitra.update', $mitra->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-5">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Mitra / GOR</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $mitra->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Akun</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $mitra->email) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-8">
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin ganti password)</span></label>
                    <input type="password" name="password" id="password"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('mitra.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-bold transition-all">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold transition-all shadow-sm">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
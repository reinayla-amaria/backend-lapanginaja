<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Kotak 1: Update Informasi Profil --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Kotak 2: Update Password --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- KOTAK BARU: Fitur Verifikasi Dua Langkah (MFA) --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Verifikasi Dua Langkah (MFA)') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Amankan akun Anda layaknya WhatsApp menggunakan kode OTP dinamis dari aplikasi Google Authenticator di HP Anda.') }}
                            </p>
                        </header>

                        {{-- Kondisi 1: Jika MFA Sudah Aktif di Database --}}
                        @if($mfaEnabled)
                            <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-medium">
                                🔒 Fitur Verifikasi Dua Langkah Akun Anda Saat Ini Telah Aktif!
                            </div>
                            
                            {{-- Tombol untuk Mematikan MFA --}}
                            <form method="post" action="{{ route('profile.mfa.disable') }}">
                                @csrf
                                @method('delete')
                                <x-danger-button>
                                    {{ __('Matikan MFA') }}
                                </x-danger-button>
                            </form>

                        {{-- Kondisi 2: Jika Klik Tombol Aktifkan dan Gambar QR Code Keluar --}}
                        @elseif($qrCode)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 text-gray-700 dark:text-gray-300 rounded-lg space-y-4">
                                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    📸 Silakan Scan QR Code di bawah ini menggunakan aplikasi Google Authenticator di Smartphone Anda:
                                </p>
                                
                                {{-- Cetak Gambar QR Code Otomatis --}}
                                <div class="inline-block p-4 bg-white rounded-lg">
                                    {!! $qrCode !!}
                                </div>

                                <p class="text-xs text-gray-500">
                                    Setelah di-scan, akun Anda otomatis terhubung. Silakan segarkan/refresh halaman ini untuk melihat perubahan status akun.
                                </p>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Selesai & Refresh') }}
                            </a>

                        {{-- Kondisi 3: Tampilan Awal Jika MFA Belum Aktif --}}
                        @else
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 rounded-lg text-sm">
                                ⚠️ Status Keamanan: Hubungkan dengan HP sekarang agar akun Admin terlindungi sempurna.
                            </div>

                            {{-- Tombol Memicu Munculnya QR Code --}}
                            <form method="get" action="{{ route('profile.edit') }}">
                                <input type="hidden" name="enable_mfa" value="1">
                                <x-primary-button>
                                    {{ __('Aktifkan MFA Sekarang') }}
                                </x-primary-button>
                            </form>
                        @endif
                    </section>
                </div>
            </div>

            {{-- Kotak 3: Hapus Akun --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
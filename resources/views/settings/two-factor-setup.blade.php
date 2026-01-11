@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Setup 2FA">
    <x-ui.page-header
        title="Aktifkan Pengesahan Dua Faktor (2FA)"
        description="Ikuti langkah-langkah di bawah untuk mengaktifkan 2FA"
    >
        <x-ui.container class="w-full max-w-2xl mx-auto">
            @if(session('error'))
                <x-ui.error-alert class="mb-6">
                    {{ session('error') }}
                </x-ui.error-alert>
            @endif

            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">
                <!-- Step 1: Install App -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">1</span>
                        <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif; font-size: 13px;">
                            Muat Turun Aplikasi Authenticator
                        </h3>
                    </div>
                    <p class="text-gray-600 ml-11" style="font-family: Poppins, sans-serif; font-size: 11px;">
                        Muat turun aplikasi authenticator seperti Google Authenticator atau Microsoft Authenticator dari App Store atau Play Store.
                    </p>
                </div>

                <!-- Step 2: Scan QR Code -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">2</span>
                        <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif; font-size: 13px;">
                            Imbas Kod QR
                        </h3>
                    </div>
                    <div class="ml-11">
                        <p class="text-gray-600 mb-4" style="font-family: Poppins, sans-serif; font-size: 11px;">
                            Buka aplikasi authenticator dan imbas kod QR di bawah:
                        </p>
                        
                        <div class="flex justify-center mb-4">
                            <div class="p-4 bg-white border border-gray-200 rounded-sm">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-sm p-4">
                            <p class="text-xs text-gray-500 mb-2" style="font-family: Poppins, sans-serif;">
                                Atau masukkan kod ini secara manual:
                            </p>
                            <code class="block text-center text-lg font-mono font-bold text-gray-900 tracking-widest select-all">
                                {{ $secret }}
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Verify -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">3</span>
                        <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif; font-size: 13px;">
                            Sahkan Kod
                        </h3>
                    </div>
                    <div class="ml-11">
                        <p class="text-gray-600 mb-4" style="font-family: Poppins, sans-serif; font-size: 11px;">
                            Masukkan kod 6 digit dari aplikasi authenticator untuk mengesahkan:
                        </p>

                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <div class="flex gap-3">
                                <input type="text" 
                                       name="code" 
                                       maxlength="6" 
                                       pattern="[0-9]{6}"
                                       required
                                       autofocus
                                       class="w-40 px-4 py-2 text-center text-lg font-mono tracking-widest border border-gray-300 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       style="font-family: 'Courier New', monospace;"
                                       placeholder="000000"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                
                                <button type="submit" 
                                        class="px-6 py-2 bg-green-600 text-white rounded-sm hover:bg-green-700 transition-colors inline-flex items-center gap-2"
                                        style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                                    Aktifkan 2FA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Cancel -->
                <div class="border-t border-gray-200 pt-4 mt-6">
                    <a href="{{ route('settings.index', ['tab' => 'keselamatan']) }}" 
                       class="text-gray-600 hover:text-gray-800 inline-flex items-center gap-1"
                       style="font-family: Poppins, sans-serif; font-size: 11px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
                        Kembali ke Tetapan
                    </a>
                </div>
            </div>
        </x-ui.container>
    </x-ui.page-header>
</x-dashboard-layout>

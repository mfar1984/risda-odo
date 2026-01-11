<x-guest-layout>
    <div class="mb-4 text-center">
        <span class="material-symbols-outlined text-blue-600 mb-2" style="font-size: 48px;">security</span>
        <h2 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif; font-size: 16px;">
            Pengesahan Dua Faktor
        </h2>
        <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif; font-size: 11px;">
            Masukkan kod 6 digit dari aplikasi authenticator anda
        </p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-sm">
            <p class="text-sm text-red-600" style="font-family: Poppins, sans-serif; font-size: 11px;">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div class="mb-6">
            <x-forms.input-label for="code" value="Kod Pengesahan" />
            <input type="text" 
                   id="code"
                   name="code" 
                   maxlength="8"
                   required
                   autofocus
                   autocomplete="one-time-code"
                   class="mt-1 block w-full px-4 py-3 text-center text-xl font-mono tracking-widest border border-gray-300 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                   style="font-family: 'Courier New', monospace;"
                   placeholder="000000"
                   oninput="this.value = this.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase()">
            <p class="mt-2 text-xs text-gray-500" style="font-family: Poppins, sans-serif;">
                Atau masukkan kod pemulihan (recovery code)
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" 
                    class="w-full px-4 py-3 bg-blue-600 text-white rounded-sm hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2"
                    style="font-family: Poppins, sans-serif; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">verified</span>
                Sahkan
            </button>

            <a href="{{ route('login') }}" 
               class="w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-sm hover:bg-gray-50 transition-colors inline-flex items-center justify-center gap-2"
               style="font-family: Poppins, sans-serif; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                Kembali ke Log Masuk
            </a>
        </div>
    </form>
</x-guest-layout>

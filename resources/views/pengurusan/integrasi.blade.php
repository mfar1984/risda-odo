@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Integrasi">
    @php
        $maskedApiToken = $integrasi->api_token ? substr($integrasi->api_token, 0, 8) . str_repeat('*', max(strlen($integrasi->api_token) - 8, 0)) : null;
        $isAdministrator = auth()->user() && auth()->user()->jenis_organisasi === 'semua';
        $defaultTab = $isAdministrator ? 'api' : 'cuaca';
    @endphp
    
    <x-ui.page-header
        title="Integrasi"
        description="Pengurusan integrasi sistem (API, Cuaca & Email Configuration)"
    >
        <div class="integrasi-page">

        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{
            activeTab: '{{ request('tab', $defaultTab) }}',
            showToken: false,
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const tabParam = urlParams.get('tab');
                if (tabParam) {
                    this.activeTab = tabParam;
                }
            },
            copyToken() {
                const tokenText = '{{ $integrasi->api_token }}';
                navigator.clipboard.writeText(tokenText).then(() => {
                    alert('Token berjaya disalin!');
                });
            },
            async generateToken() {
                if (confirm('Adakah anda pasti untuk generate token baru? Token lama akan tidak sah lagi.')) {
                    try {
                        const response = await fetch('{{ route('pengurusan.generate-api-token') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('Token baru berjaya dijana!');
                            location.reload();
                        }
                    } catch (error) {
                        alert('Ralat: Gagal generate token');
                    }
                }
            }
        }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @if($isAdministrator)
                    <button @click="activeTab = 'api'"
                            :class="activeTab === 'api' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'api' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">api</span>
                        API
                    </button>
                    @endif
                    <button @click="activeTab = 'cuaca'"
                            :class="activeTab === 'cuaca' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'cuaca' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">wb_sunny</span>
                        Cuaca
                    </button>
                    <button @click="activeTab = 'email'"
                            :class="activeTab === 'email' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'email' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">mail</span>
                        Email (SMTP)
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-8">
                <!-- API Configuration Tab (Administrator Only) -->
                @if($isAdministrator)
                <div x-show="activeTab === 'api'" x-transition>
                    <x-ui.container class="w-full">
                        <section>
                            <header class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">API Configuration</h3>
                                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Token global untuk integrasi Mobile App dan sistem luaran. Token ini akan digunakan untuk autentikasi awal sebelum user login.
                                </p>
                            </header>

                            <!-- Success/Error Messages -->
                            @if(session('success'))
                                <x-ui.success-alert class="mb-6">
                                    {{ session('success') }}
                                </x-ui.success-alert>
                            @endif

                            @if(session('error'))
                                <x-ui.error-alert class="mb-6">
                                    {{ session('error') }}
                                </x-ui.error-alert>
                            @endif

                            <!-- API Token Card -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Global API Token
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Token ini akan digunakan oleh semua aplikasi mobile
                                        </p>
                                    </div>
                                    @if($integrasi->api_token)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800" style="font-family: Poppins, sans-serif !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 14px;">check_circle</span>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600" style="font-family: Poppins, sans-serif !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 14px;">block</span>
                                        Tiada Token
                                    </span>
                                    @endif
                                </div>

                                <!-- Token Display -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            API Token
                                        </label>
                                        <div class="flex gap-2">
                                            <div class="flex-1 relative">
                                                <input 
                                                    type="text" 
                                                    :value="showToken ? '{{ $integrasi->api_token }}' : '{{ $maskedApiToken ?? 'Tiada token' }}'"
                                                    readonly
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm font-mono"
                                                    style="font-family: 'Courier New', monospace !important; font-size: 11px !important;"
                                                />
                                                @if($integrasi->api_token)
                                                <button 
                                                    type="button"
                                                    @click="showToken = !showToken"
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                                    title="Toggle visibility"
                                                >
                                                    <span class="material-symbols-outlined" style="font-size: 18px;" x-show="!showToken">visibility</span>
                                                    <span class="material-symbols-outlined" style="font-size: 18px;" x-show="showToken">visibility_off</span>
                                                </button>
                                                @endif
                                            </div>
                                            @if($integrasi->api_token)
                                            <button 
                                                type="button"
                                                @click="copyToken()"
                                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
                                                title="Copy Token"
                                            >
                                                <span class="material-symbols-outlined" style="font-size: 16px;">content_copy</span>
                                            </button>
                                            @endif
                                            <button 
                                                type="button"
                                                @click="generateToken()"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition"
                                            >
                                                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">refresh</span>
                                                {{ $integrasi->api_token ? 'Regenerate' : 'Generate' }}
                                            </button>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            ⚠️ <strong>Penting:</strong> Regenerate token akan membatalkan token lama. Pastikan update token di semua aplikasi.
                                        </p>
                                    </div>

                                    @if($integrasi->api_token)
                                    <!-- Token Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Created At</div>
                                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                {{ $integrasi->api_token_created_at ? $integrasi->api_token_created_at->format('d M Y, H:i') : 'N/A' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Last Used</div>
                                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                {{ $integrasi->api_token_last_used ? $integrasi->api_token_last_used->diffForHumans() : 'Never' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Usage Count</div>
                                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                {{ number_format($integrasi->api_token_usage_count ?? 0) }} requests
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>


                            <!-- CORS Configuration Card -->
                            <form method="POST" action="{{ route('pengurusan.update-integrasi-cors') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            CORS Configuration
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Konfigurasi Cross-Origin Resource Sharing untuk keselamatan API
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- Allow All Origins Checkbox -->
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="api_cors_allow_all" 
                                            id="api_cors_allow_all" 
                                            value="1"
                                            {{ old('api_cors_allow_all', $integrasi->api_cors_allow_all) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                        <label for="api_cors_allow_all" class="ml-2 block text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Allow All Origins (*) - <span class="text-red-600">Tidak disyorkan untuk production</span>
                                        </label>
                                    </div>

                                    <!-- Allowed Origins Textarea -->
                                    <div>
                                        <label for="api_allowed_origins" class="block text-sm font-medium text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Allowed Origins
                                        </label>
                                        <textarea 
                                            name="api_allowed_origins" 
                                            id="api_allowed_origins" 
                                            rows="6"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-mono"
                                            style="font-family: 'Courier New', monospace !important; font-size: 11px !important;"
                                            placeholder="http://localhost&#10;http://localhost:8000&#10;*.jara.my&#10;*.jara.com.my&#10;https://your-domain.com"
                                        >{{ old('api_allowed_origins', $integrasi->cors_origins_text) }}</textarea>
                                        <p class="mt-2 text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            💡 Masukkan satu domain per line. Gunakan <code class="px-1 py-0.5 bg-gray-100 rounded">*</code> untuk wildcard subdomain (contoh: <code class="px-1 py-0.5 bg-gray-100 rounded">*.jara.my</code>)
                                        </p>
                                    </div>

                                    <!-- Current Status -->
                                    @if($integrasi->api_allowed_origins && count($integrasi->api_allowed_origins) > 0)
                                    <div class="bg-green-50 border border-green-200 rounded-md p-3">
                                        <div class="flex items-center">
                                            <span class="material-symbols-outlined text-green-600 mr-2" style="font-size: 18px;">check_circle</span>
                                            <div class="text-sm text-green-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                <strong>{{ count($integrasi->api_allowed_origins) }}</strong> domain dibenarkan
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                        <div class="flex items-center">
                                            <span class="material-symbols-outlined text-yellow-600 mr-2" style="font-size: 18px;">warning</span>
                                            <div class="text-sm text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                Tiada domain dibenarkan - API tidak boleh diakses dari mana-mana origin
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Save Button -->
                                    <div class="flex justify-end pt-4 border-t border-gray-200">
                                        <x-buttons.primary-button type="submit">
                                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                                            Simpan Konfigurasi CORS
                                        </x-buttons.primary-button>
                                    </div>
                                </div>
                            </form>
                            <!-- Info Card -->
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <span class="material-symbols-outlined text-blue-400" style="font-size: 20px;">info</span>
                                    </div>
                                    <div class="ml-3">
                                        <h5 class="text-sm font-semibold text-blue-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Bagaimana Token Ini Berfungsi?
                                        </h5>
                                        <div class="text-sm text-blue-700 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                            <p>1. Mobile app guna token ini untuk autentikasi awal</p>
                                            <p>2. User login dalam app → Dapat user-specific token (Laravel Sanctum)</p>
                                            <p>3. Multi-tenancy automatic berdasarkan organisasi user yang login</p>
                                            <p>4. Token ini <strong>tidak expire</strong> melainkan anda regenerate</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </x-ui.container>
                </div>
                @endif

                <!-- Cuaca/Weather Configuration Tab (Multi-tenancy) -->
                <div x-show="activeTab === 'cuaca'" x-transition>
                    <x-ui.container class="w-full">
                        <form method="POST" action="{{ route('pengurusan.update-integrasi-cuaca') }}">
                            @csrf
                            @method('PUT')

                            <section>
                                <header class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Konfigurasi API Cuaca</h3>
                                    <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        Tetapan untuk perkhidmatan API cuaca OpenWeatherMap
                                    </p>
                                </header>

                                <!-- Success/Error Messages -->
                                @if(session('success'))
                                    <x-ui.success-alert class="mb-6">
                                        {{ session('success') }}
                                    </x-ui.success-alert>
                                @endif

                                @if(session('error'))
                                    <x-ui.error-alert class="mb-6">
                                        {{ session('error') }}
                                    </x-ui.error-alert>
                                @endif

                                <!-- Weather API Configuration -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Maklumat Asas</h4>

                                    <!-- Row 1: Provider (readonly) -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="weather_provider" value="Penyedia Perkhidmatan Cuaca" />
                                        <x-forms.text-input
                                            id="weather_provider"
                                            name="weather_provider"
                                            type="text"
                                            class="mt-1 block w-full bg-gray-50"
                                            value="OpenWeatherMap"
                                            readonly
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Sistem menggunakan OpenWeatherMap sebagai penyedia data cuaca</p>
                                    </div>

                                    <!-- Row 2: API Key -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="weather_api_key" value="Kunci API" />
                                        <x-forms.text-input
                                            id="weather_api_key"
                                            name="weather_api_key"
                                            type="text"
                                            class="mt-1 block w-full"
                                            value="{{ old('weather_api_key', $weatherConfig->weather_api_key) }}"
                                            placeholder="Masukkan API key dari OpenWeatherMap"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">
                                            Dapatkan API key percuma di <a href="https://openweathermap.org/api" target="_blank" class="text-blue-600 hover:text-blue-800 underline">OpenWeatherMap</a>
                                        </p>
                                        <x-forms.input-error class="mt-2" :messages="$errors->get('weather_api_key')" />
                                    </div>

                                    <!-- Row 3: Base URL (Readonly) -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="weather_base_url" value="URL Asas" />
                                        <x-forms.text-input
                                            id="weather_base_url"
                                            name="weather_base_url"
                                            type="text"
                                            class="mt-1 block w-full bg-gray-50"
                                            value="{{ old('weather_base_url', $weatherConfig->weather_base_url ?? 'https://api.openweathermap.org/data/2.5') }}"
                                            placeholder="https://api.openweathermap.org/data/2.5"
                                            readonly
                                        />
                                        <p class="mt-1 text-xs text-gray-500">URL endpoint API untuk OpenWeatherMap (tetap)</p>
                                    </div>
                                </div>

                                <!-- Location Settings -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Tetapan Lokasi Lalai</h4>

                                    <!-- Row 1: Default Location with Auto-Geocoding -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="weather_default_location" value="Lokasi Lalai" />
                                        <div class="flex gap-2 mt-1">
                                            <x-forms.text-input
                                                id="weather_default_location"
                                                name="weather_default_location"
                                                type="text"
                                                class="block w-full"
                                                value="{{ old('weather_default_location', $weatherConfig->weather_default_location) }}"
                                                placeholder="Contoh: Sibu, Sarawak, Malaysia"
                                            />
                                            <button type="button" onclick="getCoordinates()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors flex items-center gap-2 whitespace-nowrap" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; height: 32px;">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">my_location</span>
                                                Koordinat
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Masukkan nama lokasi dan klik butang untuk auto-populate koordinat</p>
                                        <x-forms.input-error class="mt-2" :messages="$errors->get('weather_default_location')" />
                                    </div>

                                    <!-- Row 2: Latitude & Longitude (Auto-populated, Readonly) -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="weather_default_lat" value="Latitud" />
                                            <x-forms.text-input
                                                id="weather_default_lat"
                                                name="weather_default_lat"
                                                type="number"
                                                step="0.00000001"
                                                class="mt-1 block w-full bg-gray-50"
                                                value="{{ old('weather_default_lat', $weatherConfig->weather_default_lat) }}"
                                                placeholder="Auto-populated"
                                                readonly
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Nilai antara -90 hingga 90 (auto)</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('weather_default_lat')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="weather_default_long" value="Longitud" />
                                            <x-forms.text-input
                                                id="weather_default_long"
                                                name="weather_default_long"
                                                type="number"
                                                step="0.00000001"
                                                class="mt-1 block w-full bg-gray-50"
                                                value="{{ old('weather_default_long', $weatherConfig->weather_default_long) }}"
                                                placeholder="Auto-populated"
                                                readonly
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Nilai antara -180 hingga 180 (auto)</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('weather_default_long')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- System Settings -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Tetapan Sistem</h4>

                                    <!-- Row 1: Units -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="weather_units" value="Unit Ukuran" />
                                        <select id="weather_units" name="weather_units" class="form-select mt-1">
                                            <option value="metric" {{ old('weather_units', $weatherConfig->weather_units ?? 'metric') === 'metric' ? 'selected' : '' }}>Metrik (°C, m/s)</option>
                                            <option value="imperial" {{ old('weather_units', $weatherConfig->weather_units) === 'imperial' ? 'selected' : '' }}>Imperial (°F, mph)</option>
                                            <option value="standard" {{ old('weather_units', $weatherConfig->weather_units) === 'standard' ? 'selected' : '' }}>Standard (K, m/s)</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Unit untuk suhu dan kelajuan angin</p>
                                        <x-forms.input-error class="mt-2" :messages="$errors->get('weather_units')" />
                                    </div>

                                    <!-- Row 2: Update Frequency & Cache Duration -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="weather_update_frequency" value="Kekerapan Kemaskini (Minit)" />
                                            <x-forms.text-input
                                                id="weather_update_frequency"
                                                name="weather_update_frequency"
                                                type="number"
                                                min="1"
                                                max="1440"
                                                class="mt-1 block w-full"
                                                value="{{ old('weather_update_frequency', $weatherConfig->weather_update_frequency ?? 30) }}"
                                                placeholder="30"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Berapa kerap data cuaca dikemaskini</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('weather_update_frequency')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="weather_cache_duration" value="Tempoh Cache (Minit)" />
                                            <x-forms.text-input
                                                id="weather_cache_duration"
                                                name="weather_cache_duration"
                                                type="number"
                                                min="1"
                                                max="1440"
                                                class="mt-1 block w-full"
                                                value="{{ old('weather_cache_duration', $weatherConfig->weather_cache_duration ?? 60) }}"
                                                placeholder="60"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Berapa lama data disimpan dalam cache</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('weather_cache_duration')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Weather Status -->
                                @if($weatherConfig->weather_last_update)
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Status Cuaca Semasa</h4>
                                    
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <div class="text-xs text-blue-600 mb-1" style="font-family: Poppins, sans-serif !important;">Kemaskini Terakhir</div>
                                                <div class="text-sm font-medium text-blue-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    {{ $weatherConfig->weather_last_update->format('d M Y, H:i') }}
                                                </div>
                                                <div class="text-xs text-blue-600 mt-0.5">{{ $weatherConfig->weather_last_update->diffForHumans() }}</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-blue-600 mb-1" style="font-family: Poppins, sans-serif !important;">Status Cache</div>
                                                <div class="text-sm font-medium text-blue-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    @if($weatherConfig->isWeatherCacheValid())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="material-symbols-outlined mr-1" style="font-size: 12px;">check_circle</span>
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <span class="material-symbols-outlined mr-1" style="font-size: 12px;">schedule</span>
                                                            Tamat Tempoh
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($integrasi->current_weather)
                                            <div>
                                                <div class="text-xs text-blue-600 mb-1" style="font-family: Poppins, sans-serif !important;">Cuaca Semasa</div>
                                                <div class="text-sm font-medium text-blue-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    {{ $integrasi->current_weather['description'] ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-blue-600 mt-0.5">
                                                    {{ $integrasi->current_weather['temp'] ?? 'N/A' }}°
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <x-buttons.primary-button type="submit">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                                        Simpan
                                    </x-buttons.primary-button>
                                </div>
                            </section>
                        </form>
                    </x-ui.container>
                </div>

                <!-- Email Configuration Tab (All Users - Multi-tenancy) -->
                <div x-show="activeTab === 'email'" x-transition>
                    <x-ui.container class="w-full">
                        <form method="POST" action="{{ route('pengurusan.update-integrasi-email') }}">
                            @csrf
                            @method('PUT')

                            <section>
                                <header class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Konfigurasi Email SMTP</h3>
                                    <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        Tetapan SMTP untuk penghantaran email notifikasi sistem
                                    </p>
                                </header>

                                <!-- Success/Error Messages -->
                                @if(session('success'))
                                    <x-ui.success-alert class="mb-6">
                                        {{ session('success') }}
                                    </x-ui.success-alert>
                                @endif

                                @if(session('error'))
                                    <x-ui.error-alert class="mb-6">
                                        {{ session('error') }}
                                    </x-ui.error-alert>
                                @endif

                                <!-- SMTP Server Configuration -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Pelayan SMTP</h4>

                                    <!-- Row 1: SMTP Host & Port -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 3;">
                                            <x-forms.input-label for="smtp_host" value="Hos SMTP" />
                                            <x-forms.text-input
                                                id="smtp_host"
                                                name="smtp_host"
                                                type="text"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_host', $emailConfig->smtp_host) }}"
                                                placeholder="smtp.office365.com"
                                                required
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Alamat pelayan SMTP</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_host')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_port" value="Port" />
                                            <x-forms.text-input
                                                id="smtp_port"
                                                name="smtp_port"
                                                type="number"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_port', $emailConfig->smtp_port ?? 587) }}"
                                                placeholder="587"
                                                required
                                            />
                                            <p class="mt-1 text-xs text-gray-500">TLS: 587, SSL: 465</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_port')" />
                                        </div>
                                    </div>

                                    <!-- Row 2: Username/Email & Password -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_username" value="Nama Pengguna / Email" />
                                            <x-forms.text-input
                                                id="smtp_username"
                                                name="smtp_username"
                                                type="text"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_username', $emailConfig->smtp_username) }}"
                                                placeholder="noreply@risda.gov.my"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Username untuk log masuk SMTP</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_username')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_password" value="Kata Laluan" />
                                            <x-forms.text-input
                                                id="smtp_password"
                                                name="smtp_password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                value=""
                                                placeholder="••••••••"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak mahu ubah. Disulitkan sebelum disimpan</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_password')" />
                                        </div>
                                    </div>

                                    <!-- Row 3: Encryption & Authentication -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_encryption" value="Penyulitan" />
                                            <select id="smtp_encryption" name="smtp_encryption" class="form-select mt-1">
                                                <option value="">Tiada</option>
                                                <option value="tls" {{ old('smtp_encryption', $emailConfig->smtp_encryption) === 'tls' ? 'selected' : '' }}>TLS (Disyorkan)</option>
                                                <option value="ssl" {{ old('smtp_encryption', $emailConfig->smtp_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Jenis penyulitan untuk keselamatan</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_encryption')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_authentication" value="Pengesahan" />
                                            <select id="smtp_authentication" name="smtp_authentication" class="form-select mt-1">
                                                <option value="1" {{ old('smtp_authentication', $emailConfig->smtp_authentication ?? true) ? 'selected' : '' }}>Ya (Memerlukan Username & Password)</option>
                                                <option value="0" {{ old('smtp_authentication', $emailConfig->smtp_authentication) === false ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Adakah pelayan memerlukan pengesahan?</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_authentication')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Details -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Butiran Email</h4>

                                    <!-- Row 1: From Name & From Address -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_from_name" value="Nama Pengirim" />
                                            <x-forms.text-input
                                                id="smtp_from_name"
                                                name="smtp_from_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_from_name', $emailConfig->smtp_from_name) }}"
                                                placeholder="RISDA Odometer System"
                                                required
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Nama yang akan dipaparkan sebagai pengirim</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_from_name')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_from_address" value="Alamat Email Pengirim" />
                                            <x-forms.text-input
                                                id="smtp_from_address"
                                                name="smtp_from_address"
                                                type="email"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_from_address', $emailConfig->smtp_from_address) }}"
                                                placeholder="noreply@risda.gov.my"
                                                required
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Alamat email yang akan digunakan sebagai pengirim</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_from_address')" />
                                        </div>
                                    </div>

                                    <!-- Row 2: Reply To -->
                                    <div style="margin-bottom: 20px;">
                                        <x-forms.input-label for="smtp_reply_to" value="Balas Kepada (Opsional)" />
                                        <x-forms.text-input
                                            id="smtp_reply_to"
                                            name="smtp_reply_to"
                                            type="email"
                                            class="mt-1 block w-full"
                                            value="{{ old('smtp_reply_to', $emailConfig->smtp_reply_to) }}"
                                            placeholder="support@risda.gov.my"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Alamat email untuk balasan (jika berbeza dari pengirim)</p>
                                        <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_reply_to')" />
                                    </div>
                                </div>

                                <!-- Advanced Settings -->
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Tetapan Lanjutan</h4>

                                    <!-- Row 1: Connection Timeout & Max Retries -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_connection_timeout" value="Tamat Masa Sambungan (Saat)" />
                                            <x-forms.text-input
                                                id="smtp_connection_timeout"
                                                name="smtp_connection_timeout"
                                                type="number"
                                                min="5"
                                                max="300"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_connection_timeout', $emailConfig->smtp_connection_timeout ?? 30) }}"
                                                placeholder="30"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Masa menunggu sebelum sambungan tamat (5-300 saat)</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_connection_timeout')" />
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="smtp_max_retries" value="Maksimum Percubaan Semula" />
                                            <x-forms.text-input
                                                id="smtp_max_retries"
                                                name="smtp_max_retries"
                                                type="number"
                                                min="1"
                                                max="10"
                                                class="mt-1 block w-full"
                                                value="{{ old('smtp_max_retries', $emailConfig->smtp_max_retries ?? 3) }}"
                                                placeholder="3"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Bilangan percubaan jika penghantaran gagal (1-10)</p>
                                            <x-forms.input-error class="mt-2" :messages="$errors->get('smtp_max_retries')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Test Status -->
                                @if($emailConfig->smtp_last_test)
                                <div style="margin-bottom: 32px;">
                                    <h4 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">Status Ujian</h4>
                                    
                                    <div class="bg-{{ $emailConfig->test_status_badge }}-50 rounded-lg p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <div class="text-xs text-{{ $emailConfig->test_status_badge }}-600 mb-1" style="font-family: Poppins, sans-serif !important;">Ujian Terakhir</div>
                                                <div class="text-sm font-medium text-{{ $emailConfig->test_status_badge }}-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    {{ $emailConfig->smtp_last_test->format('d M Y, H:i') }}
                                                </div>
                                                <div class="text-xs text-{{ $emailConfig->test_status_badge }}-600 mt-0.5">{{ $emailConfig->smtp_last_test->diffForHumans() }}</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-{{ $emailConfig->test_status_badge }}-600 mb-1" style="font-family: Poppins, sans-serif !important;">Status Ujian</div>
                                                <div class="text-sm font-medium text-{{ $emailConfig->test_status_badge }}-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    @if($emailConfig->smtp_test_status === 'success')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="material-symbols-outlined mr-1" style="font-size: 12px;">check_circle</span>
                                                            Berjaya
                                                        </span>
                                                    @elseif($emailConfig->smtp_test_status === 'failed')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="material-symbols-outlined mr-1" style="font-size: 12px;">error</span>
                                                            Gagal
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <span class="material-symbols-outlined mr-1" style="font-size: 12px;">pending</span>
                                                            Menunggu
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($emailConfig->smtp_test_message)
                                            <div>
                                                <div class="text-xs text-{{ $emailConfig->test_status_badge }}-600 mb-1" style="font-family: Poppins, sans-serif !important;">Mesej</div>
                                                <div class="text-sm text-{{ $emailConfig->test_status_badge }}-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                    {{ $emailConfig->smtp_test_message }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">send</span>
                                        Uji Sambungan
                                    </button>
                                    <x-buttons.primary-button type="submit">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                                        Simpan
                                    </x-buttons.primary-button>
                                </div>
                            </section>
                        </form>
                    </x-ui.container>
                </div>
            </div>
        </div>
    </x-ui.page-header>

    <!-- Geocoding JavaScript -->
    <script>
        async function getCoordinates() {
            const locationInput = document.getElementById('weather_default_location');
            const latInput = document.getElementById('weather_default_lat');
            const longInput = document.getElementById('weather_default_long');
            const apiKeyInput = document.getElementById('weather_api_key');
            
            const location = locationInput.value.trim();
            const apiKey = apiKeyInput.value.trim();
            
            if (!location) {
                alert('Sila masukkan nama lokasi terlebih dahulu.');
                locationInput.focus();
                return;
            }
            
            if (!apiKey) {
                alert('Sila masukkan API Key terlebih dahulu.');
                apiKeyInput.focus();
                return;
            }
            
            // Show loading state
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="material-symbols-outlined animate-spin" style="font-size: 16px;">progress_activity</span> Mencari...';
            
            try {
                // Use OpenWeatherMap Geocoding API
                const response = await fetch(`https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(location)}&limit=1&appid=${apiKey}`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const result = data[0];
                    latInput.value = result.lat.toFixed(8);
                    longInput.value = result.lon.toFixed(8);
                    
                    // Update location name with formatted result
                    if (result.state) {
                        locationInput.value = `${result.name}, ${result.state}, ${result.country}`;
                    } else {
                        locationInput.value = `${result.name}, ${result.country}`;
                    }
                    
                    alert(`✅ Koordinat berjaya ditemui!\n\nLokasi: ${locationInput.value}\nLatitud: ${result.lat}\nLongitud: ${result.lon}`);
                } else {
                    alert('❌ Lokasi tidak ditemui. Sila cuba dengan nama lokasi yang lain.');
                }
            } catch (error) {
                console.error('Geocoding error:', error);
                alert('❌ Ralat: Gagal mendapatkan koordinat. Sila semak API Key anda atau sambungan internet.');
            } finally {
                // Restore button
                button.disabled = false;
                button.innerHTML = originalHTML;
            }
        }
    </script>
</x-dashboard-layout>


@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Tetapan">
    <x-ui.page-header
        title="Tetapan"
        description="Konfigurasi tetapan peribadi dan keutamaan pengguna"
    >
        <div class="settings-page">

        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{
            activeTab: '{{ request('tab', 'data') }}'
        }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'data'"
                            :class="activeTab === 'data' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'data' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">download</span>
                        Data & Eksport
                    </button>
                    <button @click="activeTab = 'keselamatan'"
                            :class="activeTab === 'keselamatan' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'keselamatan' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">security</span>
                        Keselamatan
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-8">

                <!-- TAB 1: Data & Eksport -->
                <div x-show="activeTab === 'data'" x-transition>
                    <x-ui.container class="w-full">
                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <x-ui.success-alert class="mb-6">
                                {{ session('success') }}
                            </x-ui.success-alert>
                        @endif

                        @if($errors->any())
                            <x-ui.error-alert class="mb-6">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.error-alert>
                        @endif

                        <form method="POST" action="{{ route('settings.update-data-eksport') }}">
                            @csrf
                            <section>
                                <header class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tetapan Data & Eksport</h3>
                                    <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        Konfigurasi format data dan pilihan eksport. Tetapan ini akan digunakan di seluruh sistem.
                                    </p>
                                </header>

                                <!-- Export Format Card -->
                                <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                                Format Eksport Lalai
                                            </h4>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                Pilih format fail lalai untuk eksport laporan
                                            </p>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="format_eksport" value="Format Eksport" />
                                            <select id="format_eksport" name="format_eksport" class="form-select mt-1" required>
                                                <option value="pdf" {{ old('format_eksport', $settings->format_eksport) === 'pdf' ? 'selected' : '' }}>PDF (.pdf)</option>
                                                <option value="excel" {{ old('format_eksport', $settings->format_eksport) === 'excel' ? 'selected' : '' }}>Excel (.xlsx)</option>
                                                <option value="csv" {{ old('format_eksport', $settings->format_eksport) === 'csv' ? 'selected' : '' }}>CSV (.csv)</option>
                                            </select>
                                        </div>
                                        <div style="flex: 1;"></div>
                                    </div>
                                </div>

                                <!-- Date Format Card -->
                                <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                                Format Tarikh & Masa
                                            </h4>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                Pilih format paparan tarikh dan masa dalam sistem
                                            </p>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="format_tarikh" value="Format Tarikh" />
                                            <select id="format_tarikh" name="format_tarikh" class="form-select mt-1" required>
                                                <option value="DD/MM/YYYY" {{ old('format_tarikh', $settings->format_tarikh) === 'DD/MM/YYYY' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2025)</option>
                                                <option value="DD-MM-YYYY" {{ old('format_tarikh', $settings->format_tarikh) === 'DD-MM-YYYY' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2025)</option>
                                                <option value="YYYY-MM-DD" {{ old('format_tarikh', $settings->format_tarikh) === 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD (2025-12-31)</option>
                                                <option value="DD MMM YYYY" {{ old('format_tarikh', $settings->format_tarikh) === 'DD MMM YYYY' ? 'selected' : '' }}>DD MMM YYYY (31 Dec 2025)</option>
                                            </select>
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="format_masa" value="Format Masa" />
                                            <select id="format_masa" name="format_masa" class="form-select mt-1" required>
                                                <option value="24" {{ old('format_masa', $settings->format_masa) === '24' ? 'selected' : '' }}>24 Jam (14:30)</option>
                                                <option value="12" {{ old('format_masa', $settings->format_masa) === '12' ? 'selected' : '' }}>12 Jam (02:30 PM)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Number Format Card -->
                                <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                                Format Nombor & Mata Wang
                                            </h4>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                Pilih format paparan nombor dan mata wang
                                            </p>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 20px;">
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="format_nombor" value="Format Nombor" />
                                            <select id="format_nombor" name="format_nombor" class="form-select mt-1" required>
                                                <option value="1,234.56" {{ old('format_nombor', $settings->format_nombor) === '1,234.56' ? 'selected' : '' }}>1,234.56 (Koma untuk ribu)</option>
                                                <option value="1.234,56" {{ old('format_nombor', $settings->format_nombor) === '1.234,56' ? 'selected' : '' }}>1.234,56 (Titik untuk ribu)</option>
                                                <option value="1 234.56" {{ old('format_nombor', $settings->format_nombor) === '1 234.56' ? 'selected' : '' }}>1 234.56 (Ruang untuk ribu)</option>
                                            </select>
                                        </div>
                                        <div style="flex: 1;">
                                            <x-forms.input-label for="mata_wang" value="Mata Wang" />
                                            <select id="mata_wang" name="mata_wang" class="form-select mt-1" required>
                                                <option value="MYR" {{ old('mata_wang', $settings->mata_wang) === 'MYR' ? 'selected' : '' }}>MYR - Ringgit Malaysia</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between">
                                    <form method="POST" action="{{ route('settings.reset-data-eksport') }}" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Adakah anda pasti mahu kembalikan tetapan ke nilai asal?')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-sm font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">restart_alt</span>
                                            Kembalikan Ke Asal
                                        </button>
                                    </form>

                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                                        Simpan Tetapan
                                    </button>
                                </div>
                            </section>
                        </form>
                    </x-ui.container>
                </div>


                <!-- TAB 2: Keselamatan -->
                <div x-show="activeTab === 'keselamatan'" x-transition>
                    <x-ui.container class="w-full">
                        <section>
                            <header class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tetapan Keselamatan</h3>
                                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Urus keselamatan akaun dan privasi anda
                                </p>
                            </header>

                            <!-- Two-Factor Authentication Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Pengesahan Dua Faktor (2FA)
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Tambah lapisan keselamatan tambahan untuk akaun anda
                                        </p>
                                    </div>
                                    @if($twoFactorEnabled)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 text-green-800" style="font-family: Poppins, sans-serif !important;">
                                            <span class="material-symbols-outlined mr-1" style="font-size: 14px;">check_circle</span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-red-100 text-red-800" style="font-family: Poppins, sans-serif !important;">
                                            <span class="material-symbols-outlined mr-1" style="font-size: 14px;">block</span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>

                                <div class="bg-gray-50 rounded-sm p-4 mb-4">
                                    <div class="flex items-start">
                                        <span class="material-symbols-outlined text-gray-400 mr-3" style="font-size: 20px;">info</span>
                                        <div>
                                            <p class="text-sm text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                <strong>Apa itu 2FA?</strong>
                                            </p>
                                            <p class="text-xs text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                Pengesahan dua faktor memerlukan kod tambahan selain kata laluan anda semasa log masuk. Ini menjadikan akaun anda lebih selamat walaupun kata laluan anda dicuri.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Status: {{ $twoFactorEnabled ? 'Diaktifkan' : 'Tidak Diaktifkan' }}
                                        </p>
                                        @if($twoFactorEnabled)
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Gunakan aplikasi authenticator untuk log masuk</p>
                                        @else
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Tingkatkan keselamatan akaun anda</p>
                                        @endif
                                    </div>
                                    @if($twoFactorEnabled)
                                        <button type="button" 
                                                onclick="document.getElementById('disable2faModal').classList.remove('hidden')"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors">
                                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">lock_open</span>
                                            Nyahaktifkan 2FA
                                        </button>
                                    @else
                                        <a href="{{ route('two-factor.setup') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition-colors">
                                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">lock</span>
                                            Aktifkan 2FA
                                        </a>
                                    @endif
                                </div>

                                @if(session('recovery_codes'))
                                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-sm">
                                        <div class="flex items-start">
                                            <span class="material-symbols-outlined text-yellow-600 mr-3" style="font-size: 20px;">warning</span>
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                    Simpan Kod Pemulihan Ini!
                                                </p>
                                                <p class="text-xs text-yellow-700 mb-3" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    Kod ini boleh digunakan untuk log masuk jika anda kehilangan akses ke aplikasi authenticator. Setiap kod hanya boleh digunakan sekali.
                                                </p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    @foreach(session('recovery_codes') as $code)
                                                        <code class="px-2 py-1 bg-white border border-yellow-300 rounded text-xs font-mono text-gray-900">{{ $code }}</code>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Active Sessions Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Sesi Aktif
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Urus peranti yang sedang log masuk ke akaun anda
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @if($activeSessions->count() > 0)
                                        @foreach($activeSessions as $session)
                                            <div class="flex items-start justify-between p-4 {{ $session->is_current ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded-sm">
                                                <div class="flex items-start">
                                                    @php
                                                        $userAgent = strtolower($session->user_agent ?? '');
                                                        $icon = 'computer';
                                                        if (str_contains($userAgent, 'iphone') || str_contains($userAgent, 'android')) {
                                                            $icon = 'phone_iphone';
                                                        } elseif (str_contains($userAgent, 'ipad') || str_contains($userAgent, 'tablet')) {
                                                            $icon = 'tablet';
                                                        }
                                                        $iconColor = $session->is_current ? 'text-green-600' : 'text-gray-600';
                                                    @endphp
                                                    <span class="material-symbols-outlined {{ $iconColor }} mr-3" style="font-size: 24px;">{{ $icon }}</span>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                            {{ Str::limit($session->user_agent ?? 'Unknown Device', 60) }}
                                                            @if($session->is_current)
                                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Sesi Semasa</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                            {{ $session->is_current ? 'Aktif sekarang' : \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                            IP: {{ $session->ip_address ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if(!$session->is_current)
                                                    <form method="POST" action="{{ route('settings.logout-session') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                                                        <button type="submit" onclick="return confirm('Adakah anda pasti mahu log keluar sesi ini?')" class="text-red-600 hover:text-red-800 text-sm font-medium" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                            Log Keluar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-sm text-center">
                                            <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                Tiada sesi aktif
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if($activeSessions->count() > 1)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <form method="POST" action="{{ route('settings.logout-other-sessions') }}" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Adakah anda pasti mahu log keluar semua sesi lain?')" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">logout</span>
                                                Log Keluar Semua Sesi Lain
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Login History Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Sejarah Log Masuk
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Lihat aktiviti log masuk terkini
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @if($loginHistory->count() > 0)
                                        @foreach($loginHistory as $log)
                                            @php
                                                $isSuccess = $log->event === 'login_success';
                                                $borderColor = $isSuccess ? 'border-green-500' : 'border-red-500';
                                                $iconColor = $isSuccess ? 'text-green-600' : 'text-red-600';
                                                $icon = $isSuccess ? 'check_circle' : 'cancel';
                                                $description = $log->description ?? 'Log masuk';
                                                $ipAddress = $log->properties['ip'] ?? $log->properties['ip_address'] ?? 'N/A';
                                                $userAgent = $log->properties['user_agent'] ?? 'Unknown device';
                                            @endphp
                                            <div class="flex items-start p-3 border-l-4 {{ $borderColor }} bg-gray-50">
                                                <span class="material-symbols-outlined {{ $iconColor }} mr-3" style="font-size: 20px;">{{ $icon }}</span>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                        {{ $description }}
                                                    </p>
                                                    <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                        {{ $log->created_at->format('d M Y, H:i') }} • {{ Str::limit($userAgent, 50) }} • {{ $ipAddress }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-sm text-center">
                                            <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                                Tiada sejarah log masuk
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    </x-ui.container>
                </div>

            </div>
        </div>

        </div>
    </x-ui.page-header>

    {{-- Disable 2FA Modal --}}
    <div id="disable2faModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
        <div class="fixed inset-0" onclick="document.getElementById('disable2faModal').classList.add('hidden')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white text-[20px]">lock_open</span>
                        <div>
                            <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                                Nyahaktifkan 2FA
                            </h3>
                            <p class="text-red-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Masukkan kod untuk mengesahkan
                            </p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('disable2faModal').classList.add('hidden')" class="text-white hover:text-gray-200">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('two-factor.disable') }}" class="p-6">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important;">
                            Kod Pengesahan atau Kod Pemulihan
                        </label>
                        <input type="text" 
                               name="code" 
                               required
                               maxlength="8"
                               class="w-full px-4 py-2 text-center text-lg font-mono tracking-widest border border-gray-300 rounded-sm focus:ring-1 focus:ring-red-500 focus:border-red-500"
                               style="font-family: 'Courier New', monospace;"
                               placeholder="000000"
                               oninput="this.value = this.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase()">
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-sm p-3 mb-4">
                        <div class="flex">
                            <span class="material-symbols-outlined text-red-600 mr-2" style="font-size: 16px;">warning</span>
                            <p class="text-xs text-red-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Selepas nyahaktifkan 2FA, akaun anda akan kurang selamat. Anda boleh mengaktifkannya semula bila-bila masa.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" 
                                onclick="document.getElementById('disable2faModal').classList.add('hidden')"
                                class="px-4 py-2 border border-gray-300 rounded-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-sm text-xs font-medium hover:bg-red-700">
                            Nyahaktifkan 2FA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>

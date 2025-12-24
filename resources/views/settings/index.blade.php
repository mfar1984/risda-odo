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
            activeTab: '{{ request('tab', 'paparan') }}'
        }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'paparan'"
                            :class="activeTab === 'paparan' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'paparan' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">palette</span>
                        Paparan
                    </button>
                    <button @click="activeTab = 'notifikasi'"
                            :class="activeTab === 'notifikasi' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'notifikasi' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">notifications</span>
                        Notifikasi
                    </button>
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

                <!-- TAB 1: Paparan -->
                <div x-show="activeTab === 'paparan'" x-transition>
                    <x-ui.container class="w-full">
                        <section>
                            <header class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tetapan Paparan</h3>
                                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Sesuaikan paparan dan antaramuka sistem mengikut keutamaan anda
                                </p>
                            </header>

                            <!-- Theme Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Tema Antaramuka
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Pilih tema paparan yang selesa untuk mata anda
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Light Theme -->
                                    <div class="border-2 border-blue-500 rounded-sm p-4 cursor-pointer bg-blue-50">
                                        <div class="flex items-center mb-3">
                                            <span class="material-symbols-outlined text-blue-600 mr-2" style="font-size: 20px;">light_mode</span>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Cerah (Light)</span>
                                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Aktif</span>
                                        </div>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Tema terang dengan latar belakang putih
                                        </p>
                                    </div>

                                    <!-- Dark Theme -->
                                    <div class="border border-gray-200 rounded-sm p-4 cursor-pointer hover:border-gray-300 opacity-50">
                                        <div class="flex items-center mb-3">
                                            <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 20px;">dark_mode</span>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Gelap (Dark)</span>
                                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Akan Datang</span>
                                        </div>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Tema gelap untuk penggunaan malam
                                        </p>
                                    </div>

                                    <!-- System Theme -->
                                    <div class="border border-gray-200 rounded-sm p-4 cursor-pointer hover:border-gray-300 opacity-50">
                                        <div class="flex items-center mb-3">
                                            <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 20px;">contrast</span>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Ikut Sistem</span>
                                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Akan Datang</span>
                                        </div>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Ikut tetapan sistem operasi
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Language Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Bahasa
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Pilih bahasa paparan sistem
                                        </p>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 20px;">
                                    <div style="flex: 1;">
                                        <x-forms.input-label for="language" value="Bahasa Paparan" />
                                        <select id="language" name="language" class="form-select mt-1">
                                            <option value="ms" selected>Bahasa Melayu</option>
                                            <option value="en" disabled>English (Akan Datang)</option>
                                        </select>
                                    </div>
                                    <div style="flex: 1;"></div>
                                </div>
                            </div>

                            <!-- Sidebar Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Sidebar
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Tetapan paparan sidebar navigasi
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Sidebar Dikecilkan Secara Lalai</span>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Sidebar akan dikecilkan apabila halaman dimuat</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </x-ui.container>
                </div>


                <!-- TAB 2: Notifikasi -->
                <div x-show="activeTab === 'notifikasi'" x-transition>
                    <x-ui.container class="w-full">
                        <section>
                            <header class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tetapan Notifikasi</h3>
                                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Urus bagaimana dan bila anda menerima notifikasi
                                </p>
                            </header>

                            <!-- Email Notifications Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Notifikasi Email
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Terima notifikasi melalui email
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <div>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Program Baru</span>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Notifikasi apabila program baru dicipta</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" checked>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <div>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Status Program</span>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Notifikasi apabila status program berubah</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" checked>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <div>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Log Pemandu</span>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Notifikasi apabila log pemandu dikemaskini</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <span class="font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tuntutan</span>
                                            <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Notifikasi apabila tuntutan diluluskan/ditolak</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" checked>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Push Notifications Card -->
                            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                            Notifikasi Push
                                        </h4>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            Terima notifikasi push dalam aplikasi
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800" style="font-family: Poppins, sans-serif !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 14px;">schedule</span>
                                        Akan Datang
                                    </span>
                                </div>

                                <div class="bg-gray-50 rounded-sm p-4">
                                    <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        Ciri notifikasi push akan tersedia dalam versi akan datang. Anda akan dapat menerima notifikasi terus dalam pelayar web.
                                    </p>
                                </div>
                            </div>
                        </section>
                    </x-ui.container>
                </div>


                <!-- TAB 3: Data & Eksport -->
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


                <!-- TAB 4: Keselamatan -->
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-red-100 text-red-800" style="font-family: Poppins, sans-serif !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 14px;">block</span>
                                        Tidak Aktif
                                    </span>
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
                                        <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Status: Tidak Diaktifkan</p>
                                        <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Ciri ini akan tersedia dalam versi akan datang</p>
                                    </div>
                                    <button type="button" disabled class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-sm font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">lock</span>
                                        Aktifkan 2FA
                                    </button>
                                </div>
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

                                <!-- Current Session -->
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between p-4 bg-green-50 border border-green-200 rounded-sm">
                                        <div class="flex items-start">
                                            <span class="material-symbols-outlined text-green-600 mr-3" style="font-size: 24px;">computer</span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    Chrome on macOS
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Sesi Semasa</span>
                                                </p>
                                                <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    Kuala Lumpur, Malaysia • Aktif sekarang
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    IP: 192.168.1.100
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Other Sessions (Hardcoded examples) -->
                                    <div class="flex items-start justify-between p-4 bg-gray-50 border border-gray-200 rounded-sm">
                                        <div class="flex items-start">
                                            <span class="material-symbols-outlined text-gray-600 mr-3" style="font-size: 24px;">phone_iphone</span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    Safari on iPhone
                                                </p>
                                                <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    Kuala Lumpur, Malaysia • 2 jam yang lalu
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    IP: 192.168.1.101
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                            Log Keluar
                                        </button>
                                    </div>

                                    <div class="flex items-start justify-between p-4 bg-gray-50 border border-gray-200 rounded-sm">
                                        <div class="flex items-start">
                                            <span class="material-symbols-outlined text-gray-600 mr-3" style="font-size: 24px;">tablet</span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    Chrome on iPad
                                                </p>
                                                <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    Sibu, Sarawak • 1 hari yang lalu
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                    IP: 10.20.30.40
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                            Log Keluar
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 16px;">logout</span>
                                        Log Keluar Semua Sesi Lain
                                    </button>
                                </div>
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
                                    <div class="flex items-start p-3 border-l-4 border-green-500 bg-gray-50">
                                        <span class="material-symbols-outlined text-green-600 mr-3" style="font-size: 20px;">check_circle</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                Log masuk berjaya
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                23 Dis 2025, 23:30 • Chrome on macOS • 192.168.1.100
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start p-3 border-l-4 border-green-500 bg-gray-50">
                                        <span class="material-symbols-outlined text-green-600 mr-3" style="font-size: 20px;">check_circle</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                Log masuk berjaya
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                23 Dis 2025, 21:15 • Safari on iPhone • 192.168.1.101
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start p-3 border-l-4 border-red-500 bg-gray-50">
                                        <span class="material-symbols-outlined text-red-600 mr-3" style="font-size: 20px;">cancel</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                Log masuk gagal (Kata laluan salah)
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                22 Dis 2025, 14:20 • Chrome on Windows • 203.106.*.* 
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start p-3 border-l-4 border-green-500 bg-gray-50">
                                        <span class="material-symbols-outlined text-green-600 mr-3" style="font-size: 20px;">check_circle</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                Log masuk berjaya
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                                22 Dis 2025, 09:00 • Chrome on iPad • 10.20.30.40
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                                    <button type="button" class="text-blue-600 hover:text-blue-800 text-sm font-medium" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        Lihat Semua Sejarah
                                    </button>
                                </div>
                            </div>
                        </section>
                    </x-ui.container>
                </div>

            </div>
        </div>

        </div>
    </x-ui.page-header>
</x-dashboard-layout>

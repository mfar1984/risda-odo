<x-dashboard-layout title="Panduan Pengguna">
    <x-ui.page-header 
        title="Panduan Pengguna" 
        description="Panduan lengkap penggunaan Sistem Pengurusan Jejak Aset & Rekod Automatif (JARA)"
    >
        
        {{-- Hero Section --}}
        <div class="guide-hero">
            <div class="guide-hero-content">
                <div class="guide-hero-icon">
                    <span class="material-symbols-outlined text-white text-[40px]">menu_book</span>
                </div>
                <h1 class="text-white font-bold mb-2" style="font-family: Poppins, sans-serif !important; font-size: 24px !important;">
                    Panduan Pengguna JARA
                </h1>
                <p class="text-white/90 max-w-2xl mx-auto" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                    Sistem Pengurusan Jejak Aset & Rekod Automatif untuk RISDA
                </p>
                <p class="text-white/70 mt-2" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                    Ikuti panduan ini langkah demi langkah untuk menggunakan sistem dengan berkesan
                </p>
            </div>
        </div>

        {{-- Table of Contents Cards --}}
        <div class="guide-toc-grid">
            <a href="#sistem-web" class="guide-toc-card guide-toc-blue">
                <div class="guide-toc-number">1</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-blue-600 text-[18px]">computer</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Sistem Web</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Pentadbir & Pengurus</p>
            </a>

            <a href="#aplikasi-mobile" class="guide-toc-card guide-toc-green">
                <div class="guide-toc-number">2</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-green-600 text-[18px]">phone_android</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Aplikasi Mobile</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Android untuk Pemandu</p>
            </a>

            <a href="#login" class="guide-toc-card guide-toc-purple">
                <div class="guide-toc-number">3</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-purple-600 text-[18px]">login</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Log Masuk</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Web & Mobile</p>
            </a>

            <a href="#program" class="guide-toc-card guide-toc-indigo">
                <div class="guide-toc-number">4</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[18px]">event</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Pengurusan Program</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Cipta & Urus Program</p>
            </a>

            <a href="#perjalanan" class="guide-toc-card guide-toc-orange">
                <div class="guide-toc-number">5</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-orange-600 text-[18px]">directions_car</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Rekod Perjalanan</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Check-Out & Check-In</p>
            </a>

            <a href="#tuntutan" class="guide-toc-card guide-toc-teal">
                <div class="guide-toc-number">6</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-teal-600 text-[18px]">receipt_long</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Pengurusan Tuntutan</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Hantar & Proses</p>
            </a>

            <a href="#laporan" class="guide-toc-card guide-toc-pink">
                <div class="guide-toc-number">7</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-pink-600 text-[18px]">assessment</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Laporan</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Jana & Eksport</p>
            </a>

            <a href="#sokongan" class="guide-toc-card guide-toc-blue">
                <div class="guide-toc-number">8</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-blue-600 text-[18px]">support_agent</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Hubungi Sokongan</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Tiket & Chat Real-time</p>
            </a>

            <a href="#tetapan" class="guide-toc-card guide-toc-cyan">
                <div class="guide-toc-number">9</div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-cyan-600 text-[18px]">settings</span>
                    <h3 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tetapan Sistem</h3>
                </div>
                <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Konfigurasi & Integrasi</p>
            </a>
        </div>

        {{-- Section 1: Sistem Web --}}
        <div id="sistem-web" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-blue-100 text-blue-700">
                    <span class="material-symbols-outlined text-[24px]">computer</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">1. Sistem Web</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pentadbir & Pengurus</p>
                </div>
            </div>

            <p class="text-gray-700 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; line-height: 1.6;">
                Sistem web digunakan oleh pentadbir dan pengurus untuk menguruskan program, kenderaan, staf, dan memproses tuntutan.
            </p>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-blue-600 text-[16px]">check_circle</span>
                    Fungsi Utama
                </div>
                <div class="guide-feature-list">
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">group</span>
                        <span>Pengurusan Pengguna & Kumpulan</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">directions_car</span>
                        <span>Pengurusan Kenderaan & Penyelenggaraan</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">event</span>
                        <span>Pengurusan Program & Tugasan Pemandu</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <span>Pemprosesan Tuntutan (Lulus/Tolak)</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">assessment</span>
                        <span>Penjanaan Laporan & Analisis</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-blue-600">settings</span>
                        <span>Tetapan Sistem & Integrasi</span>
                    </div>
                </div>
            </div>

            <div class="guide-info-box info">
                <span class="material-symbols-outlined text-[20px]">info</span>
                <div>
                    <p class="font-semibold text-blue-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Akses Sistem</p>
                    <p class="text-blue-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Gunakan pelayar web moden seperti Chrome, Firefox, atau Edge untuk pengalaman terbaik.</p>
                </div>
            </div>
        </div>

        {{-- Section 2: Aplikasi Mobile --}}
        <div id="aplikasi-mobile" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-green-100 text-green-700">
                    <span class="material-symbols-outlined text-[24px]">phone_android</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">2. Aplikasi Mobile</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Android untuk Pemandu</p>
                </div>
            </div>

            <p class="text-gray-700 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; line-height: 1.6;">
                Aplikasi Android untuk pemandu merekod perjalanan, menghantar tuntutan, dan melihat jadual program dengan mod <strong>Offline-First</strong>.
            </p>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-green-600 text-[16px]">smartphone</span>
                    Fungsi Utama
                </div>
                <div class="guide-feature-list">
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">play_arrow</span>
                        <span>Mula & Tamat Perjalanan (Check-Out/Check-In)</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">speed</span>
                        <span>Rekod Bacaan Odometer & Kos Minyak</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">receipt</span>
                        <span>Hantar Tuntutan dengan Resit + <strong>No. Resit</strong></span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">calendar_month</span>
                        <span>Lihat Jadual Program</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">dashboard</span>
                        <span>Dashboard & Laporan Peribadi (cache offline)</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-green-600">notifications</span>
                        <span>Notifikasi Automatik</span>
                    </div>
                </div>
            </div>

            <div class="guide-info-box success">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <div>
                    <p class="font-semibold text-green-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Muat Turun Aplikasi</p>
                    <p class="text-green-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Aplikasi boleh dimuat turun dari pentadbir sistem atau melalui pautan yang diberikan. Pastikan tetapan "Install from Unknown Sources" diaktifkan.</p>
                </div>
            </div>
        </div>

        {{-- Section 3: Log Masuk --}}
        <div id="login" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-purple-100 text-purple-700">
                    <span class="material-symbols-outlined text-[24px]">login</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">3. Log Masuk</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Web & Mobile</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Web Login --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-purple-600 text-[18px]">computer</span>
                        Log Masuk Sistem Web
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-purple">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Buka Pelayar Web</h4>
                                <p>Gunakan Chrome, Firefox, atau Edge</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-purple">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Masukkan Alamat</h4>
                                <p>Taip URL sistem JARA yang diberikan</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-purple">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Masukkan Kredensial</h4>
                                <p>Isikan Email dan Kata Laluan anda</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-purple">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Klik Log Masuk</h4>
                                <p>Tekan butang biru untuk masuk</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Login --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-green-600 text-[18px]">phone_android</span>
                        Log Masuk Aplikasi Mobile
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Buka Aplikasi</h4>
                                <p>Tap ikon JARA di skrin telefon</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Masukkan Email</h4>
                                <p>Taip alamat email anda</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Masukkan Kata Laluan</h4>
                                <p>Isikan password yang diberikan</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Tap Log Masuk</h4>
                                <p>Tunggu proses pengesahan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-info-box warning mt-4">
                <span class="material-symbols-outlined text-[20px]">lock</span>
                <div>
                    <p class="font-semibold text-yellow-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Keselamatan</p>
                    <p class="text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jangan kongsikan kata laluan anda dengan sesiapa. Selepas 5 cubaan gagal, akaun akan dikunci untuk keselamatan.</p>
                </div>
            </div>
        </div>

        {{-- Section 4: Pengurusan Program --}}
        <div id="program" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-indigo-100 text-indigo-700">
                    <span class="material-symbols-outlined text-[24px]">event</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">4. Pengurusan Program</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Cipta & Urus Program</p>
                </div>
            </div>

            <div class="guide-subsection mb-4">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-indigo-600 text-[16px]">add_circle</span>
                    Cara Membuat Program Baharu
                </div>
                <div class="guide-steps">
                    <div class="guide-step guide-step-indigo">
                        <div class="guide-step-number">1</div>
                        <div class="guide-step-content">
                            <h4>Pergi ke Menu Program</h4>
                            <p>Klik menu "Program" di sidebar</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-indigo">
                        <div class="guide-step-number">2</div>
                        <div class="guide-step-content">
                            <h4>Klik Tambah Program</h4>
                            <p>Tekan butang "+ Tambah Program"</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-indigo">
                        <div class="guide-step-number">3</div>
                        <div class="guide-step-content">
                            <h4>Isikan Maklumat</h4>
                            <p>Nama, tarikh, lokasi, dan butiran program</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-indigo">
                        <div class="guide-step-number">4</div>
                        <div class="guide-step-content">
                            <h4>Pilih Pemandu</h4>
                            <p>Tugaskan pemandu untuk program ini</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-indigo">
                        <div class="guide-step-number">5</div>
                        <div class="guide-step-content">
                            <h4>Simpan Program</h4>
                            <p>Klik "Simpan" untuk cipta program</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-indigo-600 text-[16px]">label</span>
                    Status Program
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-gray-600 text-[14px] mt-0.5">fiber_manual_record</span>
                        <div>
                            <p class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Draf</p>
                            <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Program baru dicipta, belum aktif</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[14px] mt-0.5">fiber_manual_record</span>
                        <div>
                            <p class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Aktif</p>
                            <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Program sedang berjalan</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-green-600 text-[14px] mt-0.5">fiber_manual_record</span>
                        <div>
                            <p class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Selesai</p>
                            <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Program telah tamat</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-red-600 text-[14px] mt-0.5">fiber_manual_record</span>
                        <div>
                            <p class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Dibatalkan</p>
                            <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Program dibatalkan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Rekod Perjalanan --}}
        <div id="perjalanan" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-orange-100 text-orange-700">
                    <span class="material-symbols-outlined text-[24px]">directions_car</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">5. Rekod Perjalanan</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Check-Out & Check-In</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Check-Out --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-green-600 text-[18px]">logout</span>
                        Check-Out (Mula Perjalanan)
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Buka Aplikasi Mobile</h4>
                                <p>Tap ikon JARA di telefon</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Pilih Program</h4>
                                <p>Tap program yang aktif dari senarai</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Klik Check-Out</h4>
                                <p>Tekan butang "Mula Perjalanan"</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Rekod Odometer</h4>
                                <p>Masukkan bacaan odometer mula (tiada auto-prefill, mesti menaik)</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">5</div>
                            <div class="guide-step-content">
                                <h4>Lokasi & Arahan Khas</h4>
                                <p>Isi <strong>Lokasi Mula Perjalanan</strong> (opsional) dan rujuk <strong>Arahan Khas Pengguna Kenderaan</strong> jika ada</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-green">
                            <div class="guide-step-number">6</div>
                            <div class="guide-step-content">
                                <h4>Ambil Gambar (Opsional)</h4>
                                <p>Foto kenderaan atau odometer</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Check-In --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-orange-600 text-[18px]">login</span>
                        Check-In (Tamat Perjalanan)
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-orange">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Klik Check-In</h4>
                                <p>Tekan butang "Tamat Perjalanan"</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-orange">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Rekod Odometer Akhir</h4>
                                <p>Masukkan bacaan odometer tamat</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-orange">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Masukkan Kos Minyak</h4>
                                <p>Isikan jumlah kos minyak dan <strong>No. Resit</strong> (jika ada)</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-orange">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Lokasi Tamat & Nota</h4>
                                <p>Isi <strong>Lokasi Tamat Perjalanan</strong> dan nota perjalanan (opsional)</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-orange">
                            <div class="guide-step-number">5</div>
                            <div class="guide-step-content">
                                <h4>Simpan</h4>
                                <p>Tekan "Simpan" untuk rekod</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-info-box tip mt-4">
                <span class="material-symbols-outlined text-[20px]">lightbulb</span>
                <div>
                    <p class="font-semibold text-purple-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tips</p>
                    <p class="text-purple-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">GPS akan automatik merekod lokasi anda. Pastikan GPS telefon diaktifkan untuk ketepatan data.</p>
                </div>
            </div>
        </div>

        {{-- Section 6: Pengurusan Tuntutan --}}
        <div id="tuntutan" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-teal-100 text-teal-700">
                    <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">6. Pengurusan Tuntutan</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Hantar & Proses</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Hantar Tuntutan (Mobile) --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-teal-600 text-[18px]">upload</span>
                        Hantar Tuntutan (Mobile)
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Pergi ke Tab Tuntutan</h4>
                                <p>Tap tab "Tuntutan" di bottom bar</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Klik Buat Tuntutan</h4>
                                <p>Tekan butang "+ Buat Tuntutan"</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Pilih Jenis</h4>
                                <p>Minyak, Tol, Parking, atau Lain-lain</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Muat Naik Resit</h4>
                                <p>Ambil gambar atau pilih dari galeri</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">5</div>
                            <div class="guide-step-content">
                                <h4>Isikan Jumlah</h4>
                                <p>Masukkan jumlah tuntutan (RM)</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-teal">
                            <div class="guide-step-number">6</div>
                            <div class="guide-step-content">
                                <h4>Hantar</h4>
                                <p>Tekan "Hantar" untuk proses</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Proses Tuntutan (Web) --}}
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        <span class="material-symbols-outlined text-indigo-600 text-[18px]">task_alt</span>
                        Proses Tuntutan (Web)
                    </h3>
                    <div class="guide-steps">
                        <div class="guide-step guide-step-indigo">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <h4>Pergi ke Laporan Tuntutan</h4>
                                <p>Menu "Laporan" → "Laporan Tuntutan"</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-indigo">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <h4>Pilih Tuntutan</h4>
                                <p>Klik ikon "Mata" untuk lihat butiran</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-indigo">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <h4>Semak Resit & Butiran</h4>
                                <p>Verify jumlah dengan resit yang dimuat naik</p>
                            </div>
                        </div>
                        <div class="guide-step guide-step-indigo">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <h4>Buat Keputusan</h4>
                                <p>Klik "Lulus" atau "Tolak" dengan nota</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-subsection mt-4">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-teal-600 text-[16px]">category</span>
                    Jenis Tuntutan
                </div>
                <div class="guide-feature-list">
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-teal-600">local_gas_station</span>
                        <span><strong>Minyak</strong> - Tuntutan kos minyak kenderaan</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-teal-600">toll</span>
                        <span><strong>Tol</strong> - Tuntutan bayaran tol</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-teal-600">local_parking</span>
                        <span><strong>Parking</strong> - Tuntutan bayaran parking</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-teal-600">more_horiz</span>
                        <span><strong>Lain-lain</strong> - Perbelanjaan berkaitan program</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 7: Laporan --}}
        <div id="laporan" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-pink-100 text-pink-700">
                    <span class="material-symbols-outlined text-[24px]">assessment</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">7. Laporan</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jana & Eksport</p>
                </div>
            </div>

            <p class="text-gray-700 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; line-height: 1.6;">
                Sistem menyediakan pelbagai laporan untuk analisis dan audit.
            </p>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-pink-600 text-[16px]">description</span>
                    Jenis Laporan Tersedia
                </div>
                <div class="guide-feature-list">
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-pink-600">local_shipping</span>
                        <span><strong>Penggunaan Kenderaan</strong> - Butir penggunaan (tarikh, masa, pemandu, tujuan & destinasi, bacaan odometer, trip meter, pembelian bahan api, arahan khas)</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-pink-600">receipt_long</span>
                        <span><strong>Laporan Tuntutan</strong> - Senarai semua tuntutan dengan status</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-pink-600">directions_car</span>
                        <span><strong>Laporan Perjalanan</strong> - Rekod perjalanan mengikut pemandu</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-pink-600">event</span>
                        <span><strong>Laporan Program</strong> - Status dan analisis program</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-pink-600">build</span>
                        <span><strong>Laporan Penyelenggaraan</strong> - Kos dan jadual servis</span>
                    </div>
                </div>
            </div>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-pink-600 text-[16px]">download</span>
                    Cara Eksport Laporan
                </div>
                <div class="guide-steps">
                    <div class="guide-step guide-step-pink">
                        <div class="guide-step-number">1</div>
                        <div class="guide-step-content">
                            <h4>Pilih Laporan</h4>
                            <p>Menu "Laporan" → pilih jenis laporan</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-pink">
                        <div class="guide-step-number">2</div>
                        <div class="guide-step-content">
                            <h4>Tapis Data</h4>
                            <p>Gunakan penapis tarikh, pemandu, atau status</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-pink">
                        <div class="guide-step-number">3</div>
                        <div class="guide-step-content">
                            <h4>Klik Eksport</h4>
                            <p>Tekan butang "Eksport PDF" atau "Eksport Excel"</p>
                        </div>
                    </div>
                    <div class="guide-step guide-step-pink">
                        <div class="guide-step-number">4</div>
                        <div class="guide-step-content">
                            <h4>Muat Turun</h4>
                            <p>Fail akan dimuat turun secara automatik</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 8: Hubungi Sokongan --}}
        <div id="sokongan" class="guide-section">
            <div class="guide-section-header guide-header-blue">
                <span class="material-symbols-outlined text-[24px]">support_agent</span>
                <h2 style="font-family: Poppins, sans-serif !important; font-size: 16px !important; font-weight: 700;">Hubungi Sokongan</h2>
            </div>

            <p class="guide-description">
                Sistem tiket sokongan untuk pengurusan isu, pertanyaan, dan bantuan teknikal dengan real-time chat dan multi-user collaboration.
            </p>

            <div class="guide-content-grid">
                {{-- Create Ticket --}}
                <div class="guide-content-card">
                    <div class="guide-card-icon guide-icon-blue">
                        <span class="material-symbols-outlined">add_circle</span>
                    </div>
                    <h3 class="guide-card-title">Buat Tiket Baru</h3>
                    <div class="guide-steps">
                        <div class="guide-step">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <strong>Akses Hubungi Sokongan</strong>
                                <p>Pergi ke menu <code>Bantuan → Hubungi Sokongan</code></p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <strong>Klik "Buat Tiket Baru"</strong>
                                <p>Butang biru di bahagian atas halaman</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <strong>Isi Maklumat Tiket</strong>
                                <p>Subjek, Kategori (Teknikal/Akaun/Perjalanan/Tuntutan/Lain), Keutamaan (Rendah/Sederhana/Tinggi/Kritikal)</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <strong>Taip Mesej & Lampiran</strong>
                                <p>Terangkan isu anda. Boleh upload fail (PDF, gambar, dokumen) jika perlu</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">5</div>
                            <div class="guide-step-content">
                                <strong>Hantar Tiket</strong>
                                <p>Administrator akan terima notification dan respond dalam masa terdekat</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reply & Chat --}}
                <div class="guide-content-card">
                    <div class="guide-card-icon guide-icon-green">
                        <span class="material-symbols-outlined">chat</span>
                    </div>
                    <h3 class="guide-card-title">Reply & Real-time Chat</h3>
                    <div class="guide-steps">
                        <div class="guide-step">
                            <div class="guide-step-number">1</div>
                            <div class="guide-step-content">
                                <strong>Buka Tiket</strong>
                                <p>Klik butang <code>Lihat & Respond</code> pada tiket</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">2</div>
                            <div class="guide-step-content">
                                <strong>Lihat Chat Thread</strong>
                                <p>Semua mesej dipaparkan dengan nama, timestamp, IP address, dan lokasi</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">3</div>
                            <div class="guide-step-content">
                                <strong>Taip Balasan</strong>
                                <p>Scroll ke bawah, taip dalam text area "Balas Tiket"</p>
                            </div>
                        </div>
                        <div class="guide-step">
                            <div class="guide-step-number">4</div>
                            <div class="guide-step-content">
                                <strong>Auto-update (Real-time)</strong>
                                <p>Chat auto-refresh setiap 3 saat. Mesej baru appear automatik dengan notification sound!</p>
                            </div>
                        </div>
                    </div>
                    <div class="guide-note guide-note-success">
                        <span class="material-symbols-outlined">info</span>
                        <div>
                            <strong>Real-time Collaboration:</strong> Multiple users boleh chat simultaneously. Mesej auto-sync tanpa perlu refresh page!
                        </div>
                    </div>
                </div>

                {{-- Assign & Participants --}}
                <div class="guide-content-card">
                    <div class="guide-card-icon guide-icon-purple">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <h3 class="guide-card-title">Assign & Tambah Peserta</h3>
                    <p class="guide-card-text">
                        <strong>Tugaskan (Assign):</strong> Assign tiket kepada staff yang bertanggungjawab.
                    </p>
                    <ul class="guide-list">
                        <li>Klik butang <strong>"Tugaskan"</strong> dalam tiket modal</li>
                        <li>Pilih pengguna dalam dropdown "Tugaskan Kepada"</li>
                        <li>Staff boleh assign kepada staff dalam organisasi sama</li>
                        <li>Administrator boleh assign kepada sesiapa sahaja</li>
                    </ul>
                    <p class="guide-card-text mt-3">
                        <strong>Tambah Peserta (Participants):</strong> Add staff untuk discussion kumpulan.
                    </p>
                    <ul class="guide-list">
                        <li>Dalam modal "Tugaskan", pilih pengguna untuk add participant</li>
                        <li>Klik <strong>"Tambah"</strong> - multiple participants boleh ditambah</li>
                        <li>Semua participants boleh lihat dan reply dalam tiket</li>
                        <li>Gunakan untuk "loop in" staff lain untuk bantuan</li>
                    </ul>
                </div>

                {{-- Export & Close --}}
                <div class="guide-content-card">
                    <div class="guide-card-icon guide-icon-orange">
                        <span class="material-symbols-outlined">download</span>
                    </div>
                    <h3 class="guide-card-title">Eksport & Tutup Tiket</h3>
                    <p class="guide-card-text">
                        <strong>Selesaikan Tiket:</strong> Administrator & creator boleh close tiket.
                    </p>
                    <ul class="guide-list">
                        <li>Klik butang <strong>"Selesaikan"</strong> dalam modal</li>
                        <li>Tambah nota resolution (optional)</li>
                        <li>Status berubah ke "Ditutup"</li>
                        <li>Creator & participants dapat notification</li>
                    </ul>
                    <p class="guide-card-text mt-3">
                        <strong>Eksport Chat History:</strong> Download perbincangan lengkap.
                    </p>
                    <ul class="guide-list">
                        <li>Buka tiket yang dah selesai</li>
                        <li>Klik <strong>"Eksport Chat History"</strong></li>
                        <li>Fail teks (.txt) dengan semua messages, timestamps, attachments</li>
                        <li>Untuk arkib dan audit purposes</li>
                    </ul>
                    <div class="guide-note guide-note-info">
                        <span class="material-symbols-outlined">lock</span>
                        <div>
                            <strong>Tiket Selesai:</strong> Reply form auto-hide. Hanya creator & admin boleh buka semula tiket.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 9: Tetapan Sistem --}}
        <div id="tetapan" class="guide-section">
            <div class="guide-section-header">
                <div class="guide-section-icon bg-cyan-100 text-cyan-700">
                    <span class="material-symbols-outlined text-[24px]">settings</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">8. Tetapan Sistem</h2>
                    <p class="text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Konfigurasi & Integrasi</p>
                </div>
            </div>

            <p class="text-gray-700 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; line-height: 1.6;">
                Tetapan sistem hanya boleh diakses oleh Administrator.
            </p>

            <div class="guide-subsection">
                <div class="guide-subsection-title">
                    <span class="material-symbols-outlined text-cyan-600 text-[16px]">tune</span>
                    Tetapan Tersedia
                </div>
                <div class="guide-feature-list">
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">badge</span>
                        <span><strong>Tetapan Umum</strong> - Nama sistem, logo, versi</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">group</span>
                        <span><strong>Pengurusan Kumpulan</strong> - Peranan dan kebenaran</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">link</span>
                        <span><strong>Integrasi API</strong> - Token dan endpoint</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">cloud</span>
                        <span><strong>Weather API</strong> - Integrasi cuaca</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">email</span>
                        <span><strong>Konfigurasi Email</strong> - SMTP settings</span>
                    </div>
                    <div class="guide-feature-item">
                        <span class="material-symbols-outlined text-cyan-600">notifications</span>
                        <span><strong>Notifikasi</strong> - FCM & push notifications</span>
                    </div>
                </div>
            </div>

            <div class="guide-info-box warning">
                <span class="material-symbols-outlined text-[20px]">warning</span>
                <div>
                    <p class="font-semibold text-yellow-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Amaran</p>
                    <p class="text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Hanya ubah tetapan sistem jika anda faham impaknya. Perubahan yang salah boleh menjejaskan fungsi sistem.</p>
                </div>
            </div>
        </div>

        {{-- Quick Links Section --}}
        <div class="guide-quick-links">
            <div class="text-center mb-6">
                <h3 class="font-bold text-gray-900 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                    Perlukan Bantuan Lanjut?
                </h3>
                <p class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Akses sumber bantuan tambahan di sini
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('help.faq') }}" class="guide-quick-link-button primary">
                    <span class="material-symbols-outlined text-[16px]">quiz</span>
                    Soalan Lazim (FAQ)
                </a>
                <a href="{{ route('help.hubungi-sokongan') }}" class="guide-quick-link-button secondary">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                    Hubungi Sokongan
                </a>
                <a href="{{ route('help.nota-keluaran') }}" class="guide-quick-link-button accent">
                    <span class="material-symbols-outlined text-[16px]">new_releases</span>
                    Nota Keluaran
                </a>
            </div>
        </div>

    </x-ui.page-header>
</x-dashboard-layout>

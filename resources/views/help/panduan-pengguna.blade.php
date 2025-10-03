<x-dashboard-layout title="Panduan Pengguna">
    <x-ui.page-header 
        title="Panduan Pengguna" 
        description="Panduan lengkap penggunaan Sistem Pengurusan Jejak Aset & Rekod Automatif (JARA)"
    >
        <!-- User Guide Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Table of Contents -->
            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Kandungan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <a href="#sistem-web" class="text-blue-600 hover:text-blue-800 hover:underline">1. Sistem Web (Pentadbir & Pengurus)</a>
                    <a href="#aplikasi-mobile" class="text-blue-600 hover:text-blue-800 hover:underline">2. Aplikasi Mobile (Pemandu)</a>
                    <a href="#login" class="text-blue-600 hover:text-blue-800 hover:underline">3. Log Masuk</a>
                    <a href="#program" class="text-blue-600 hover:text-blue-800 hover:underline">4. Pengurusan Program</a>
                    <a href="#perjalanan" class="text-blue-600 hover:text-blue-800 hover:underline">5. Rekod Perjalanan</a>
                    <a href="#tuntutan" class="text-blue-600 hover:text-blue-800 hover:underline">6. Pengurusan Tuntutan</a>
                    <a href="#laporan" class="text-blue-600 hover:text-blue-800 hover:underline">7. Laporan</a>
                    <a href="#tetapan" class="text-blue-600 hover:text-blue-800 hover:underline">8. Tetapan Sistem</a>
                </div>
            </div>

            <!-- Section 1: Sistem Web -->
            <div id="sistem-web" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-blue-600 text-2xl mr-3">computer</span>
                    <h2 class="text-base font-semibold text-gray-900">1. Sistem Web (Pentadbir & Pengurus)</h2>
                </div>
                <p class="text-sm text-gray-700 mb-4">Sistem web digunakan oleh pentadbir dan pengurus untuk menguruskan program, kenderaan, staf, dan memproses tuntutan.</p>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Fungsi Utama:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <li>Pengurusan Pengguna & Kumpulan</li>
                            <li>Pengurusan Kenderaan & Penyelenggaraan</li>
                            <li>Pengurusan Program & Tugasan Pemandu</li>
                            <li>Pemprosesan Tuntutan (Lulus/Tolak)</li>
                            <li>Penjanaan Laporan & Analisis</li>
                            <li>Tetapan Sistem & Integrasi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 2: Aplikasi Mobile -->
            <div id="aplikasi-mobile" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-green-600 text-2xl mr-3">phone_android</span>
                    <h2 class="text-base font-semibold text-gray-900">2. Aplikasi Mobile (Pemandu)</h2>
                </div>
                <p class="text-sm text-gray-700 mb-4">Aplikasi Android untuk pemandu merekod perjalanan, menghantar tuntutan, dan melihat jadual program.</p>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Fungsi Utama:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <li>Mula & Tamat Perjalanan (Check-Out/Check-In)</li>
                            <li>Rekod Bacaan Odometer & Kos Minyak</li>
                            <li>Hantar Tuntutan dengan Resit</li>
                            <li>Lihat Jadual Program</li>
                            <li>Dashboard & Laporan Peribadi</li>
                            <li>Notifikasi Automatik</li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-blue-600 text-xl mr-2">info</span>
                            <div>
                                <p class="text-sm font-semibold text-blue-900 mb-1">Muat Turun Aplikasi</p>
                                <p class="text-sm text-blue-800">Aplikasi boleh dimuat turun dari pentadbir sistem atau melalui pautan yang diberikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Log Masuk -->
            <div id="login" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-purple-600 text-2xl mr-3">login</span>
                    <h2 class="text-base font-semibold text-gray-900">3. Log Masuk</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Web Login -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Log Masuk Sistem Web:</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Buka pelayar web (Chrome/Firefox/Edge)</li>
                            <li>Masukkan alamat sistem (contoh: http://jara.risda.gov.my)</li>
                            <li>Masukkan <strong>Email</strong> dan <strong>Kata Laluan</strong> anda</li>
                            <li>Klik butang <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Log Masuk</span></li>
                            <li>Anda akan dibawa ke Paparan Utama (Dashboard)</li>
                        </ol>
                    </div>

                    <!-- Mobile Login -->
                    <div class="bg-gray-50 rounded border border-gray-200 p-4 mt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Log Masuk Aplikasi Mobile:</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Buka aplikasi RISDA Driver di telefon Android</li>
                            <li>Masukkan <strong>Email</strong> dan <strong>Kata Laluan</strong> yang sama dengan sistem web</li>
                            <li>Tandakan "Ingat Saya" untuk kekal log masuk</li>
                            <li>Tekan butang <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Log Masuk</span></li>
                            <li>Izinkan notifikasi apabila diminta</li>
                        </ol>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-yellow-600 text-xl mr-2">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-yellow-900 mb-1">Lupa Kata Laluan?</p>
                                <p class="text-sm text-yellow-800">Hubungi pentadbir sistem untuk menetapkan semula kata laluan anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Pengurusan Program -->
            <div id="program" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-indigo-600 text-2xl mr-3">event</span>
                    <h2 class="text-base font-semibold text-gray-900">4. Pengurusan Program</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Tambah Program -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Tambah Program Baru (Sistem Web):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Pergi ke menu <strong>Program → Senarai Program</strong></li>
                            <li>Klik butang <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">+ Tambah Program</span></li>
                            <li>Isi maklumat program:
                                <ul class="list-disc list-inside ml-6 mt-1 space-y-1">
                                    <li><strong>Nama Program:</strong> Contoh "Gotong Royong Kampung"</li>
                                    <li><strong>Tarikh Mula & Selesai:</strong> Pilih tarikh dari kalendar</li>
                                    <li><strong>Lokasi:</strong> Masukkan alamat destinasi</li>
                                    <li><strong>Pemandu:</strong> Pilih staf yang ditugaskan</li>
                                    <li><strong>Kenderaan:</strong> Pilih kenderaan untuk program</li>
                                    <li><strong>Jarak Anggaran:</strong> Masukkan jarak dalam KM</li>
                                </ul>
                            </li>
                            <li>Klik <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Simpan</span></li>
                            <li>Program akan disimpan dengan status <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">Draf</span></li>
                        </ol>
                    </div>

                    <!-- Lulus Program -->
                    <div class="bg-gray-50 rounded border border-gray-200 p-4 mt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Luluskan Program (Pengurus/Admin):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Pergi ke <strong>Program → Senarai Program</strong></li>
                            <li>Klik pada program yang berstatus <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">Draf</span></li>
                            <li>Semak maklumat program dengan teliti</li>
                            <li>Klik <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Lulus Program</span></li>
                            <li>Status berubah kepada <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">Lulus</span></li>
                            <li>Pemandu akan menerima notifikasi</li>
                        </ol>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Status Program:</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 mr-2">Draf</span> Baru dicipta</div>
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white mr-2">Lulus</span> Diluluskan</div>
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white mr-2">Aktif</span> Sedang berjalan</div>
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-600 text-white mr-2">Selesai</span> Tamat</div>
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white mr-2">Tolak</span> Tidak diluluskan</div>
                            <div class="flex items-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white mr-2">Tertunda</span> Lewat dimulakan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Rekod Perjalanan -->
            <div id="perjalanan" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-green-600 text-2xl mr-3">directions_car</span>
                    <h2 class="text-base font-semibold text-gray-900">5. Rekod Perjalanan (Aplikasi Mobile)</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Check-Out (Mula Perjalanan) -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Check-Out (Mula Perjalanan):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Buka aplikasi mobile dan log masuk</li>
                            <li>Pergi ke tab <strong>Do</strong> di bahagian bawah</li>
                            <li>Pilih program dari senarai yang ditugaskan</li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Start Journey</span></li>
                            <li>Isi maklumat:
                                <ul class="list-disc list-inside ml-6 mt-1 space-y-1">
                                    <li><strong>Bacaan Odometer:</strong> Masukkan bacaan semasa (contoh: 125000 KM)</li>
                                    <li><strong>Foto Odometer:</strong> Ambil gambar odometer (optional)</li>
                                    <li><strong>Lokasi:</strong> GPS akan auto-capture</li>
                                    <li><strong>Catatan:</strong> Nota tambahan (optional)</li>
                                </ul>
                            </li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">Confirm</span></li>
                            <li>Perjalanan bermula! Status: <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white">Dalam Perjalanan</span></li>
                        </ol>
                    </div>

                    <!-- Check-In (Tamat Perjalanan) -->
                    <div class="bg-gray-50 rounded border border-gray-200 p-4 mt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Check-In (Tamat Perjalanan):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Setelah sampai ke destinasi, buka aplikasi</li>
                            <li>Pergi ke tab <strong>Do</strong></li>
                            <li>Anda akan lihat perjalanan aktif</li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">End Journey</span></li>
                            <li>Isi maklumat:
                                <ul class="list-disc list-inside ml-6 mt-1 space-y-1">
                                    <li><strong>Bacaan Odometer:</strong> Bacaan semasa (contoh: 125045 KM)</li>
                                    <li><strong>Foto Odometer:</strong> Ambil gambar (optional)</li>
                                    <li><strong>Kos Minyak:</strong> Jika isi minyak, masukkan RM & liter</li>
                                    <li><strong>Resit Minyak:</strong> Ambil gambar resit (optional)</li>
                                    <li><strong>Stesen Minyak:</strong> Nama stesen (contoh: Petronas Bangi)</li>
                                </ul>
                            </li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Confirm</span></li>
                            <li>Jarak akan dikira automatik: 125045 - 125000 = <strong>45 KM</strong></li>
                            <li>Status berubah kepada <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Selesai</span></li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-green-600 text-xl mr-2">tips_and_updates</span>
                            <div>
                                <p class="text-sm font-semibold text-green-900 mb-1">Tips:</p>
                                <ul class="list-disc list-inside text-sm text-green-800 space-y-1">
                                    <li>Pastikan GPS telefon dibuka untuk lokasi tepat</li>
                                    <li>Ambil foto odometer yang jelas</li>
                                    <li>Simpan resit minyak untuk tuntutan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6: Tuntutan -->
            <div id="tuntutan" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-orange-600 text-2xl mr-3">receipt_long</span>
                    <h2 class="text-base font-semibold text-gray-900">6. Pengurusan Tuntutan</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Hantar Tuntutan (Mobile) -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Hantar Tuntutan (Aplikasi Mobile):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Buka aplikasi dan pergi ke tab <strong>Claim</strong></li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">+ New Claim</span></li>
                            <li>Pilih perjalanan yang ingin dituntut</li>
                            <li>Pilih kategori tuntutan:
                                <ul class="list-disc list-inside ml-6 mt-1 space-y-1">
                                    <li><strong>Tol</strong> - Bayaran tol</li>
                                    <li><strong>Parking</strong> - Bayaran parkir</li>
                                    <li><strong>Makanan & Minuman (F&B)</strong></li>
                                    <li><strong>Penginapan</strong> - Jika bermalam</li>
                                    <li><strong>Minyak</strong> - Kos minyak tambahan</li>
                                    <li><strong>Penyelenggaraan</strong> - Baiki kenderaan</li>
                                    <li><strong>Lain-lain</strong> - Perbelanjaan lain</li>
                                </ul>
                            </li>
                            <li>Masukkan <strong>Jumlah</strong> dalam RM (contoh: 10.00)</li>
                            <li>Tambah <strong>Keterangan</strong> (contoh: "Parking di tempat program")</li>
                            <li>Ambil gambar <strong>Resit</strong></li>
                            <li>Tekan <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Submit</span></li>
                            <li>Tuntutan dihantar! Status: <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white">Pending</span></li>
                        </ol>
                    </div>

                    <!-- Proses Tuntutan (Web) -->
                    <div class="bg-gray-50 rounded border border-gray-200 p-4 mt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Proses Tuntutan (Sistem Web - Pengurus):</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2 ml-4">
                            <li>Pergi ke <strong>Laporan → Laporan Tuntutan</strong></li>
                            <li>Cari tuntutan dengan status <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white">Pending</span></li>
                            <li>Klik pada tuntutan untuk melihat detail</li>
                            <li>Semak resit, jumlah, dan keterangan</li>
                            <li>Untuk <strong>meluluskan</strong>:
                                <ul class="list-disc list-inside ml-6 mt-1">
                                    <li>Klik <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Lulus</span></li>
                                    <li>Status berubah → <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">Diluluskan</span></li>
                                    <li>Pemandu terima notifikasi</li>
                                </ul>
                            </li>
                            <li>Untuk <strong>menolak</strong>:
                                <ul class="list-disc list-inside ml-6 mt-1">
                                    <li>Klik <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">Tolak</span></li>
                                    <li>Masukkan <strong>Alasan Penolakan</strong></li>
                                    <li>Status → <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">Ditolak</span></li>
                                    <li>Pemandu boleh edit & hantar semula</li>
                                </ul>
                            </li>
                        </ol>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Status Tuntutan:</p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white mr-2">Pending</span> Menunggu kelulusan</div>
                            <div><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white mr-2">Diluluskan</span> Sudah diluluskan</div>
                            <div><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white mr-2">Ditolak</span> Ditolak, boleh edit</div>
                            <div><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-600 text-white mr-2">Digantung</span> Dibatalkan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 7: Laporan -->
            <div id="laporan" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-teal-600 text-2xl mr-3">bar_chart</span>
                    <h2 class="text-base font-semibold text-gray-900">7. Laporan & Analisis</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Laporan Web -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Laporan Sistem Web:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Senarai Program</p>
                                <p class="text-xs text-gray-600">Senarai semua program dengan status dan butiran</p>
                            </div>
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Kenderaan</p>
                                <p class="text-xs text-gray-600">Penggunaan kenderaan mengikut jarak & kos</p>
                            </div>
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Kilometer</p>
                                <p class="text-xs text-gray-600">Jumlah jarak perjalanan mengikut program</p>
                            </div>
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Kos</p>
                                <p class="text-xs text-gray-600">Kos minyak & penyelenggaraan</p>
                            </div>
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Pemandu</p>
                                <p class="text-xs text-gray-600">Prestasi pemandu mengikut jarak & masa</p>
                            </div>
                            <div class="border border-gray-200 rounded p-3">
                                <p class="text-sm font-semibold text-gray-800 mb-1">Laporan Tuntutan</p>
                                <p class="text-xs text-gray-600">Ringkasan tuntutan & status kelulusan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Mobile -->
                    <div class="bg-gray-50 rounded border border-gray-200 p-4 mt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Laporan Aplikasi Mobile:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <li><strong>Dashboard:</strong> Statistik bulan semasa vs bulan lepas</li>
                            <li><strong>Report Tab:</strong> Carta prestasi 6 bulan (kos minyak, tuntutan, aktiviti)</li>
                            <li><strong>History:</strong> Sejarah perjalanan lengkap dengan gambar</li>
                            <li><strong>Claims:</strong> Status semua tuntutan</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Eksport Laporan:</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                            <li>Buka mana-mana laporan di sistem web</li>
                            <li>Pilih tarikh mula & tarikh akhir (jika ada)</li>
                            <li>Klik <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">Eksport PDF</span></li>
                            <li>Fail PDF akan dimuat turun</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Section 8: Tetapan -->
            <div id="tetapan" class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-gray-600 text-2xl mr-3">settings</span>
                    <h2 class="text-base font-semibold text-gray-900">8. Tetapan Sistem (Administrator)</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Tetapan Utama:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-2">
                            <li><strong>Tetapan Umum:</strong> Nama sistem, alamat, logo, maklumat hubungan</li>
                            <li><strong>Pengurusan Pengguna:</strong> Tambah/edit/padam pengguna</li>
                            <li><strong>Kumpulan & Kebenaran:</strong> Tetapkan kebenaran akses</li>
                            <li><strong>Integrasi:</strong> API Token, CORS, cuaca, email</li>
                            <li><strong>Notifikasi:</strong> Tetapan Firebase Cloud Messaging</li>
                        </ul>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-yellow-600 text-xl mr-2">lock</span>
                            <div>
                                <p class="text-sm font-semibold text-yellow-900 mb-1">Akses Administrator</p>
                                <p class="text-sm text-yellow-800">Hanya pengguna dengan jenis organisasi "Semua" (Administrator) boleh mengakses tetapan sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Help Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-md border border-blue-200 p-6">
                <div class="flex items-start">
                    <span class="material-symbols-outlined text-blue-600 text-3xl mr-4">help</span>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Perlukan Bantuan Lanjut?</h3>
                        <p class="text-sm text-gray-700 mb-3">Jika anda menghadapi sebarang masalah atau memerlukan bantuan tambahan, sila hubungi:</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-gray-600 text-lg mr-2">mail</span>
                                <span class="text-gray-700">Email: support@risda.gov.my</span>
                            </div>
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-gray-600 text-lg mr-2">phone</span>
                                <span class="text-gray-700">Tel: 03-XXXX XXXX</span>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('help.faq') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50">
                                <span class="material-symbols-outlined text-sm mr-1">quiz</span>
                                Soalan Lazim (FAQ)
                            </a>
                            <a href="{{ route('help.hubungi-sokongan') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50">
                                <span class="material-symbols-outlined text-sm mr-1">support_agent</span>
                                Hubungi Sokongan
                            </a>
                            <a href="{{ route('help.nota-keluaran') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50">
                                <span class="material-symbols-outlined text-sm mr-1">new_releases</span>
                                Nota Keluaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-ui.page-header>

    <style>
        /* Smooth scroll for anchor links */
        html {
            scroll-behavior: smooth;
        }
        
        /* Adjust spacing */
        #sistem-web, #aplikasi-mobile, #login, #program, #perjalanan, #tuntutan, #laporan, #tetapan {
            scroll-margin-top: 2rem;
        }
    </style>
</x-dashboard-layout>

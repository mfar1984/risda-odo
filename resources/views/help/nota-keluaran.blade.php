<x-dashboard-layout title="Nota Keluaran">


    <!-- Nota Keluaran Container -->
    <x-ui.page-header
        title="Nota Keluaran"
        description="Senarai kemaskini dan ciri baharu sistem"
    >
        <div class="release-notes-container">
            <div class="mb-12">
                <div class="version-header">
                    <div class="version-badge">
                        <span class="material-symbols-outlined version-icon">new_releases</span>
                        <span class="version-number">v1.5.0</span>
                        <span class="version-label">Major Release</span>
                    </div>
                    <div class="version-date">30/09/2025</div>
                </div>
                <div class="version-description">
                    <p>Kemas kini besar yang memantapkan modul Log Pemandu, memperkenalkan suite laporan baharu lengkap dan menyelaraskan semula pagination serta rekod untuk pengalaman pentadbiran yang standard di seluruh sistem.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Log Pemandu: tab `Semua`, `Aktif`, `Selesai`, `Tertunda` mengikut kebenaran kumpulan pengguna.</li>
                                <li>Paparan Log Pemandu (show/edit) dipenuhi maklumat program, pemandu, kenderaan, lokasi check-in/out dan audit.</li>
                                <li>Tambah peta interaktif (MapTiler/OpenStreetMap) untuk Lokasi Program di tambah/edit/show.</li>
                                <li>Laporan Senarai Program lengkap dengan statistik keseluruhan, jadual ringkas, paparan laporan terperinci dan eksport PDF.</li>
                                <li>Laporan Kenderaan, Kilometer, Kos, Pemandu – setiap satu mempunyai stat card, jadual, halaman show, PDF.</li>
                                <li>Breadcumb baharu untuk halaman program, log pemandu dan setiap modul laporan.</li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Pagination gaya E-Kubur (ringkasan "Menunjukkan X hingga Y" dan pautan di tengah) digunakan semula di senarai kumpulan, pengguna, kenderaan, RISDA (bahagian/stesen/staf), program dan log pemandu.</li>
                                <li>Penjajaran ikon tindakan (show/edit/delete/PDF) supaya konsisten dan tidak terpotong.</li>
                                <li>Penggunaan komponen `x-forms.text-input` dengan atribut `readonly` untuk paparan data (menggantikan tag `p`).</li>
                                <li>Pemangkasan teks panjang (nama program/pemohon/lokasi) agar kekal kemas dalam jadual.</li>
                                <li>Button `Lokasi Default` pada map picker menggunakan tetapan lat/long daripada Tetapan Umum.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Menyelesaikan ralat pagination `Collection::elements` dengan refactor `x-ui.pagination` dan pandangan `pagination::risda`/`risda-simple`.</li>
                                <li>Menggantikan komponen yang hilang (`primary-button`, `nav-link`, `dropdown-link`, `responsive-nav-link`).</li>
                                <li>Memperbetul rujukan route (contoh: `tambah-program` → `program.create`) dan middleware permission baharu.</li>
                                <li>Menyemak semula migrasi/seed supaya `jarak_anggaran`, koordinat program/log dan tetapan map tersedia.</li>
                                <li>Menambah seed realistik (program aktif/selesai, log pemandu dengan koordinat jagaan, kenderaan, pemandu) untuk demo laporan.</li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>Komponen `x-map.location-picker` kini menyokong MapTiler/OpenStreetMap, carian alamat Malaysia dan switch gaya peta (Jalan/Satelit/Hibrid).</li>
                                <li>Tetapan Umum menerima konfigurasi penyedia peta, API Key bertopeng, Style URL tanpa parameter key dan koordinat default.</li>
                                <li>Model/relationship: `Program` (jarak_anggaran, lokasi_lat/long, `logPemandu()`), `LogPemandu` (lokasi_lat/long, accessor label), `TetapanUmum`, `User` (`logPemandu()`).</li>
                                <li>Pengemaskinian `UserGroup` permission matrix untuk Log Pemandu (tab khusus) dan modul laporan (lihat/eksport).</li>
                                <li>Routing baharu: `laporan/*` (index/show/pdf), `log-pemandu` granular permissions, `program.create` dsb.</li>
                                <li>Penambahan template PDF khusus (`resources/views/laporan/pdf/*.blade.php`).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($releases as $index => $release)
                @if($index === 0)
                    <!-- Latest Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">{{ $release->is_latest ? 'new_releases' : 'history' }}</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>
                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @elseif($index === 1)
                    <!-- Previous Version Separator -->
                    <div style="margin: 40px 0; border-top: 2px solid #e5e7eb; padding-top: 40px;">
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 18px !important; font-weight: 600 !important; color: #6b7280; margin-bottom: 20px;">Previous Versions</h2>
                    </div>

                    <!-- Previous Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">history</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>

                    <!-- Version Description -->
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>

                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Subsequent Previous Versions -->
                    <div style="margin: 40px 0; border-top: 2px solid #e5e7eb; padding-top: 40px;"></div>

                    <!-- Previous Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">history</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>

                    <!-- Version Description -->
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>

                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

        </div>
    </x-ui.page-header>
</x-dashboard-layout>

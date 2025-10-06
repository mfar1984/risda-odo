<?php

namespace Database\Seeders;

use App\Models\NotaKeluaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotaKeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@jara.my'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'status' => 'aktif',
                'jenis_organisasi' => 'semua',
            ]
        );

        NotaKeluaran::truncate();

        $notaKeluarans = [
            [
                'versi' => '1.0.0',
                'nama_versi' => 'Asas Sistem Laravel',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 1, 5),
                'penerangan' => 'Pelancaran awal sistem RISDA ODO dengan struktur Laravel asas dan pengesahan pengguna.',
                'ciri_baharu' => [
                    'Pelancaran projek Laravel 10 lengkap dengan modul autentikasi Breeze yang diubah suai.',
                    'Pengenalan kerangka susun atur `x-dashboard-layout` bersama header dan slot kandungan asas.',
                    'Konfigurasi Vite dan Tailwind CSS untuk memuatkan Alpine serta ikon Material Symbols.'
                ],
                'penambahbaikan' => [
                    'Penyelarasan konfigurasi `.env` dan `config/app.php` untuk zon waktu, locale dan sambungan MySQL.',
                    'Pembinaan komponen Blade asas seperti `x-ui.page-header` dan `x-ui.card` untuk konsistensi UI awal.'
                ],
                'pembetulan_pepijat' => [
                    'Pengasingan laluan tetamu dan pengguna sah dalam `routes/web.php` dan `routes/auth.php`.',
                    'Pelarasaan middleware autentikasi supaya akses pentadbir sentiasa dilindungi.'
                ],
                'perubahan_teknikal' => [
                    'Pengamalan standard PSR-12 bagi semua kelas PHP dan helper dalam `AppServiceProvider`.',
                    'Konfigurasi Vite/Tailwind agar Material Symbols dan Poppins dimuat secara global.'
                ],
                'urutan' => 100,
            ],
            [
                'versi' => '1.1.0',
                'nama_versi' => 'Identiti Reka Bentuk',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 2, 12),
                'penerangan' => 'Penyempurnaan identiti visual RISDA ODO dengan sidebar kekal dan komponen UI tersuai.',
                'ciri_baharu' => [
                    'Sidebar menegak dan topbar responsif lengkap dengan status pengguna dan pautan pantas.',
                    'Komponen `x-ui.data-table`, `x-ui.stat-card`, `x-ui.pagination` (versi awal) serta `x-forms.*` untuk borang.',
                    'Penggayaan tipografi Poppins dan Material Symbols sebagai ikon standard sistem.'
                ],
                'penambahbaikan' => [
                    'Penambahan slot tindakan bagi setiap halaman senarai (butang tambah, pautan kembali, status).',
                    'Peningkatan responsif untuk paparan mudah alih dengan menu hamburger dan navigasi ringkas.'
                ],
                'pembetulan_pepijat' => [
                    'Pelarasan gaya Tailwind bagi teks asas dan tajuk agar tidak bercanggah dengan tema Laravel.',
                    'Penghalusan keadaan butang aktif dan hover menggunakan Alpine untuk mengelak konflik CSS.'
                ],
                'perubahan_teknikal' => [
                    'Pengurusan ikon menerusi import CSS global dan `@vite` bagi memastikan bundling bersih.',
                    'Penyediaan alias dalam `vite.config.js` untuk memudahkan import modul JS/CSS khusus.'
                ],
                'urutan' => 110,
            ],
            [
                'versi' => '1.2.0',
                'nama_versi' => 'Modul Organisasi RISDA',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 3, 18),
                'penerangan' => 'Pembangunan penuh modul Bahagian, Stesen dan Staf dengan sokongan multi-organisasi.',
                'ciri_baharu' => [
                    'Migrations dan model `RisdaBahagian`, `RisdaStesen`, `RisdaStaf` lengkap dengan hubungan hierarki.',
                    'Paparan tab “Bahagian / Stesen / Staf” pada `pengurusan/senarai-risda` dengan carian dan pagination.',
                    'Borang tambah, edit dan paparan terperinci menggunakan komponen `x-forms` readonly untuk konsistensi.'
                ],
                'penambahbaikan' => [
                    'Penapisan skop organisasi secara automatik agar pengguna hanya melihat data mengikut akses.',
                    'Penambahan seed HQ contoh, stesen Kajang dan staf latihan untuk pengujian modul seterusnya.'
                ],
                'pembetulan_pepijat' => [
                    'Penetapan cascade foreign key supaya Stesen dan Staf berkait dipadam bersama Bahagian.',
                    'Pelarasan ENUM `status` dan `status_dropdown` bagi mengelakkan konflik semasa migrasi.'
                ],
                'perubahan_teknikal' => [
                    "Pengelompokan laluan `Route::prefix('pengurusan')` untuk kekemasan modul organisasi.",
                    'Pengenalan `applyOrganisationScope` pada pengawal bagi menguatkuasakan multi-tenancy.'
                ],
                'urutan' => 120,
            ],
            [
                'versi' => '1.3.0',
                'nama_versi' => 'Kebenaran & Kumpulan Pengguna',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 4, 10),
                'penerangan' => 'Modul senarai kumpulan dan pengguna dengan kebenaran granular serta pengasingan data.',
                'ciri_baharu' => [
                    'Jadual `user_groups` dan modul pengurusan kumpulan dengan `kebenaran_matrix` berbentuk JSON.',
                    'Modul senarai pengguna yang menyokong tetapan kumpulan, akses organisasi dan `stesen_akses_ids`.',
                    'Middleware `CheckPermission` & `CheckAdministrator` untuk mengawal akses modul secara granular.'
                ],
                'penambahbaikan' => [
                    'Penjanaan breadcrumb dinamik melalui `BreadcrumbService` bagi halaman pengurusan utama.',
                    'Helper skop organisasi pada model (`User`, `Program`, `LogPemandu`) untuk memudahkan query.'
                ],
                'pembetulan_pepijat' => [
                    'Integrasi `RisdaHashService` dan `RisdaUserProvider` memastikan keserasian hash warisan RISDA.',
                    'Validasi unik e-mel, nombor pekerja dan sanitasi input bagi mengelakkan pendua pengguna.'
                ],
                'perubahan_teknikal' => [
                    'Kaedah `adaKebenaran()` digunakan pada view bagi memaparkan atau menyembunyi tindakan.',
                    'Laluan `web.php` dikemas kini dengan middleware `permission:module,action` untuk setiap modul.'
                ],
                'urutan' => 130,
            ],
            [
                'versi' => '1.4.0',
                'nama_versi' => 'Pengurusan Kenderaan & Program Asas',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 5, 22),
                'penerangan' => 'Penambahan modul kenderaan lengkap dan penyediaan awal modul Program serta Tetapan Umum.',
                'ciri_baharu' => [
                    'Modul `pengurusan/senarai-kenderaan` dengan senarai, borang tambah/edit dan paparan terperinci.',
                    'Penubuhan awal modul Program (senarai, borang asas, paparan program) dengan status dan penerangan.',
                    'Halaman Tetapan Umum untuk mengurus versi sistem, maklumat organisasi dan konfigurasi asas.'
                ],
                'penambahbaikan' => [
                    'Jadual Program memaparkan tempoh, status dan tindakan dengan ikon Material Symbols tersuai.',
                    'Accessor/mutator pada `Program` dan `Kenderaan` bagi memudahkan paparan label status dan tarikh.'
                ],
                'pembetulan_pepijat' => [
                    'Pengemaskinian `UserFactory` supaya nilai `jenis_organisasi` sah digunakan ketika ujian.',
                    'Penetapan foreign key antara Program, Kenderaan dan Staf untuk menjamin integriti data.'
                ],
                'perubahan_teknikal' => [
                    "Pengelompokan laluan Program di bawah `Route::prefix('program')` untuk pengurusan lebih kemas.",
                    'Pewujudan hubungan `Program` ↔ `Kenderaan/Staf` sebagai persediaan modul Log Pemandu dan laporan.'
                ],
                'urutan' => 140,
            ],
            [
                'versi' => '1.5.0',
                'nama_versi' => 'Suite Log Pemandu & Laporan',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 9, 30),
                'penerangan' => 'Penyatuan modul Log Pemandu, laporan komprehensif dan pembaikan pagination serta PDF.',
                'ciri_baharu' => [
                    'Log Pemandu lengkap dengan tab status (Semua, Aktif, Selesai, Tertunda) dan paparan terperinci.',
                    'Lima modul laporan baharu: Senarai Program, Kenderaan, Kilometer, Kos dan Pemandu dengan eksport PDF.',
                    'Integrasi pemilih lokasi `x-map.location-picker` (MapTiler/OpenStreetMap) untuk Program dan Log Pemandu.',
                    'Breadcrumb baharu bagi halaman Program, Log Pemandu dan semua laporan.'
                ],
                'penambahbaikan' => [
                    'Pagination gaya E-Kubur (ringkasan dan pautan terpusat) digunakan semula ke semua senarai penting.',
                    'Penjajaran ikon tindakan supaya konsisten, termasuk butang PDF pada laporan.',
                    'Paparan readonly menggunakan `x-forms.text-input` bagi memastikan tipografi kekal seragam.'
                ],
                'pembetulan_pepijat' => [
                    'Refactor `x-ui.pagination` untuk menyokong `LengthAwarePaginator` dan `SimplePaginator` tanpa ralat.',
                    'Pemulihan komponen Blade hilang (`primary-button`, `nav-link`) yang menyebabkan ralat runtime.',
                    'Pengemaskinian seed data supaya hubungan Program ↔ Log Pemandu kekal sah dan realistik.'
                ],
                'perubahan_teknikal' => [
                    'Penambahan medan koordinat pada Program & Log Pemandu serta tetapan MapTiler/OSM dalam Tetapan Umum.',
                    'Pengemaskinian matrix kebenaran untuk tab Log Pemandu dan modul laporan (lihat/eksport).',
                    'Pewujudan templat PDF khusus di `resources/views/laporan/pdf/*` untuk perkongsian laporan.'
                ],
                'urutan' => 150,
                'is_latest' => false,
            ],
            [
                'versi' => '1.6.0',
                'nama_versi' => 'Integrasi & Selenggara Kenderaan',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 10, 1),
                'penerangan' => 'Modul integrasi API, Email SMTP, Cuaca dan pengurusan selenggara kenderaan lengkap.',
                'ciri_baharu' => [
                    'Modul Integrasi (API, Email SMTP, Cuaca) dengan tab terpisah dan kawalan multi-tenancy.',
                    'Pengurusan Selenggara Kenderaan dengan kategori kos dinamik, upload invois dan status penyelenggaraan.',
                    'Halaman Dokumentasi API dengan sidebar menu berkumpulan dan contoh endpoint lengkap.',
                    'Laporan Tuntutan dengan workflow kelulusan (Lulus, Tolak, Batal) dan eksport PDF.'
                ],
                'penambahbaikan' => [
                    'CORS configuration dengan sokongan multiple domains dan wildcard origins.',
                    'Modal popup berpusat (z-index 1100) untuk semua halaman tuntutan.',
                    'API token generation dengan visibility toggle dan copy to clipboard.',
                    'Breadcrumb untuk semua halaman selenggara dan integrasi.'
                ],
                'pembetulan_pepijat' => [
                    'Penetapan z-index modal supaya tidak bertindih dengan topbar (header: 1000, modal: 1100).',
                    'Pelarasan status badge "Digantung" → "Dibatalkan" untuk konsistensi UI.',
                    'Auto-scroll issue pada API documentation dengan scrollable sidebar dan content area.'
                ],
                'perubahan_teknikal' => [
                    'Middleware `ApiCorsMiddleware` dan `ApiTokenMiddleware` untuk validasi global API.',
                    'Laravel Sanctum untuk user authentication dengan custom hash service (Argon2id + email salt).',
                    'Observer pattern untuk auto-update status selenggara kenderaan.',
                    'Soft deletes untuk modul Tuntutan dengan audit trail lengkap.'
                ],
                'urutan' => 160,
                'is_latest' => false,
            ],
            [
                'versi' => '1.7.0',
                'nama_versi' => 'Aplikasi Mudah Alih & Sistem Notifikasi',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 10, 2),
                'penerangan' => 'Pelancaran aplikasi mudah alih JARA Driver dengan sistem notifikasi real-time dan auto-refresh.',
                'ciri_baharu' => [
                    'Aplikasi Flutter JARA Driver dengan 5 tab: Overview, Do, Claim, Report, Logs.',
                    'Sistem Notifikasi FCM (Firebase Cloud Messaging) untuk push notification real-time.',
                    'Bell notification dengan auto-refresh 5 saat (backend & mobile app).',
                    'Auto-refresh tab count pada Log Pemandu (Semua/Aktif/Selesai/Tertunda) setiap 5 saat.',
                    'Start Journey & End Journey dengan GPS, foto odometer dan fuel tracking.',
                    'Pengurusan Tuntutan (Tol, Parking, F&B, Accommodation, Fuel, Car Maintenance, Others).',
                    'Chart analytics untuk Overview (Fuel & Claims per month) dan Do tab (Journey stats).',
                    'Profile management dengan upload foto profil dan change password.',
                    'Privacy Policy dan About screen dengan versi dinamik dari TetapanUmum.'
                ],
                'penambahbaikan' => [
                    'API endpoints lengkap: Auth, Programs, Log Pemandu, Tuntutan, Notifications, Profile, Charts.',
                    'Multi-platform support: Android, iOS, Web dengan responsive design.',
                    'Offline support menggunakan Hive untuk local data persistence.',
                    'Real-time notification dengan polling 5 saat untuk near real-time updates.',
                    'Modal drawer design untuk detail view (Program, Log, Claim, Report).',
                    'Cross-platform image handling dengan XFile dan Uint8List.',
                    'API documentation update dengan 27 completed endpoints (93% coverage).'
                ],
                'pembetulan_pepijat' => [
                    'Field name mismatch antara backend (`nama_program`) dan Flutter (`nama`) pada End Journey.',
                    'Bell icon clickable area fixed dengan IgnorePointer pada badge.',
                    'Status label "Digantung" → "Dibatalkan" untuk konsistensi dengan backend.',
                    'Notification count update selepas approve/reject/cancel tuntutan.',
                    'Auto-delete notification bila program status berubah dari "Lulus" ke status lain.',
                    'Image.file assertion error pada Flutter Web dengan conditional rendering.'
                ],
                'perubahan_teknikal' => [
                    'Flutter Clean Architecture: core, models, services, repositories, screens, widgets.',
                    'Hive adapters untuk serialization/deserialization Dart objects.',
                    'Firebase Admin SDK (kreait/firebase-php) untuk send FCM dari Laravel.',
                    'Laravel Notifications (database) untuk backend bell icon.',
                    'Alpine.js untuk dynamic frontend behavior (notification bell, token toggle).',
                    'Singleton pattern untuk ApiClient di Flutter (consistent token management).',
                    'Platform detection dengan kIsWeb untuk conditional UI/API usage.',
                    'Laravel Method Spoofing (_method=PUT) untuk FormData compatibility.',
                    'Timezone handling: UTC to Malaysia (UTC+8) untuk display di Flutter.',
                    'Auto-refresh menggunakan setInterval (JavaScript) dan Timer.periodic (Flutter).'
                ],
                'urutan' => 170,
                'is_latest' => false,
            ],
            [
                'versi' => '1.7.1',
                'nama_versi' => 'Penjejakan Tarikh Sebenar & Auto-Status Program',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 10, 3),
                'penerangan' => 'Sistem penjejakan tarikh sebenar program dan auto-update status berdasarkan aktiviti perjalanan.',
                'ciri_baharu' => [
                    'Tarikh Kelulusan: Rekod tarikh & masa program diluluskan oleh admin.',
                    'Tarikh Mula Aktif: Rekod tarikh & masa program jadi aktif (bila driver mula journey pertama).',
                    'Tarikh Sebenar Selesai: Rekod tarikh & masa program sebenarnya selesai (end journey terakhir atau auto-close).',
                    'Auto-update Program Status: LULUS → AKTIF (bila start journey), AKTIF → SELESAI (auto scheduler hourly).',
                    'Auto-detect Tertunda: LULUS → TERTUNDA jika tarikh_mula sudah lepas dan tiada journey dimulakan.',
                    'Artisan Command: `php artisan program:update-status` untuk manual trigger status update.'
                ],
                'penambahbaikan' => [
                    'Paparan tarikh sebenar di "Maklumat Pemohon Program" dengan format d/m/Y H:i.',
                    'Paparan tarikh mula aktif dan sebenar selesai di "Maklumat Pemandu Program".',
                    'Removed redundant "Kelulusan" column dari Maklumat Pemandu (sudah ada di Pemohon).',
                    'Removed "No Tel" column dari Maklumat Pemohon untuk UI yang lebih kemas.',
                    'API response update: Tambah 3 date fields (tarikh_kelulusan, tarikh_mula_aktif, tarikh_sebenar_selesai).',
                    'Backfill existing approved programs dengan tarikh_kelulusan = created_at (data migration).',
                    'Scheduler Laravel (hourly) untuk auto-close programs dan detect tertunda programs.'
                ],
                'pembetulan_pepijat' => [
                    'Program show page displaying "-" for Kelulusan bila status bukan "lulus" (sekarang guna tarikh_kelulusan).',
                    'Tarikh sebenar selesai tidak update bila driver end journey multiple times (sekarang guna latest).',
                    'Status badge logic confusing dengan multiple conditional checks (simplified to direct date display).'
                ],
                'perubahan_teknikal' => [
                    'Migration: Add 3 datetime columns (tarikh_kelulusan, tarikh_mula_aktif, tarikh_sebenar_selesai) to programs table.',
                    'Model Program: Update fillable & casts untuk support 3 date fields.',
                    'ProgramController@approve: Auto-set tarikh_kelulusan = now() on approval.',
                    'Api\\LogPemanduController@startJourney: Auto-set tarikh_mula_aktif & change status to "aktif".',
                    'Api\\LogPemanduController@endJourney: Auto-update tarikh_sebenar_selesai dengan latest end journey time.',
                    'UpdateProgramStatus Command: Auto-close AKTIF programs & mark LULUS as TERTUNDA bila tarikh lepas.',
                    'routes/console.php: Schedule program:update-status command to run hourly.',
                    'API Documentation: Add field explanation untuk 3 new date fields dengan examples.'
                ],
                'urutan' => 171,
                'is_latest' => false,
            ],
            [
                'versi' => '1.7.2',
                'nama_versi' => 'Aplikasi Mudah Alih Enhanced & Notifikasi Auto-Status',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 10, 3),
                'penerangan' => 'Penambahbaikan aplikasi mudah alih dengan paparan tarikh sebenar program dan sistem notifikasi auto-status.',
                'ciri_baharu' => [
                    'Program Detail Screen: Tambah card "Tarikh Sebenar" untuk papar 3 tarikh baharu (Kelulusan, Mula Aktif, Sebenar Selesai).',
                    'Auto-Close Notification: Backend & mobile notification bila program auto-ditutup (AKTIF → SELESAI).',
                    'Tertunda Notification: Backend & mobile notification bila program jadi TERTUNDA (LULUS → TERTUNDA).',
                    'Notification Icons: Tambah icon `check_circle` untuk auto-closed dan `warning` untuk tertunda.',
                    'Timezone Support: Format tarikh dengan Malaysia timezone (UTC+8) untuk display di mobile app.'
                ],
                'penambahbaikan' => [
                    'Flutter Program Model: Update untuk support tarikh_kelulusan, tarikh_mula_aktif, tarikh_sebenar_selesai.',
                    'Program Detail UI: Display tarikh dengan format dd/MM/yyyy HH:mm, atau "-" jika null.',
                    'Notification Screen: Handle new notification types (program_auto_closed, program_tertunda).',
                    'UpdateProgramStatus Command: Send notification ke admin & driver bila auto-close atau tertunda.',
                    'Backend Bell Icon: Update icon logic untuk support notification types baharu.',
                    'Mobile Notification: FCM push notification dengan title & body yang sesuai untuk setiap type.'
                ],
                'pembetulan_pepijat' => [
                    'API response tidak return tarikh_kelulusan, tarikh_mula_aktif, tarikh_sebenar_selesai (fixed di formatProgramData method).',
                    'Flutter app tidak dapat display tarikh sebenar kerana API tidak return fields tersebut.',
                    'Notification icon tidak display untuk auto-close dan tertunda notifications.'
                ],
                'perubahan_teknikal' => [
                    'Flutter lib/models/program.dart: Add 3 nullable DateTime fields (tarikhKelulusan, tarikhMulaAktif, tarikhSebenarSelesai).',
                    'Flutter lib/screens/program_detail_screen.dart: Add _formatDateTime() helper untuk convert UTC to Malaysia timezone.',
                    'Api\\ProgramController@formatProgramData: Add tarikh_kelulusan, tarikh_mula_aktif, tarikh_sebenar_selesai to API response.',
                    'UpdateProgramStatus Command: Integrate Notification::create & FCM push notification untuk auto-status changes.',
                    'header.blade.php: Update Alpine.js notification icon logic untuk handle program_auto_closed & program_tertunda.',
                    'notification_screen.dart: Add color & icon mapping untuk new notification types dengan PastelColors theme.'
                ],
                'urutan' => 172,
                'is_latest' => false,
            ],
            [
                'versi' => '1.7.3',
                'nama_versi' => 'Real-Time Auto-Refresh & Offline Database Structure',
                'jenis_keluaran' => 'blue',
                'tarikh_keluaran' => Carbon::create(2025, 10, 3),
                'penerangan' => 'Sistem real-time auto-refresh untuk Log Pemandu dan struktur Hive database untuk offline support.',
                'ciri_baharu' => [
                    'Auto-Refresh Table Data: Table log pemandu auto-refresh setiap 10 saat tanpa reload page.',
                    'Real-Time Status Updates: Status badge & timestamp update automatically bila ada perubahan.',
                    'Hive Database Structure: JourneyHive & ClaimHive models siap untuk offline functionality.',
                    'Enhanced JourneyHive: Tambah fields foto_odometer_keluar, foto_odometer_masuk, jenis_organisasi.',
                    'Enhanced ClaimHive: Tambah fields dikemaskini_oleh, diproses_oleh, tarikh_diproses, alasan_tolak, alasan_gantung.',
                    'Smart Polling: Tab counts (5s), table data (10s), bell notification (5s) dengan different intervals.'
                ],
                'penambahbaikan' => [
                    'Current Vehicle Odometer: Fix untuk display latest odometer dengan tepat (orderBy ID instead of timestamp).',
                    'Table Content Preservation: Pagination, search, filters maintained during auto-refresh.',
                    'Concurrent Refresh Prevention: isRefreshing flag untuk elak multiple simultaneous updates.',
                    'Seamless Updates: No page flicker atau interrupt bila auto-refresh berjalan.',
                    'Hive Models 100% Match MySQL: Semua fields sama dengan backend database untuk perfect sync.',
                    'Local Storage Fields: fotoOdometerKeluarLocal, fotoOdometerMasukLocal, resitMinyakLocal untuk offline photo storage.'
                ],
                'pembetulan_pepijat' => [
                    'Current Vehicle Odometer showing outdated value (10800 km instead of 10900 km).',
                    'Log Pemandu table not updating when journey status changes (Start/End Journey).',
                    'Tab counts updating but table content remains static until manual refresh.',
                    'getLatestOdometerAttribute() using unreliable masa_masuk sorting causing wrong odometer display.',
                    'Hive models missing critical fields causing sync issues (foto_odometer, diproses_oleh, etc.).'
                ],
                'perubahan_teknikal' => [
                    'resources/views/log-pemandu/index.blade.php: Add refreshTableData() JavaScript function (10s interval).',
                    'DOMParser API: Parse HTML response tanpa full page reload untuk seamless table update.',
                    'app/Models/Kenderaan.php: Change orderBy(masa_masuk, desc) to orderBy(id, desc) di getLatestOdometerAttribute().',
                    'risda_driver_app/lib/models/journey_hive_model.dart: Add @HiveField(16-20) untuk new fields.',
                    'risda_driver_app/lib/models/claim_hive_model.dart: Add @HiveField(8-12) untuk approval workflow fields.',
                    'flutter pub run build_runner: Regenerate Hive adapters (.g.dart files) untuk new TypeAdapters.',
                    'JourneyHive.toJson(): Map catatan to keterangan untuk match MySQL column naming.',
                    'isRefreshing lock mechanism: Prevent race conditions during concurrent refresh attempts.',
                    'Preserve current URL params: Maintain tab, search, tarikh_dari, tarikh_hingga, page during auto-refresh.'
                ],
                'urutan' => 173,
                'is_latest' => false,
            ],
            [
                'versi' => '2.0.0',
                'nama_versi' => 'Support Ticketing System & Real-Time Collaboration',
                'jenis_keluaran' => 'green',
                'tarikh_keluaran' => Carbon::create(2025, 10, 6),
                'penerangan' => 'Kemaskini major dengan sistem tiket sokongan yang komprehensif, termasuk real-time chat, multi-user collaboration, dan mobile-responsive design yang canggih.',
                'ciri_baharu' => [
                    'Sistem Tiket Sokongan: Platform lengkap untuk pengurusan isu dan pertanyaan dengan ticket numbering',
                    'Real-time Chat: Mesej auto-update setiap 3 saat dengan notification sound',
                    'Multi-user Collaboration: Assign tiket kepada staff, tambah peserta untuk perbincangan kumpulan',
                    'Attachment Support: Upload dan preview fail (PDF, gambar, dokumen) dengan modal cantik',
                    'IP & Location Tracking: Setiap mesej direkod dengan IP address dan lokasi pengguna (Google Maps link)',
                    'Organization Badges: Paparan automatik nama stesen/bahagian dalam chat messages',
                    'Bell Notifications: Notifikasi real-time dengan bunyi untuk create, reply, assign, escalate, close',
                    'Activity Logging: Audit trail lengkap dengan IP untuk semua tindakan tiket',
                    'Export Chat History: Eksport perbincangan lengkap sebagai fail teks untuk arkib',
                    'Mobile Responsive: UI/UX optimized sepenuhnya - full-screen modals, stacked buttons, horizontal scroll tabs',
                    'Access Control: Multi-tenancy filtering untuk creator, assigned person, participants',
                    'Auto-assignment: Tiket Android auto-assign kepada staff yang pertama membuka',
                    'Escalation System: Staff boleh escalate tiket kritikal kepada administrator',
                    'Delete Verification: Padam tiket dengan sistem 6-digit kod pengesahan alphanumeric',
                    'Participant Management: Add/remove peserta dalam tiket untuk collaborative discussion',
                ],
                'penambahbaikan' => [
                    'Mobile view untuk permission matrix dalam tambah/edit kumpulan (checkbox labels appear conditionally)',
                    'Smart pagination dengan limited page numbers untuk better navigation experience',
                    'Breadcrumb navigation untuk halaman show laporan kenderaan',
                    'Adaptive activity log display berdasarkan jenis entity dengan rich details',
                    'Centralized CSS components untuk support tickets dengan mobile media queries',
                    'Modal footer buttons dengan adaptive grid layout untuk mobile (stack vertical)',
                    'Search & filter layout optimization - smart 2-column grid untuk mobile',
                    'Stats cards 2-column layout pada mobile untuk better space utilization',
                ],
                'pembetulan_pepijat' => [
                    'Fixed organization name accessor untuk stesen (nama_stesen) dan bahagian (nama_bahagian)',
                    'Fixed duplicate activity logging dengan disable model auto-logging trait',
                    'Fixed IP address field naming consistency (ip vs ip_address) across activity logs',
                    'Fixed notification logic untuk ensure administrators selalu dapat notification dari staff tickets',
                    'Fixed modal delete confirmation path untuk support tickets',
                    'Fixed reply notification untuk include all administrators bila ticket dari staff',
                    'Fixed circular dependency errors dalam mobile CSS dengan remove @apply',
                    'Fixed canBeAccessedBy logic untuk unassigned Android tickets dalam same organization',
                ],
                'perubahan_teknikal' => [
                    'Database: Created support_tickets, support_messages, support_ticket_participants tables',
                    'Models: SupportTicket with relationships (creator, assignedAdmin, participants, messages)',
                    'API: Internal /api/users/list endpoint untuk filtered user selection',
                    'Services: IP geolocation via ip-api.com dengan graceful fallback untuk localhost',
                    'CSS Architecture: Separated mobile-specific styles dalam support-tickets-mobile.css',
                    'JavaScript: Real-time polling mechanism dengan auto-cleanup on modal close',
                    'Permissions: Added sokongan module dengan granular actions (tambah, lihat, balas, tugaskan, tutup, padam)',
                ],
                'urutan' => 200,
                'is_latest' => true,
            ],
        ];

        foreach ($notaKeluarans as $nota) {
            NotaKeluaran::updateOrCreate(
                ['versi' => $nota['versi']],
                [
                    'nama_versi' => $nota['nama_versi'],
                    'jenis_keluaran' => $nota['jenis_keluaran'],
                    'tarikh_keluaran' => $nota['tarikh_keluaran'],
                    'penerangan' => $nota['penerangan'],
                    'ciri_baharu' => $nota['ciri_baharu'],
                    'penambahbaikan' => $nota['penambahbaikan'],
                    'pembetulan_pepijat' => $nota['pembetulan_pepijat'],
                    'perubahan_teknikal' => $nota['perubahan_teknikal'],
                    'status' => 'published',
                    'is_latest' => $nota['is_latest'] ?? false,
                    'urutan' => $nota['urutan'],
                    'dicipta_oleh' => $admin->id,
                    'dikemaskini_oleh' => $admin->id,
                ]
            );
        }

        NotaKeluaran::where('versi', '!=', '2.0.0')->update(['is_latest' => false]);
    }
}

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

        NotaKeluaran::where('versi', '!=', '1.5.0')->update(['is_latest' => false]);
    }
}

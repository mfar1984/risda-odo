{{-- POLICY MODAL: Peta Laman --}}

<div x-show="sitemapModal"
     x-cloak
     @keydown.escape.window="sitemapModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="sitemapModal = false"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col" @click.away="sitemapModal = false">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">map</span>
                    <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Peta Laman</h3>
                </div>
                <button @click="sitemapModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-6" style="max-height: calc(85vh - 140px); font-family: Poppins, sans-serif !important;">
                <p class="text-gray-700" style="font-size: 12px !important;">Semua pautan utama sistem disenaraikan di bawah sebagai URL mutlak menggunakan domain <strong>https://jara.my</strong>, kecuali pautan khusus yang dinyatakan. Setiap pautan disertakan keterangan ringkas tujuannya. Akses ke sesetengah pautan tertakluk kepada kebenaran peranan.</p>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">A. Umum & Navigasi</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/" class="text-blue-700 underline">https://jara.my/</a> — Laman Utama (Welcome).</li>
                        <li><a href="https://jara.my/dashboard" class="text-blue-700 underline">https://jara.my/dashboard</a> — Papan Pemuka ringkas selepas log masuk.</li>
                        <li><a href="https://jara.my/profile" class="text-blue-700 underline">https://jara.my/profile</a> — Profil pengguna (lihat/kemaskini).</li>
                        <li><a href="https://jara.my/settings" class="text-blue-700 underline">https://jara.my/settings</a> — Tetapan aplikasi (paparan tetapan umum pengguna).</li>
                        <li><a href="https://jara.my/notifications" class="text-blue-700 underline">https://jara.my/notifications</a> — Senarai notifikasi (loceng notifikasi di topbar).</li>
                        <li><span class="text-gray-800">POST https://jara.my/notifications/{id}/mark-as-read</span> — Tandakan satu notifikasi sebagai telah dibaca.</li>
                        <li><span class="text-gray-800">POST https://jara.my/notifications/mark-all-as-read</span> — Tandakan semua notifikasi sebagai telah dibaca.</li>
                        <li><span class="text-gray-800">Parameter penomboran (?page=)</span> — Digunakan pada senarai untuk navigasi halaman.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">B. Program</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/program" class="text-blue-700 underline">https://jara.my/program</a> — Senarai Program (carian, penapisan, navigasi).</li>
                        <li><a href="https://jara.my/program/tambah-program" class="text-blue-700 underline">https://jara.my/program/tambah-program</a> — Cipta/Tambah Program baharu.</li>
                        <li><span class="text-gray-800">POST https://jara.my/program/tambah-program</span> — Simpan Program baharu.</li>
                        <li><span class="text-gray-800">https://jara.my/program/{id}</span> — Butiran Program (lihat semua maklumat program).</li>
                        <li><span class="text-gray-800">https://jara.my/program/{id}/edit</span> — Kemaskini Program.</li>
                        <li><span class="text-gray-800">PUT https://jara.my/program/{id}</span> — Simpan kemaskini Program.</li>
                        <li><span class="text-gray-800">PATCH https://jara.my/program/{id}/approve</span> — Lulus Program.</li>
                        <li><span class="text-gray-800">PATCH https://jara.my/program/{id}/reject</span> — Tolak Program.</li>
                        <li><span class="text-gray-800">POST https://jara.my/program/{id}/log-export</span> — Eksport Log Program (fail).</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/program/{id}</span> — Padam Program.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">C. Log Pemandu</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/log-pemandu" class="text-blue-700 underline">https://jara.my/log-pemandu</a> — Senarai log perjalanan pemandu.</li>
                        <li><span class="text-gray-800">https://jara.my/log-pemandu/{id}</span> — Butiran Log Pemandu.</li>
                        <li><span class="text-gray-800">https://jara.my/log-pemandu/{id}/edit</span> — Kemaskini Log Pemandu.</li>
                        <li><span class="text-gray-800">PUT https://jara.my/log-pemandu/{id}</span> — Simpan kemaskini log.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/log-pemandu/{id}</span> — Padam log.</li>
                        <li><span class="text-gray-800">GET https://jara.my/log-pemandu/tab-counts</span> — API dalaman kiraan tab.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">D. Laporan</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/laporan/senarai-program" class="text-blue-700 underline">https://jara.my/laporan/senarai-program</a> — Laporan Senarai Program.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/senarai-program/{programId}</span> — Butiran program (laporan).</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/senarai-program/{programId}/pdf</span> — Eksport PDF.</li>
                        <li><a href="https://jara.my/laporan/laporan-kenderaan" class="text-blue-700 underline">https://jara.my/laporan/laporan-kenderaan</a> — Laporan Kenderaan.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kenderaan/{kenderaanId}</span> — Butiran kenderaan (laporan).</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kenderaan/{kenderaanId}/pdf</span> — Eksport PDF.</li>
                        <li><a href="https://jara.my/laporan/laporan-kilometer" class="text-blue-700 underline">https://jara.my/laporan/laporan-kilometer</a> — Laporan Kilometer.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kilometer/{programId}</span> — Butiran kilometer.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kilometer/{programId}/pdf</span> — Eksport PDF.</li>
                        <li><a href="https://jara.my/laporan/laporan-kos" class="text-blue-700 underline">https://jara.my/laporan/laporan-kos</a> — Laporan Kos.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kos/{programId}</span> — Butiran kos program.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-kos/{programId}/pdf</span> — Eksport PDF.</li>
                        <li><a href="https://jara.my/laporan/laporan-pemandu" class="text-blue-700 underline">https://jara.my/laporan/laporan-pemandu</a> — Laporan Pemandu.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-pemandu/{driverId}</span> — Butiran pemandu.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-pemandu/{driverId}/pdf</span> — Eksport PDF.</li>
                        <li><a href="https://jara.my/laporan/laporan-tuntutan" class="text-blue-700 underline">https://jara.my/laporan/laporan-tuntutan</a> — Laporan Tuntutan.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-tuntutan/{tuntutanId}</span> — Butiran tuntutan.</li>
                        <li><span class="text-gray-800">https://jara.my/laporan/laporan-tuntutan/export/pdf</span> — Eksport PDF semua/terpilih.</li>
                        <li><span class="text-gray-800">POST https://jara.my/laporan/laporan-tuntutan/{tuntutanId}/approve</span> — Lulus tuntutan.</li>
                        <li><span class="text-gray-800">POST https://jara.my/laporan/laporan-tuntutan/{tuntutanId}/reject</span> — Tolak tuntutan.</li>
                        <li><span class="text-gray-800">POST https://jara.my/laporan/laporan-tuntutan/{tuntutanId}/cancel</span> — Gantung/Batal tuntutan.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/laporan/laporan-tuntutan/{tuntutanId}</span> — Padam tuntutan.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">E. Pengurusan</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/pengurusan/tetapan-umum" class="text-blue-700 underline">https://jara.my/pengurusan/tetapan-umum</a> — Tetapan umum organisasi/stesen.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-risda" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-risda</a> — Senarai RISDA (Bahagian/Stesen/Staf).</li>
                        <li><a href="https://jara.my/pengurusan/senarai-risda/tambah-bahagian" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-risda/tambah-bahagian</a> — Tambah Bahagian.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-risda/tambah-stesen" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-risda/tambah-stesen</a> — Tambah Stesen.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/stesen/{id}</span> — Butiran Stesen.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/stesen/{id}/edit</span> — Kemaskini Stesen.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-risda/tambah-staf" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-risda/tambah-staf</a> — Tambah Staf.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/staf/{id}</span> — Butiran Staf.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/staf/{id}/edit</span> — Kemaskini Staf.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/{bahagianId}</span> — Butiran Bahagian.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-risda/{bahagianId}/edit</span> — Kemaskini Bahagian.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-kumpulan" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-kumpulan</a> — Senarai Kumpulan (Roles).</li>
                        <li><a href="https://jara.my/pengurusan/senarai-kumpulan/tambah-kumpulan" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-kumpulan/tambah-kumpulan</a> — Tambah Kumpulan.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-kumpulan/{id}</span> — Butiran Kumpulan.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-kumpulan/{id}/edit</span> — Kemaskini Kumpulan.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/pengurusan/senarai-kumpulan/{id}</span> — Padam Kumpulan.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-pengguna" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-pengguna</a> — Senarai Pengguna.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-pengguna/tambah-pengguna" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-pengguna/tambah-pengguna</a> — Tambah Pengguna.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-pengguna/{id}</span> — Butiran Pengguna.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-pengguna/{id}/edit</span> — Kemaskini Pengguna.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/pengurusan/senarai-pengguna/{id}</span> — Padam Pengguna.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-kenderaan" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-kenderaan</a> — Senarai Kenderaan.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-kenderaan/tambah-kenderaan" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-kenderaan/tambah-kenderaan</a> — Tambah Kenderaan.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-kenderaan/{id}</span> — Butiran Kenderaan.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-kenderaan/{id}/edit</span> — Kemaskini Kenderaan.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/pengurusan/senarai-kenderaan/{id}</span> — Padam Kenderaan.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-selenggara" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-selenggara</a> — Senarai Selenggara Kenderaan.</li>
                        <li><a href="https://jara.my/pengurusan/senarai-selenggara/tambah-selenggara" class="text-blue-700 underline">https://jara.my/pengurusan/senarai-selenggara/tambah-selenggara</a> — Tambah rekod selenggara.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-selenggara/{id}</span> — Butiran selenggara.</li>
                        <li><span class="text-gray-800">https://jara.my/pengurusan/senarai-selenggara/{id}/edit</span> — Kemaskini selenggara.</li>
                        <li><span class="text-gray-800">POST https://jara.my/pengurusan/kategori-kos-selenggara</span> — Tambah kategori kos selenggara.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/pengurusan/kategori-kos-selenggara/{kategoriId}</span> — Padam kategori kos selenggara.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/pengurusan/senarai-selenggara/{id}</span> — Padam rekod selenggara.</li>
                        <li><a href="https://jara.my/pengurusan/integrasi" class="text-blue-700 underline">https://jara.my/pengurusan/integrasi</a> — Halaman integrasi (API token, CORS, e‑mel, cuaca).</li>
                        <li><span class="text-gray-800">POST https://jara.my/pengurusan/integrasi/generate-api-token</span> — Jana token API baharu.</li>
                        <li><span class="text-gray-800">PUT https://jara.my/pengurusan/integrasi/cors</span> — Kemaskini konfigurasi CORS.</li>
                        <li><span class="text-gray-800">PUT https://jara.my/pengurusan/integrasi/cuaca</span> — Kemaskini tetapan cuaca.</li>
                        <li><span class="text-gray-800">PUT https://jara.my/pengurusan/integrasi/email</span> — Kemaskini tetapan e‑mel.</li>
                        <li><a href="https://jara.my/pengurusan/aktiviti-log" class="text-blue-700 underline">https://jara.my/pengurusan/aktiviti-log</a> — Aktiviti Log sistem.</li>
                        
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">F. Bantuan & Sokongan</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><a href="https://jara.my/help/panduan-pengguna" class="text-blue-700 underline">https://jara.my/help/panduan-pengguna</a> — Panduan pengguna sistem.</li>
                        <li><a href="https://jara.my/help/faq" class="text-blue-700 underline">https://jara.my/help/faq</a> — Soalan Lazim (FAQ).</li>
                        <li><a href="https://jara.my/help/hubungi-sokongan" class="text-blue-700 underline">https://jara.my/help/hubungi-sokongan</a> — Hubungi Sokongan / Tiket.</li>
                        <li><span class="text-gray-800">https://jara.my/help/tickets/{id}</span> — Butiran tiket sokongan.</li>
                        <li><span class="text-gray-800">https://jara.my/help/tickets/{id}/export</span> — Eksport tiket (PDF/arkib).</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets</span> — Cipta tiket sokongan baharu.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/reply</span> — Hantar balasan dalam tiket.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/assign</span> — Tugaskan tiket kepada pengguna.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/participants</span> — Tambah peserta tiket.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/help/tickets/{id}/participants/{userId}</span> — Buang peserta tiket.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/typing</span> — Kemaskini status menaip.</li>
                        <li><span class="text-gray-800">GET https://jara.my/help/tickets/{id}/typing</span> — Dapatkan status menaip semasa.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/escalate</span> — Eskalasi tiket.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/close</span> — Tutup tiket.</li>
                        <li><span class="text-gray-800">POST https://jara.my/help/tickets/{id}/reopen</span> — Buka semula tiket.</li>
                        <li><span class="text-gray-800">DELETE https://jara.my/help/tickets/{id}</span> — Padam tiket.</li>
                        <li><a href="https://jara.my/help/status-sistem" class="text-blue-700 underline">https://jara.my/help/status-sistem</a> — Status sistem.</li>
                        <li><a href="https://jara.my/help/nota-keluaran" class="text-blue-700 underline">https://jara.my/help/nota-keluaran</a> — Nota keluaran / changelog.</li>
                        <li><a href="https://jara.my/help/api-doc" class="text-blue-700 underline">https://jara.my/help/api-doc</a> — Dokumentasi API.</li>
                        <li><span class="text-gray-800">https://jara.my/help/api-doc/{module}/{endpoint}</span> — Butiran endpoint API.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">G. API Dalaman (AJAX & Utiliti)</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li><span class="text-gray-800">GET https://jara.my/log-pemandu/tab-counts</span> — Kiraan tab untuk paparan Log Pemandu.</li>
                        <li><span class="text-gray-800">GET https://jara.my/pengurusan/senarai-pengguna/get-stesen/{bahagianId}</span> — Senarai stesen mengikut bahagian.</li>
                        <li><span class="text-gray-800">GET https://jara.my/pengurusan/senarai-pengguna/get-all-stesen</span> — Senarai semua stesen.</li>
                        <li><span class="text-gray-800">GET https://jara.my/api/users/list</span> — Senarai pengguna (dalaman).</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">H. Dasar & Polisi (Modal)</h4>
                    <ul class="list-disc ml-5 space-y-1 text-gray-700" style="font-size: 12px !important;">
                        <li>Penafian — Dibuka sebagai modal dari footer.</li>
                        <li>Privasi — Dibuka sebagai modal dari footer.</li>
                        <li>Terma Penggunaan — Dibuka sebagai modal dari footer.</li>
                        <li>Peta Laman — Modal ini.</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button @click="sitemapModal = false" type="button" class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" style="font-family: Poppins, sans-serif !important;">Tutup</button>
            </div>
        </div>
    </div>
</div>



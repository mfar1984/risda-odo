{{-- POLICY MODAL: Terma Penggunaan --}}

<div x-show="termsModal"
     x-cloak
     @keydown.escape.window="termsModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="termsModal = false"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col" @click.away="termsModal = false">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">description</span>
                    <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Terma Penggunaan</h3>
                </div>
                <button @click="termsModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-5" style="max-height: calc(85vh - 140px); font-family: Poppins, sans-serif !important;">
                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">1. Pengenalan & Penerimaan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Terma Penggunaan ini mengawal selia akses dan penggunaan Sistem <strong>JARA — Jejak Aset & Rekod Automatif</strong> ("Sistem", "Aplikasi" atau "JARA"). Dengan mengakses atau menggunakan JARA, anda mengakui bahawa anda telah membaca, memahami, dan bersetuju untuk terikat dengan Terma ini, serta dasar dan garis panduan lain yang berkaitan (termasuk Polisi Privasi). Jika anda tidak bersetuju dengan mana‑mana bahagian Terma ini, anda hendaklah berhenti menggunakan Sistem. JARA dibangunkan untuk menyokong operasi Pejabat RISDA Bahagian Sibu, termasuk pengurusan log perjalanan pemandu, rekod tuntutan, dan pelaporan berkaitan aset/kenderaan secara <em>offline‑first</em>.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">2. Definisi</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li><strong>Pengguna</strong>: Individu yang diberi kebenaran untuk menggunakan JARA (contoh: pemandu, penyelia, pentadbir).</li>
                        <li><strong>Akaun</strong>: Kredensial log masuk dan profil yang berkaitan dengan identiti pengguna.</li>
                        <li><strong>Data</strong>: Segala maklumat yang direkodkan melalui JARA termasuk log perjalanan, tuntutan, media, metadata teknikal, dan konfigurasi.</li>
                        <li><strong>Peranti</strong>: Telefon pintar atau tablet yang memasang aplikasi mudah alih JARA.</li>
                        <li><strong>Pelayan</strong>: Infrastruktur backend yang menghoskan API, storan, dan fungsi pentadbiran.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">3. Skop & Tujuan Sistem</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">JARA direka untuk pengurusan operasi perjalanan dan rekod berkaitan aset. Ciri utama merangkumi mula/tamat perjalanan (check‑in/check‑out), semakan odometer secara manual dengan bukti foto, simpanan dan penyelarasan data <em>offline‑first</em>, pengurusan tuntutan, paparan laporan (Kenderaan, Kos, Pemandu), notifikasi tugasan, serta modul sokongan. JARA bukan aplikasi pengguna awam; ia terhad kepada pengguna yang dilantik dan tertakluk kepada kebenaran serta dasar keselamatan organisasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">4. Akaun, Akses & Keselamatan</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Anda bertanggungjawab ke atas kerahsiaan kredensial log masuk dan keselamatan peranti.</li>
                        <li>Dilarang berkongsi akaun. Aktiviti yang berlaku di bawah akaun anda dianggap dilakukan oleh anda.</li>
                        <li>Pentadbir berhak menggantung atau menamatkan akses jika dikesan pelanggaran keselamatan atau dasar.</li>
                        <li>Semasa log keluar, aplikasi akan mengosongkan storan tempatan bagi mencegah pencemaran data antara akaun.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">5. Penggunaan Yang Dibenarkan & Larangan</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Gunakan JARA hanya untuk tujuan kerja yang dibenarkan oleh organisasi.</li>
                        <li>Dilarang memanipulasi, mengubah, menggodam, menyahsulit atau mengakses data tanpa kebenaran.</li>
                        <li>Dilarang memasukkan data palsu/menyesatkan (contoh: bacaan odometer yang tidak benar).</li>
                        <li>Dilarang menyalahguna media yang dimuat naik (contoh: gambar odometer/resit).</li>
                        <li>Dilarang menyalin, mengedar atau membuat turunan aplikasi tanpa kelulusan bertulis.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">6. Mod Offline‑First & Penyelarasan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">JARA menyokong operasi tanpa talian (offline). Rekod baharu/kemaskini akan disimpan pada peranti dan diselaraskan ke pelayan apabila talian dipulihkan. Penyelarasan dilakukan secara berperingkat, dengan kaedah kawalan konflik yang memelihara integriti rekod (contoh: status perjalanan aktif tunggal, pengesahan nilai odometer tidak menurun, dan penentuan keutamaan data yang disahkan pelayan).</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">7. Ketepatan Data & Odometer</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Anda wajib memastikan semua maklumat yang dimasukkan adalah tepat dan semasa. Bacaan odometer hendaklah dimasukkan secara manual berdasarkan pemerhatian sebenar dan disokong dengan gambar yang jelas. Sistem tidak melakukan auto‑prefill; walau bagaimanapun, ia mungkin memaparkan cadangan semakan bagi mengelakkan bacaan menurun. Jika bacaan didapati tidak munasabah, pentadbir boleh menolak rekod atau memerlukan pembetulan dengan bukti tambahan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">8. Media, Bukti & Fail</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Gambar odometer/resit diperlukan sebagai bukti audit dan pengesahan tuntutan.</li>
                        <li>Anda hendaklah memastikan media yang dimuat naik adalah milik sah dan tidak melanggar hak pihak ketiga.</li>
                        <li>Kualiti/kejelasan media adalah tanggungjawab pengguna; rekod kabur boleh ditolak.</li>
                        <li>Media boleh disimpan pada peranti (sementara) dan disalin ke pelayan semasa penyelarasan.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">9. Notifikasi & Komunikasi</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">JARA menggunakan notifikasi (contoh: <em>Firebase Cloud Messaging</em>) untuk memaklumkan tugasan baharu, status rekod, atau pengumuman penting. Anda bersetuju untuk menerima notifikasi yang berkaitan dengan tugas dan peranan anda. Token peranti digunakan bagi tujuan ini dan diproses mengikut Polisi Privasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">10. Hak & Tanggungjawab Pengguna</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Mengemas kini rekod dengan betul dan mematuhi arahan kerja.</li>
                        <li>Menjaga kerahsiaan akaun dan peranti serta melaporkan insiden keselamatan dengan segera.</li>
                        <li>Mematuhi larangan manipulasi data, cubaan godaman, atau perlanggaran hak cipta.</li>
                        <li>Berkerjasama dalam siasatan audit atau semakan dalaman yang berkaitan penggunaan anda.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">11. Hak & Tanggungjawab Pentadbir</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Menetapkan peranan/kebenaran dan mengurus akses pengguna.</li>
                        <li>Melakukan penyelenggaraan, kemas kini, dan penambahbaikan sistem.</li>
                        <li>Menggantung/menamatkan akses jika terdapat pelanggaran atau risiko keselamatan.</li>
                        <li>Melaksanakan audit dan kawalan integriti data mengikut dasar organisasi.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">12. Hak Cipta & Harta Intelek</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Semua kandungan, kod, reka bentuk UI, logo, dan bahan dalam JARA adalah milik organisasi dan/atau pemberi lesen. Anda tidak dibenarkan menyalin, mengubah suai, menerbit, mengedar, atau menghasilkan karya terbitan tanpa kebenaran bertulis. Sebarang maklum balas atau cadangan yang anda serahkan boleh digunakan bagi tujuan penambahbaikan tanpa obligasi pampasan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">13. Integrasi Pihak Ketiga</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">JARA mungkin berintegrasi dengan perkhidmatan pihak ketiga (contoh: FCM untuk notifikasi). Penggunaan ciri tersebut tertakluk kepada terma pihak ketiga berkenaan. Kami berusaha mengehadkan data yang dihantar kepada maklumat minimum yang diperlukan, dan pemprosesan akan mengikut Polisi Privasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">14. Privasi & Perlindungan Data</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Penggunaan JARA tertakluk kepada Polisi Privasi berkuat kuasa. Sila rujuk polisi tersebut untuk perincian mengenai kategori data, tujuan pemprosesan, langkah keselamatan, hak subjek data, dan pengekalan rekod. Dengan menggunakan Sistem, anda bersetuju bahawa data anda akan diproses sebagaimana dihuraikan dalam Polisi Privasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">15. Keselamatan Aplikasi & Peranti</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Kami melaksanakan kawalan peranan, token akses, dan log audit untuk keselamatan.</li>
                        <li>Komunikasi rangkaian menggunakan HTTPS; data offline disimpan dalam storan peranti.</li>
                        <li>Anda hendaklah mengekalkan integriti peranti (contoh: elakkan <em>jailbreak</em>/<em>root</em>).</li>
                        <li>Tiada sistem yang 100% selamat; risiko baki sentiasa wujud dan pengguna perlu berwaspada.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">16. Penggantungan, Penamatan & Pengehadan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Kami boleh menggantung atau menamatkan akses anda pada bila‑bila masa jika terdapat pelanggaran Terma, risiko keselamatan, arahan pihak berkuasa, atau keperluan operasi. Sekatan sementara boleh dikenakan bagi tujuan penyelenggaraan atau siasatan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">17. Penyelenggaraan, Kemas Kini & Perubahan Ciri</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Kami berhak mengubah, menambah, atau mengalih keluar mana‑mana ciri tanpa notis terdahulu bagi meningkatkan prestasi, keselamatan, dan pematuhan. Ketersediaan sementara mungkin terjejas semasa penyelenggaraan. Anda dinasihatkan menggunakan versi aplikasi terkini.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">18. Penafian Jaminan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Sistem disediakan "seadanya" (<em>as is</em>) dan "seperti tersedia" (<em>as available</em>) tanpa jaminan tersurat atau tersirat, termasuk tetapi tidak terhad kepada kebolehdagangan, kesesuaian untuk tujuan tertentu, dan ketiadaan pelanggaran. Kami tidak menjamin bahawa JARA akan bebas ralat, bebas gangguan, atau memenuhi semua jangkaan khusus pengguna. Penggunaan adalah atas risiko pengguna sendiri.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">19. Had Tanggungan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Setakat yang dibenarkan undang‑undang, kami tidak akan menanggung liabiliti bagi sebarang kerosakan tidak langsung, khas, sampingan, turutan, penalti atau kehilangan data/keuntungan yang timbul daripada penggunaan atau ketidakupayaan menggunakan JARA, walaupun telah dimaklumkan tentang kemungkinan tersebut. Tanggungan agregat kami (jika ada) adalah terhad kepada had yang dibenarkan dasar organisasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">20. Ganti Rugi</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Anda bersetuju untuk mempertahankan, menanggung rugi, dan melepaskan kami, pegawai, pekerja, ejen dan pemberi lesen daripada sebarang tuntutan, liabiliti, kerugian, atau kos (termasuk yuran guaman munasabah) yang timbul akibat penggunaan JARA yang melanggar Terma ini, undang‑undang, atau hak pihak ketiga.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">21. Keadaan Luar Jangka (Force Majeure)</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Kami tidak bertanggungjawab atas kelewatan atau kegagalan prestasi akibat kejadian di luar kawalan munasabah seperti bencana alam, gangguan utiliti, peperangan, rusuhan, tindakan pihak berkuasa, kegagalan rangkaian pihak ketiga, atau perisian/perkakasan pihak ketiga.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">22. Undang‑Undang Terpakai & Bidang Kuasa</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Terma ini ditadbir oleh undang‑undang Malaysia. Sebarang pertikaian yang tidak dapat diselesaikan secara rundingan akan tertakluk kepada bidang kuasa eksklusif mahkamah yang kompeten di Malaysia, tertakluk kepada tatacara dalaman organisasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">23. Penyelesaian Pertikaian</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Sekiranya timbul pertikaian, pihak‑pihak hendaklah terlebih dahulu berusaha menyelesaikannya melalui rundingan dalaman dengan pentadbir yang berkaitan. Jika perlu, mekanisme mediasi atau tatacara disiplin organisasi akan digunakan sebelum tindakan undang‑undang dipertimbangkan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">24. Kebolehasingan & Keseluruhan Perjanjian</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Jika mana‑mana peruntukan Terma ini didapati tidak sah atau tidak boleh dikuatkuasakan, peruntukan tersebut akan dihadkan atau dibuang setakat minimum yang perlu, dan peruntukan selebihnya kekal berkuat kuasa. Terma ini bersama Polisi Privasi membentuk keseluruhan perjanjian antara anda dan kami berkaitan penggunaan JARA.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">25. Perubahan Terma</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Kami boleh mengemas kini Terma ini dari semasa ke semasa. Versi terkini akan diterbitkan dalam Sistem dan berkuat kuasa sebaik diterbitkan. Dengan meneruskan penggunaan selepas perubahan, anda dianggap menerima Terma yang dipinda.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">26. Hubungi Kami</h4>
                    <div class="text-gray-800" style="font-size: 12px !important;">
                        Pejabat RISDA Bahagian Sibu<br>
                        No 49 Lorong 51, Jalan Lau King Howe, 96000 Sibu, Sarawak<br>
                        E‑mel: <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a><br>
                        Tel: 084‑344712 / 084‑344713 &nbsp; | &nbsp; Faks: 084‑322531
                    </div>
                    <p class="text-gray-700 mt-2" style="font-size: 12px !important;">Sokongan JARA: <a href="mailto:administrator@jara.my" class="text-blue-700 underline">administrator@jara.my</a>, <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a></p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">27. Tarikh Kuat Kuasa</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Terma ini berkuat kuasa mulai tarikh diterbitkan dan menggantikan semua versi terdahulu.</p>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button @click="termsModal = false" type="button" class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" style="font-family: Poppins, sans-serif !important;">Tutup</button>
            </div>
        </div>
    </div>
</div>



{{-- POLICY MODAL: Penafian --}}

<div x-show="disclaimerModal"
     x-cloak
     @keydown.escape.window="disclaimerModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="disclaimerModal = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col"
             @click.away="disclaimerModal = false">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">info</span>
                    <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penafian</h3>
                </div>
                <button @click="disclaimerModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 space-y-5" style="max-height: calc(85vh - 140px); font-family: Poppins, sans-serif !important;">
                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">1. Pengenalan & Skop</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Dokumen Penafian ini menghuraikan had tanggungjawab, skop penggunaan dan batasan fungsi
                        Sistem <strong>JARA — Jejak Aset & Rekod Automatif</strong> ("Sistem", "Aplikasi" atau "JARA").
                        JARA merupakan sistem rekod perjalanan pemandu, pengurusan odometer, tuntutan berkaitan
                        perjalanan dan modul sokongan yang beroperasi sebagai aplikasi mudah alih (Flutter) dan papan
                        pemuka web (Laravel) dengan mod <em>offline-first</em>. Sistem ini dimiliki dan diselenggara untuk
                        kegunaan <strong>Pejabat RISDA Bahagian Sibu</strong> ("Pemilik"), beralamat di:
                    </p>
                    <div class="mt-3 text-gray-800" style="font-size: 12px !important;">
                        Pejabat RISDA Bahagian Sibu<br>
                        No 49 Lorong 51, Jalan Lau King Howe,<br>
                        96000 Sibu, Sarawak<br>
                        E-mel: <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a><br>
                        Tel: 084-344712 / 084-344713 &nbsp; | &nbsp; Faks: 084-322531
                    </div>
                    <p class="text-gray-700 mt-3" style="font-size: 12px !important;">
                        Untuk sebarang urusan teknikal khusus JARA, pengguna juga boleh menghubungi pentadbir sistem di
                        <a href="mailto:administrator@jara.my" class="text-blue-700 underline">administrator@jara.my</a>
                        atau <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a>.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">2. Sifat Sistem & Had Tanggungjawab</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        JARA ialah platform bantuan operasi yang memudahkan pengumpulan maklumat perjalanan, rekod
                        odometer (melalui input pengguna bergambar), penyegerakan data ke pelayan, dan penjanaan
                        laporan. Walau bagaimanapun, Sistem ini <strong>tidak</strong> menggantikan prosedur, pekeliling, garis
                        panduan, atau kuasa melulus yang ditetapkan oleh Pemilik. Semua keputusan pentadbiran,
                        kewangan, dan penguatkuasaan hendaklah merujuk dasar rasmi semasa. Pemilik dan pembangun
                        Sistem <strong>tidak bertanggungjawab</strong> terhadap sebarang kerugian, kehilangan data, kelewatan,
                        salah maklumat atau implikasi lain yang timbul daripada penggunaan Sistem ini, sama ada secara
                        langsung atau tidak langsung.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">3. Mod Offline‑First & Keselarasan Data</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        JARA direka bentuk untuk berfungsi tanpa rangkaian (mod offline). Rekod dimasukkan ke dalam
                        storan peranti (<em>Hive</em>) dan disegerak semula apabila sambungan Internet dipulihkan. Dalam mod ini:
                    </p>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Pengguna hendaklah memastikan semua isian adalah tepat dan lengkap. Sebarang input yang
                            tidak tepat (contoh: bacaan odometer, masa, atau gambar) akan disimpan di peranti dan boleh
                            menjejaskan integriti data apabila disegerak.</li>
                        <li>Penyegerakan akan cuba mengekalkan kesinambungan odometer (<em>monotonic</em>). Jika sistem
                            mengesan konflik (contoh: bacaan menurun), rekod boleh ditolak atau memerlukan semakan
                            manual oleh pentadbir.</li>
                        <li>Wujud kemungkinan ketidakselarasan sementara antara paparan mudah alih dan pelayan jika
                            penyegerakan tertangguh. Pengguna disaran menyemak status penyegerakan sebelum membuat
                            keputusan operasi yang kritikal.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">4. Ketepatan Odometer & Bukti Bergambar</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Bacaan odometer <strong>mesti</strong> dimasukkan oleh pemandu berdasarkan bacaan sebenar pada kenderaan
                        dan disokong oleh foto odometer. Sistem tidak melakukan <em>auto‑prefill</em> bacaan dan hanya
                        menggunakan cadangan (jika wujud) untuk tujuan semakan logik. Foto yang dimuat naik menjadi
                        sebahagian daripada rekod dan boleh digunakan sebagai bukti audit dalaman. Pengguna bertanggungjawab
                        memastikan gambar adalah jelas, tidak dimanipulasi, dan mematuhi garis panduan data peribadi.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">5. Lokasi, Notifikasi & Perkhidmatan Pihak Ketiga</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Aplikasi mungkin menggunakan GPS/perkhidmatan lokasi (untuk menyokong ketepatan rekod),
                        Firebase Cloud Messaging (untuk notifikasi), dan pustaka lain pihak ketiga. Ketepatan lokasi
                        dipengaruhi perkakasan peranti, tetapan, dan keadaan persekitaran. Notifikasi mungkin tertangguh
                        atau gagal dihantar akibat sekatan rangkaian atau tetapan peranti. Setiap perkhidmatan pihak ketiga
                        tertakluk kepada terma dan polisi mereka; Pemilik tidak mengawal ketersediaan atau perubahan
                        perkhidmatan tersebut.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">6. Keselamatan, Akses & Tanggungjawab Pengguna</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Pengguna hendaklah melindungi kelayakan log masuk (ID/kata laluan) dan peranti yang
                            digunakan. Kehilangan peranti atau pendedahan kata laluan hendaklah dimaklumkan segera
                            kepada pentadbir.</li>
                        <li>JARA menggunakan kaedah penyulitan/keselamatan sewajarnya (termasuk token akses) namun
                            tiada sistem yang 100% bebas risiko. Pengguna memahami risiko siber yang wujud dalam
                            penggunaan aplikasi mudah alih.</li>
                        <li>Akses ke modul/rekod adalah berdasarkan kebenaran yang ditetapkan oleh pentadbir (contoh:
                            peranan pemandu, penyelia, pentadbir, dan sebagainya). Akses boleh diubah, digantung atau
                            ditarik balik tanpa notis jika didapati menyalahi dasar.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">7. Data, Privasi & Pemilikan Kandungan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Semua data yang dihantar atau tersimpan melalui JARA dianggap milik Pemilik. Pengumpulan,
                        pemprosesan, penyimpanan, dan pelupusan data hendaklah mematuhi undang‑undang/polisi dalaman
                        berkaitan privasi dan perlindungan data. Data yang dimuat naik oleh pengguna (contoh: gambar
                        odometer, resit bahan api, bukti tuntutan) akan diproses untuk tujuan operasi, pengesahan, audit,
                        pelaporan dan pematuhan. Pemilik berhak menjalankan pemprosesan data untuk keselamatan sistem
                        dan peningkatan kualiti perkhidmatan.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">8. Analitik, Log & Penjejakan Aktiviti</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Bagi tujuan keselamatan dan penyelesaian masalah, sistem mungkin merekod aktiviti tertentu
                        (seperti waktu log masuk, percubaan API, ralat, atau jejak penyegerakan). Analitik ini digunakan
                        untuk memantau prestasi, kapasiti storan, serta mengenal pasti corak operasi yang memerlukan
                        penambahbaikan. Rekod ini adalah rahsia dan tertakluk kepada kawalan akses yang ketat.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">9. Ketepatan Laporan & Pengiraan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Laporan yang dihasilkan (contoh: kenderaan, kos, pemandu) adalah berdasarkan data yang wujud
                        dalam Sistem pada waktu penjanaan. Sila ambil perhatian bahawa laporan mungkin berubah selepas
                        penyegerakan atau pembetulan data. Segala pengiraan (contoh: jumlah jarak, jumlah kos) adalah
                        anggaran berdasarkan input pengguna dan rekod yang diluluskan. Pengguna perlu menyemak serta
                        mengesahkan sebelum membuat keputusan rasmi.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">10. Pergantungan Peranti & Bateri</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Prestasi aplikasi bergantung kepada keadaan peranti (OS, storan, bateri, tetapan keselamatan,
                        kebenaran kamera/lokasi) dan rangkaian. Penggunaan GPS, kamera, dan penyegerakan latar boleh
                        meningkatkan penggunaan bateri. Pengguna bertanggungjawab memastikan peranti berada dalam
                        keadaan baik dan mempunyai sumber kuasa yang mencukupi, terutama ketika operasi lapangan.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">11. Perubahan, Penyelenggaraan & Ketersediaan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Pemilik berhak melakukan perubahan pada fungsi, antaramuka, dasar, atau kaedah operasi sistem
                        pada bila-bila masa tanpa notis, termasuk kerja penyelenggaraan berjadual atau kecemasan.
                        Akses ke sistem mungkin terhenti sementara. Pemilik tidak bertanggungjawab terhadap kehilangan
                        peluang, masa atau data akibat gangguan perkhidmatan yang munasabah.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">12. Konflik, Duplikasi & Resolusi</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Jika duplikasi rekod dikesan ketika penyegerakan, sistem akan cuba menggabung atau menolak
                            rekod mengikut logik keselamatan data (contoh: id tempatan berbanding id pelayan, cap masa,
                            status perjalanan).</li>
                        <li>Kes khas (contoh: mula perjalanan secara offline, tamat perjalanan offline kemudian online)
                            akan diurus sebagai rantaian berurutan (mula → tamat) apabila talian dipulihkan.</li>
                        <li>Pentadbir berhak menyemak, membetul dan meluluskan penyelesaian konflik mengikut dasar.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">13. Harta Intelek</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Hak cipta, tanda dagang, reka bentuk dan kod sumber Sistem adalah milik Pemilik atau pemberi
                        lesen yang berkaitan. Penggunaan, penggandaan, pengubahsuaian atau pengedaran kandungan tanpa
                        kebenaran bertulis adalah dilarang.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">14. Undang-undang Terpakai & Bidang Kuasa</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Penafian ini ditadbir oleh undang‑undang Malaysia. Sebarang pertikaian utama berkaitan penggunaan
                        Sistem hendaklah dirunding secara dalaman terlebih dahulu. Jika perlu, tindakan lanjut tertakluk kepada
                        bidang kuasa mahkamah yang sesuai di Malaysia, mengikut arahan Pemilik.
                    </p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">15. Pertanyaan & Sokongan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">
                        Untuk pertanyaan mengenai penafian atau penggunaan Sistem:
                    </p>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Pejabat RISDA Bahagian Sibu — E‑mel: <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a> | Tel: 084‑344712 / 084‑344713 | Faks: 084‑322531</li>
                        <li>JARA Pentadbir Sistem — E‑mel: <a href="mailto:administrator@jara.my" class="text-blue-700 underline">administrator@jara.my</a>, <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-gray-600" style="font-size: 11px !important;">
                        Dokumen ini mungkin dikemas kini dari semasa ke semasa selaras dengan keperluan operasi dan
                        pematuhan. Versi terkini akan menggantikan sebarang versi terdahulu.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button @click="disclaimerModal = false"
                        type="button"
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
                        style="font-family: Poppins, sans-serif !important;">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>



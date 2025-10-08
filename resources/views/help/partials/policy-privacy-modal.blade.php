{{-- POLICY MODAL: Privasi --}}

<div x-show="privacyModal"
     x-cloak
     @keydown.escape.window="privacyModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">

    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="privacyModal = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col"
             @click.away="privacyModal = false">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">shield_person</span>
                    <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Privasi</h3>
                </div>
                <button @click="privacyModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-5" style="max-height: calc(85vh - 140px); font-family: Poppins, sans-serif !important;">
                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">1. Pengenalan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Polisi Privasi ini menjelaskan kaedah kami mengumpul, menggunakan, menyimpan, mendedahkan dan melindungi data peribadi apabila anda menggunakan Sistem <strong>JARA — Jejak Aset & Rekod Automatif</strong> ("Sistem", "Aplikasi" atau "JARA"). JARA dimiliki dan dikendalikan untuk kegunaan <strong>Pejabat RISDA Bahagian Sibu</strong>, No 49 Lorong 51, Jalan Lau King Howe, 96000 Sibu, Sarawak. Pertanyaan boleh dibuat melalui e‑mel <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a> atau pentadbir JARA di <a href="mailto:administrator@jara.my" class="text-blue-700 underline">administrator@jara.my</a> dan <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a>. Dengan menggunakan Sistem ini, anda bersetuju dengan amalan yang dihuraikan di bawah.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">2. Definisi Ringkas</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li><strong>Data Peribadi</strong> bermaksud apa‑apa maklumat yang mengenal pasti individu secara langsung atau tidak langsung (contoh: nama, e‑mel, nombor telefon, lokasi, imej).</li>
                        <li><strong>Pemprosesan</strong> termasuk pengumpulan, rakaman, penyimpanan, penggunaan, pendedahan, pemindahan, analisis, dan pelupusan data peribadi.</li>
                        <li><strong>Peranti</strong> merujuk peranti mudah alih yang memasang aplikasi JARA.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">3. Kategori Data Yang Dikumpul</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li><strong>Maklumat Akaun</strong>: nama, e‑mel, ID pengguna, peranan, stesen/bahagian.</li>
                        <li><strong>Rekod Operasi</strong>: log perjalanan (program, kenderaan, masa keluar/masuk, odometer, jarak, kos minyak, stesen minyak, catatan), tuntutan (kategori, amaun, lampiran).</li>
                        <li><strong>Media</strong>: gambar odometer, resit bahan api, dan bahan sokongan tuntutan.</li>
                        <li><strong>Lokasi</strong>: koordinat GPS semasa mula/tamat perjalanan (jika diberi kebenaran).</li>
                        <li><strong>Teknikal</strong>: token peranti (FCM), cap masa penyegerakan, ralat aplikasi, versi aplikasi, jenis peranti.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">4. Sumber Data</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Data diperoleh terus daripada pengguna (melalui borang aplikasi), data yang dijana oleh sistem (contoh: status penyegerakan, token notifikasi), dan rekod sedia ada pada pelayan (contoh: senarai program/kenderaan yang ditugaskan). Dalam mod offline, data dimasukkan ke storan peranti dan disegerakkan apabila talian dipulihkan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">5. Tujuan Pemprosesan</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Pengurusan operasi perjalanan (mula/tamat) dan penyelarasan data dengan pelayan.</li>
                        <li>Pengurusan tuntutan berkaitan perjalanan.</li>
                        <li>Notifikasi operasi (contoh: tugasan program baharu, status log/tuntutan, makluman sistem).</li>
                        <li>Pelaporan dalaman dan analitik prestasi (kenderaan, kos, pemandu).</li>
                        <li>Sokongan pengguna (tiket sokongan, semakan aktiviti, penyelesaian isu teknikal).</li>
                        <li>Keselamatan, audit, pematuhan dan penambahbaikan ciri.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">6. Asas Pemprosesan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Pemprosesan dilakukan atas asas kepentingan sah operasi organisasi, pelaksanaan tugas rasmi, keperluan kontraktual perkhidmatan, pematuhan undang‑undang/dasar dalaman, dan persetujuan pengguna bagi data yang memerlukan kebenaran (contoh: lokasi/peranti/ kamera/ notifikasi).</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">7. Lokasi Data & Penyimpanan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Data disimpan secara tempatan di peranti (Hive) untuk operasi offline dan di pelayan organisasi apabila disegerak. Rekod lama yang telah diselaraskan boleh dipangkas mengikut polisi retention organisasi. Fail media disimpan pada storan pelayan yang dikawal.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">8. Keselamatan</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li>Token akses, kawalan peranan, dan audit aktiviti mengehadkan capaian.</li>
                        <li>Komunikasi rangkaian melalui HTTPS; data offline disimpan pada storan peranti yang dilindungi OS.</li>
                        <li>Pengguna bertanggungjawab menjaga peranti, kata laluan dan kebenaran aplikasi.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">9. Perkongsian Dengan Pihak Ketiga</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">JARA menggunakan perkhidmatan pihak ketiga terhad (contoh: Firebase Cloud Messaging untuk notifikasi). Data yang dihantar adalah minimum yang perlu (contoh: token peranti). Kami tidak menjual data peribadi. Pendedahan hanya berlaku mengikut keperluan operasi, undang‑undang, atau dengan kebenaran pengguna.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">10. Media & Lokasi</h4>
                    <ul class="list-disc ml-5 text-gray-700 space-y-1" style="font-size: 12px !important;">
                        <li><strong>Gambar Odometer/Resit</strong>: digunakan sebagai bukti audit dan pengesahan tuntutan.</li>
                        <li><strong>Lokasi GPS</strong>: dihimpun bagi menyokong konteks perjalanan (berdasarkan kebenaran peranti); ketepatan bergantung kepada perkakasan dan keadaan persekitaran.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">11. Hak Subjek Data</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Tertakluk kepada undang‑undang dan dasar operasi, anda boleh memohon akses, pembetulan, sekatan pemprosesan, pemadaman data tertentu, dan menarik balik persetujuan (lokasi/peranti/notifikasi) pada bila‑bila masa melalui tetapan peranti atau halaman profil. Permintaan boleh dihantar kepada <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a> atau <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a>.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">12. Pengekalan & Pelupusan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Data disimpan untuk tempoh yang perlu bagi tujuan operasi dan pematuhan. Data dalam peranti akan dikosongkan semasa log keluar (logout) untuk mengelakkan pencemaran antara akaun.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">13. Kanak‑kanak</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Sistem ini ditujukan kepada kakitangan/kontraktor yang diberi kebenaran sahaja dan tidak bertujuan untuk digunakan oleh kanak‑kanak.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">14. Pemindahan Rentas Sempadan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Jika terdapat penggunaan perkhidmatan awan/pihak ketiga di luar Malaysia, langkah perlindungan sewajarnya akan diambil selaras dengan undang‑undang dan dasar organisasi.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">15. Insiden Keselamatan & Pelaporan</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Jika disyaki berlaku pelanggaran keselamatan data, pengguna hendaklah memaklumkan pentadbir dengan segera melalui saluran rasmi. Tindakan mitigasi, siasatan dan pemakluman yang wajar akan dilaksanakan mengikut prosedur dalaman.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">16. Perubahan Polisi</h4>
                    <p class="text-gray-700" style="font-size: 12px !important;">Polisi ini boleh dikemas kini dari semasa ke semasa. Versi terkini akan diterbitkan dalam Sistem dan berkuat kuasa sebaik diterbitkan.</p>
                </div>

                <div>
                    <h4 class="text-gray-900 font-semibold mb-2" style="font-size: 13px !important;">17. Hubungi Kami</h4>
                    <div class="text-gray-800" style="font-size: 12px !important;">
                        Pejabat RISDA Bahagian Sibu<br>
                        No 49 Lorong 51, Jalan Lau King Howe, 96000 Sibu, Sarawak<br>
                        E‑mel: <a href="mailto:prbsibu@risda.gov.my" class="text-blue-700 underline">prbsibu@risda.gov.my</a><br>
                        Tel: 084‑344712 / 084‑344713 &nbsp; | &nbsp; Faks: 084‑322531
                    </div>
                    <p class="text-gray-700 mt-2" style="font-size: 12px !important;">Sokongan JARA: <a href="mailto:administrator@jara.my" class="text-blue-700 underline">administrator@jara.my</a>, <a href="mailto:support@jara.my" class="text-blue-700 underline">support@jara.my</a></p>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end items-center">
                <button @click="privacyModal = false" type="button" class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" style="font-family: Poppins, sans-serif !important;">Tutup</button>
            </div>
        </div>
    </div>
</div>



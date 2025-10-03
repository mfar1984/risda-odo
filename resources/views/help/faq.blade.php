<x-dashboard-layout title="Soalan Lazim (FAQ)">
    <x-ui.page-header 
        title="Soalan Lazim (FAQ)" 
        description="Jawapan kepada soalan yang kerap ditanya mengenai sistem"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Search Bar -->
            <div class="bg-white rounded-md shadow-sm border border-gray-200 mb-6" x-data="{ search: '' }">
                <div class="flex items-center gap-3 px-4 h-[42px]">
                    <span class="material-symbols-outlined text-gray-400 text-lg">search</span>
                    <input 
                        type="text" 
                        x-model="search"
                        @input="filterFAQs($event.target.value)"
                        placeholder="Cari soalan..." 
                        class="flex-1 border-0 focus:ring-0 text-sm text-gray-900 placeholder-gray-400 h-full py-0"
                    >
                    <button 
                        x-show="search.length > 0"
                        @click="search = ''; filterFAQs('')"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-md border border-blue-200 p-4 mb-6">
                <p class="text-sm font-semibold text-gray-900 mb-3">Kategori Soalan:</p>
                <div class="flex flex-wrap gap-2">
                    <a href="#umum" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-100">
                        <span class="material-symbols-outlined text-sm mr-1">info</span>
                        Umum
                    </a>
                    <a href="#login" class="inline-flex items-center px-3 py-1.5 bg-white border border-purple-300 rounded text-xs font-medium text-purple-700 hover:bg-purple-100">
                        <span class="material-symbols-outlined text-sm mr-1">login</span>
                        Log Masuk
                    </a>
                    <a href="#program" class="inline-flex items-center px-3 py-1.5 bg-white border border-indigo-300 rounded text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                        <span class="material-symbols-outlined text-sm mr-1">event</span>
                        Program
                    </a>
                    <a href="#perjalanan" class="inline-flex items-center px-3 py-1.5 bg-white border border-green-300 rounded text-xs font-medium text-green-700 hover:bg-green-100">
                        <span class="material-symbols-outlined text-sm mr-1">directions_car</span>
                        Perjalanan
                    </a>
                    <a href="#tuntutan" class="inline-flex items-center px-3 py-1.5 bg-white border border-orange-300 rounded text-xs font-medium text-orange-700 hover:bg-orange-100">
                        <span class="material-symbols-outlined text-sm mr-1">receipt_long</span>
                        Tuntutan
                    </a>
                    <a href="#mobile" class="inline-flex items-center px-3 py-1.5 bg-white border border-teal-300 rounded text-xs font-medium text-teal-700 hover:bg-teal-100">
                        <span class="material-symbols-outlined text-sm mr-1">phone_android</span>
                        Aplikasi Mobile
                    </a>
                </div>
            </div>

            <!-- FAQ Sections -->
            <div x-data="faqData()" class="space-y-6">
                
                <!-- Category 1: Umum -->
                <div id="umum" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-blue-600 text-2xl mr-3">info</span>
                        <h2 class="text-base font-semibold text-gray-900">Soalan Umum</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item" data-category="umum" data-question="Apakah itu Sistem JARA?" data-keywords="jara sistem tentang">
                            <button @click="toggle('umum_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Apakah itu Sistem JARA?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'umum_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_1'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    JARA adalah singkatan kepada <strong>Jejak Aset & Rekod Automatif</strong>. Ia merupakan sistem pengurusan kenderaan dan perjalanan yang direka khas untuk RISDA bagi merekod penggunaan kenderaan, menguruskan program, dan memproses tuntutan pemandu secara automatik dan efisien.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="faq-item" data-category="umum" data-question="Siapa yang boleh menggunakan sistem ini?" data-keywords="pengguna akses siapa">
                            <button @click="toggle('umum_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Siapa yang boleh menggunakan sistem ini?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'umum_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_2'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Sistem ini boleh digunakan oleh:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li><strong>Administrator</strong> - Akses penuh ke semua modul dan tetapan</li>
                                    <li><strong>Pengurus Bahagian</strong> - Urus program, kenderaan, dan staf di bahagian masing-masing</li>
                                    <li><strong>Pengurus Stesen</strong> - Urus program dan kenderaan di stesen masing-masing</li>
                                    <li><strong>Pemandu</strong> - Rekod perjalanan dan hantar tuntutan melalui aplikasi mobile</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="faq-item" data-category="umum" data-question="Apakah pelayar web yang disokong?" data-keywords="browser chrome firefox edge safari">
                            <button @click="toggle('umum_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Apakah pelayar web yang disokong?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'umum_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Sistem ini menyokong pelayar web moden:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Google Chrome (versi terkini) - <strong>Disyorkan</strong></li>
                                    <li>Mozilla Firefox (versi terkini)</li>
                                    <li>Microsoft Edge (versi terkini)</li>
                                    <li>Safari (versi terkini)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="faq-item" data-category="umum" data-question="Adakah sistem ini selamat?" data-keywords="keselamatan security password">
                            <button @click="toggle('umum_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Adakah sistem ini selamat?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'umum_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Ya, sistem ini menggunakan pelbagai lapisan keselamatan:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Kata laluan disulitkan menggunakan <strong>Argon2</strong> (standard industri)</li>
                                    <li>Kawalan akses berasaskan <strong>peranan dan kebenaran</strong></li>
                                    <li>Semua aktiviti direkod dalam <strong>activity log</strong></li>
                                    <li>API dilindungi dengan <strong>token authentication</strong></li>
                                    <li>Data diasingkan mengikut <strong>organisasi</strong> (multi-tenancy)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 2: Log Masuk -->
                <div id="login" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-purple-600 text-2xl mr-3">login</span>
                        <h2 class="text-base font-semibold text-gray-900">Log Masuk & Akaun</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 5 -->
                        <div class="faq-item" data-category="login" data-question="Lupa kata laluan, bagaimana?" data-keywords="lupa password reset">
                            <button @click="toggle('login_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Saya lupa kata laluan, bagaimana?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'login_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_1'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Sila hubungi <strong>pentadbir sistem</strong> atau <strong>pengurus anda</strong> untuk menetapkan semula kata laluan. Berikan maklumat berikut:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Nama penuh</li>
                                    <li>Email akaun</li>
                                    <li>Bahagian/Stesen</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 6 -->
                        <div class="faq-item" data-category="login" data-question="Kenapa tidak boleh log masuk?" data-keywords="gagal login error">
                            <button @click="toggle('login_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Kenapa saya tidak boleh log masuk?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'login_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_2'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Berikut adalah sebab-sebab biasa:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-2 ml-4">
                                    <li><strong>Email atau kata laluan salah</strong> - Pastikan CAPS LOCK tidak aktif</li>
                                    <li><strong>Akaun tidak aktif</strong> - Hubungi pentadbir untuk mengaktifkan akaun</li>
                                    <li><strong>Sambungan internet</strong> - Semak sambungan internet anda</li>
                                    <li><strong>Pelayar web lama</strong> - Gunakan versi terkini Chrome/Firefox</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 7 -->
                        <div class="faq-item" data-category="login" data-question="Bagaimana tukar kata laluan?" data-keywords="tukar change password">
                            <button @click="toggle('login_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bagaimana cara menukar kata laluan?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'login_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Untuk menukar kata laluan:</p>
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Log masuk ke sistem</li>
                                    <li>Klik nama anda di sudut kanan atas</li>
                                    <li>Pilih <strong>Profil</strong></li>
                                    <li>Pergi ke tab <strong>Tukar Kata Laluan</strong></li>
                                    <li>Masukkan kata laluan semasa dan kata laluan baharu</li>
                                    <li>Klik <strong>Simpan</strong></li>
                                </ol>
                            </div>
                        </div>

                        <!-- FAQ Item 8 -->
                        <div class="faq-item" data-category="login" data-question="Aplikasi mobile logout sendiri?" data-keywords="logout auto keluar">
                            <button @click="toggle('login_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Kenapa aplikasi mobile logout sendiri?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'login_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Aplikasi mobile akan auto-logout jika:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Sudah lebih <strong>7 hari</strong> sejak login terakhir</li>
                                    <li>Token authentication tamat tempoh</li>
                                    <li>Kata laluan ditukar di sistem web</li>
                                    <li>Akaun dinyahaktifkan oleh pentadbir</li>
                                </ul>
                                <p class="text-sm text-gray-700 mt-2">
                                    <strong>Tips:</strong> Tandakan "Ingat Saya" semasa log masuk untuk kekal log masuk lebih lama.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 3: Program -->
                <div id="program" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-indigo-600 text-2xl mr-3">event</span>
                        <h2 class="text-base font-semibold text-gray-900">Pengurusan Program</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 9 -->
                        <div class="faq-item" data-category="program" data-question="Bagaimana buat program baru?" data-keywords="tambah create program">
                            <button @click="toggle('program_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bagaimana cara membuat program baru?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'program_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_1'" x-collapse class="px-6 pb-4">
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Pergi ke menu <strong>Program → Senarai Program</strong></li>
                                    <li>Klik butang <strong>+ Tambah Program</strong></li>
                                    <li>Isi maklumat (nama, tarikh, lokasi, pemandu, kenderaan)</li>
                                    <li>Klik <strong>Simpan</strong></li>
                                    <li>Program akan berstatus <strong>Draf</strong> dan perlu diluluskan</li>
                                </ol>
                            </div>
                        </div>

                        <!-- FAQ Item 10 -->
                        <div class="faq-item" data-category="program" data-question="Status program apa maksudnya?" data-keywords="status draf lulus aktif selesai">
                            <button @click="toggle('program_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Apakah maksud status program?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'program_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_2'" x-collapse class="px-6 pb-4">
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-2 ml-4">
                                    <li><strong>Draf</strong> - Program baru dicipta, belum diluluskan</li>
                                    <li><strong>Lulus</strong> - Program diluluskan, menunggu tarikh mula</li>
                                    <li><strong>Aktif</strong> - Program sedang berjalan, pemandu boleh rekod perjalanan</li>
                                    <li><strong>Selesai</strong> - Program tamat, semua perjalanan selesai</li>
                                    <li><strong>Tolak</strong> - Program tidak diluluskan</li>
                                    <li><strong>Tertunda</strong> - Program lewat dimulakan (auto-update oleh sistem)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 11 -->
                        <div class="faq-item" data-category="program" data-question="Boleh edit program selepas lulus?" data-keywords="edit kemaskini program">
                            <button @click="toggle('program_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bolehkah edit program selepas diluluskan?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'program_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Ya, program boleh diedit selagi:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Status masih <strong>Draf</strong> atau <strong>Lulus</strong></li>
                                    <li>Belum ada perjalanan yang direkod (status bukan Aktif)</li>
                                    <li>Anda mempunyai kebenaran untuk mengedit</li>
                                </ul>
                                <p class="text-sm text-gray-700 mt-2">
                                    <strong>Nota:</strong> Program yang sudah Aktif atau Selesai tidak boleh diedit untuk mengekalkan integriti data.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 12 -->
                        <div class="faq-item" data-category="program" data-question="Pemandu tidak terima notifikasi?" data-keywords="notifikasi notification">
                            <button @click="toggle('program_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Pemandu tidak terima notifikasi program?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'program_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Semak perkara berikut:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Pemandu telah <strong>log masuk</strong> ke aplikasi mobile sekurang-kurangnya sekali</li>
                                    <li>Notifikasi <strong>dibenarkan</strong> untuk aplikasi RISDA Driver</li>
                                    <li>Telefon mempunyai sambungan <strong>internet</strong> aktif</li>
                                    <li>Pemandu adalah yang <strong>betul ditugaskan</strong> dalam program</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 4: Perjalanan -->
                <div id="perjalanan" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-green-600 text-2xl mr-3">directions_car</span>
                        <h2 class="text-base font-semibold text-gray-900">Rekod Perjalanan</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 13 -->
                        <div class="faq-item" data-category="perjalanan" data-question="Bagaimana check-out?" data-keywords="check-out checkout mula">
                            <button @click="toggle('perjalanan_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bagaimana cara check-out (mula perjalanan)?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'perjalanan_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'perjalanan_1'" x-collapse class="px-6 pb-4">
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Buka aplikasi mobile</li>
                                    <li>Pergi ke tab <strong>Do</strong></li>
                                    <li>Pilih program dari senarai</li>
                                    <li>Tekan <strong>Start Journey</strong></li>
                                    <li>Masukkan <strong>bacaan odometer</strong> semasa</li>
                                    <li>Ambil <strong>foto odometer</strong> (optional)</li>
                                    <li>Tekan <strong>Confirm</strong></li>
                                </ol>
                            </div>
                        </div>

                        <!-- FAQ Item 14 -->
                        <div class="faq-item" data-category="perjalanan" data-question="Lupa check-in, boleh tambah kemudian?" data-keywords="lupa check-in tamat">
                            <button @click="toggle('perjalanan_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Lupa check-in, boleh tambah kemudian?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'perjalanan_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'perjalanan_2'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Tidak. Rekod perjalanan <strong>MESTI</strong> dibuat semasa check-out dan check-in sebenar. Jika terlupa, hubungi <strong>pengurus anda</strong> untuk bantuan. Pengurus boleh mengedit rekod perjalanan melalui sistem web.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 15 -->
                        <div class="faq-item" data-category="perjalanan" data-question="Foto odometer wajib?" data-keywords="foto odometer gambar">
                            <button @click="toggle('perjalanan_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Adakah foto odometer wajib?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'perjalanan_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'perjalanan_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Foto odometer adalah <strong>optional</strong> tetapi <strong>amat disyorkan</strong> untuk:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Bukti bacaan odometer yang tepat</li>
                                    <li>Elak pertikaian atau salah faham</li>
                                    <li>Kemudahan semakan semula jika perlu</li>
                                    <li>Audit trail yang lengkap</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 16 -->
                        <div class="faq-item" data-category="perjalanan" data-question="Jarak auto-calculate macam mana?" data-keywords="jarak kilometer auto">
                            <button @click="toggle('perjalanan_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bagaimana jarak auto-calculate?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'perjalanan_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'perjalanan_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Sistem akan mengira jarak secara automatik menggunakan formula:
                                </p>
                                <div class="bg-gray-100 rounded p-3 mb-2">
                                    <code class="text-sm text-blue-700">Jarak (KM) = Odometer Masuk - Odometer Keluar</code>
                                </div>
                                <p class="text-sm text-gray-700">
                                    Contoh: 125045 KM - 125000 KM = <strong>45 KM</strong>
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 17 -->
                        <div class="faq-item" data-category="perjalanan" data-question="Boleh check-out tanpa program?" data-keywords="check-out program">
                            <button @click="toggle('perjalanan_5')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Boleh check-out tanpa program?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'perjalanan_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'perjalanan_5'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Tidak. Setiap perjalanan <strong>MESTI dikaitkan dengan program</strong> yang telah diluluskan. Ini untuk memastikan:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4 mt-2">
                                    <li>Penggunaan kenderaan adalah sah dan dibenarkan</li>
                                    <li>Kos boleh dikaitkan dengan program tertentu</li>
                                    <li>Audit trail yang lengkap dan teratur</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 5: Tuntutan -->
                <div id="tuntutan" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-orange-600 text-2xl mr-3">receipt_long</span>
                        <h2 class="text-base font-semibold text-gray-900">Tuntutan</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 18 -->
                        <div class="faq-item" data-category="tuntutan" data-question="Kategori tuntutan apa ada?" data-keywords="kategori tuntutan jenis">
                            <button @click="toggle('tuntutan_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Apakah kategori tuntutan yang ada?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'tuntutan_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'tuntutan_1'" x-collapse class="px-6 pb-4">
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li><strong>Tol</strong> - Bayaran tol lebuhraya</li>
                                    <li><strong>Parking</strong> - Bayaran parkir kenderaan</li>
                                    <li><strong>Makanan & Minuman (F&B)</strong> - Belanja makan minum</li>
                                    <li><strong>Penginapan</strong> - Kos penginapan jika program bermalam</li>
                                    <li><strong>Minyak</strong> - Kos minyak tambahan</li>
                                    <li><strong>Penyelenggaraan</strong> - Kos baiki kenderaan (emergency)</li>
                                    <li><strong>Lain-lain</strong> - Perbelanjaan lain berkaitan program</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 19 -->
                        <div class="faq-item" data-category="tuntutan" data-question="Mesti ada resit?" data-keywords="resit receipt wajib">
                            <button @click="toggle('tuntutan_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Adakah resit wajib untuk tuntutan?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'tuntutan_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'tuntutan_2'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Ya, <strong>resit adalah WAJIB</strong> untuk semua tuntutan. Pastikan:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Resit asal (bukan photocopy jika boleh)</li>
                                    <li>Resit <strong>jelas dan boleh dibaca</strong></li>
                                    <li>Menunjukkan <strong>jumlah, tarikh, dan nama kedai</strong></li>
                                    <li>Ambil foto resit <strong>dalam keadaan terang</strong></li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 20 -->
                        <div class="faq-item" data-category="tuntutan" data-question="Tuntutan ditolak, boleh hantar semula?" data-keywords="tolak reject resubmit">
                            <button @click="toggle('tuntutan_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Tuntutan ditolak, boleh hantar semula?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'tuntutan_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'tuntutan_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Ya, boleh! Jika tuntutan ditolak:
                                </p>
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Baca <strong>alasan penolakan</strong> dengan teliti</li>
                                    <li>Pergi ke tab <strong>Claim</strong> dalam aplikasi</li>
                                    <li>Cari tuntutan yang ditolak (status: Ditolak)</li>
                                    <li>Tekan untuk <strong>edit</strong></li>
                                    <li>Betulkan maklumat/resit mengikut alasan</li>
                                    <li>Tekan <strong>Resubmit</strong></li>
                                </ol>
                            </div>
                        </div>

                        <!-- FAQ Item 21 -->
                        <div class="faq-item" data-category="tuntutan" data-question="Berapa lama kelulusan?" data-keywords="masa lama process">
                            <button @click="toggle('tuntutan_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Berapa lama tuntutan akan diluluskan?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'tuntutan_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'tuntutan_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Masa pemprosesan bergantung kepada <strong>pengurus/pentadbir</strong> anda. Secara amnya:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4 mt-2">
                                    <li>Tuntutan kecil (Tol, Parking): <strong>1-3 hari bekerja</strong></li>
                                    <li>Tuntutan besar (Penginapan, Penyelenggaraan): <strong>3-7 hari bekerja</strong></li>
                                </ul>
                                <p class="text-sm text-gray-700 mt-2">
                                    Anda akan terima <strong>notifikasi</strong> apabila tuntutan diproses.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 22 -->
                        <div class="faq-item" data-category="tuntutan" data-question="Boleh padam tuntutan?" data-keywords="delete padam cancel">
                            <button @click="toggle('tuntutan_5')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Boleh padam tuntutan yang sudah dihantar?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'tuntutan_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'tuntutan_5'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Pemandu <strong>tidak boleh</strong> padam tuntutan sendiri. Jika ingin membatalkan tuntutan, hubungi pengurus anda. Hanya <strong>pengurus/pentadbir</strong> boleh membatalkan tuntutan yang statusnya masih <strong>Pending</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 6: Aplikasi Mobile -->
                <div id="mobile" class="faq-category scroll-mt-6">
                    <div class="flex items-center mb-4">
                        <span class="material-symbols-outlined text-teal-600 text-2xl mr-3">phone_android</span>
                        <h2 class="text-base font-semibold text-gray-900">Aplikasi Mobile</h2>
                    </div>
                    
                    <div class="bg-white rounded-md shadow-sm border border-gray-200 divide-y divide-gray-200">
                        <!-- FAQ Item 23 -->
                        <div class="faq-item" data-category="mobile" data-question="Android apa yang disokong?" data-keywords="android version versi">
                            <button @click="toggle('mobile_1')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Versi Android yang disokong?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_1'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Aplikasi RISDA Driver menyokong <strong>Android 6.0 (Marshmallow) ke atas</strong>. Untuk prestasi terbaik, gunakan Android 9.0 atau lebih baharu.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 24 -->
                        <div class="faq-item" data-category="mobile" data-question="iOS ada aplikasi?" data-keywords="ios iphone apple">
                            <button @click="toggle('mobile_2')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Ada aplikasi untuk iOS (iPhone)?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_2'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Pada masa ini, aplikasi hanya tersedia untuk <strong>Android</strong>. Versi iOS sedang dalam perancangan.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 25 -->
                        <div class="faq-item" data-category="mobile" data-question="Aplikasi guna internet banyak?" data-keywords="data internet quota">
                            <button @click="toggle('mobile_3')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Aplikasi guna internet banyak?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_3'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Tidak. Aplikasi direka <strong>offline-first</strong>:
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Data disimpan dalam telefon (Hive storage)</li>
                                    <li>Internet hanya perlu untuk sync dan download program</li>
                                    <li>Foto dimampatkan sebelum upload</li>
                                    <li>Anggaran penggunaan: <strong>5-10 MB/hari</strong></li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Item 26 -->
                        <div class="faq-item" data-category="mobile" data-question="GPS tidak berfungsi?" data-keywords="gps location lokasi">
                            <button @click="toggle('mobile_4')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">GPS tidak berfungsi, kenapa?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_4'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Semak perkara berikut:</p>
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Pastikan <strong>GPS telefon dibuka</strong> (Location Services)</li>
                                    <li>Beri <strong>kebenaran lokasi</strong> kepada aplikasi RISDA Driver</li>
                                    <li>Gunakan aplikasi di <strong>kawasan terbuka</strong> (bukan dalam bangunan)</li>
                                    <li>Restart aplikasi atau telefon</li>
                                </ol>
                            </div>
                        </div>

                        <!-- FAQ Item 27 -->
                        <div class="faq-item" data-category="mobile" data-question="Kamera tidak berfungsi?" data-keywords="camera kamera foto gambar">
                            <button @click="toggle('mobile_5')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Tidak boleh ambil gambar, kenapa?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_5'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">Kemungkinan sebab:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Aplikasi tidak diberi <strong>kebenaran kamera</strong></li>
                                    <li>Storage telefon <strong>penuh</strong></li>
                                    <li>Kamera telefon sedang digunakan aplikasi lain</li>
                                </ul>
                                <p class="text-sm text-gray-700 mt-2">
                                    <strong>Penyelesaian:</strong> Pergi ke Settings → Apps → RISDA Driver → Permissions → Benarkan Camera & Storage
                                </p>
                            </div>
                        </div>

                        <!-- FAQ Item 28 -->
                        <div class="faq-item" data-category="mobile" data-question="Update aplikasi macam mana?" data-keywords="update kemaskini version">
                            <button @click="toggle('mobile_6')" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Bagaimana update aplikasi ke versi terkini?</span>
                                <span class="material-symbols-outlined text-gray-400 transition-transform" :class="{ 'rotate-180': open === 'mobile_6' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_6'" x-collapse class="px-6 pb-4">
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                                    Pentadbir akan mengedarkan fail APK terkini apabila ada update. Untuk install:
                                </p>
                                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1 ml-4">
                                    <li>Muat turun fail APK terkini</li>
                                    <li>Buka fail APK</li>
                                    <li>Benarkan "Install from Unknown Sources" jika diminta</li>
                                    <li>Klik <strong>Install</strong></li>
                                    <li>Update akan replace versi lama (data kekal selamat)</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results Message -->
                <div x-show="!hasVisibleFAQs()" class="text-center py-12">
                    <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">search_off</span>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tiada Hasil Ditemui</h3>
                    <p class="text-sm text-gray-600">Cuba cari dengan kata kunci yang berbeza</p>
                </div>

            </div>

            <!-- Contact Support -->
            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-md border border-orange-200 p-6 mt-8">
                <div class="flex items-start">
                    <span class="material-symbols-outlined text-orange-600 text-3xl mr-4">contact_support</span>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Soalan Tidak Dijawab?</h3>
                        <p class="text-sm text-gray-700 mb-3">Jika soalan anda tidak dijawab di sini, sila hubungi pasukan sokongan kami:</p>
                        <a href="{{ route('help.hubungi-sokongan') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded transition-colors">
                            <span class="material-symbols-outlined text-lg mr-2">mail</span>
                            Hubungi Sokongan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </x-ui.page-header>

    <script>
        function faqData() {
            return {
                open: null,
                
                toggle(id) {
                    this.open = this.open === id ? null : id;
                },

                hasVisibleFAQs() {
                    const items = document.querySelectorAll('.faq-item');
                    return Array.from(items).some(item => item.style.display !== 'none');
                }
            }
        }

        function filterFAQs(searchTerm) {
            const term = searchTerm.toLowerCase().trim();
            const items = document.querySelectorAll('.faq-item');
            const categories = document.querySelectorAll('.faq-category');

            items.forEach(item => {
                const question = item.dataset.question.toLowerCase();
                const keywords = item.dataset.keywords.toLowerCase();
                const category = item.dataset.category.toLowerCase();
                
                if (term === '' || question.includes(term) || keywords.includes(term) || category.includes(term)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide categories that have no visible items
            categories.forEach(category => {
                const categoryItems = category.querySelectorAll('.faq-item');
                const hasVisible = Array.from(categoryItems).some(item => item.style.display !== 'none');
                category.style.display = hasVisible ? '' : 'none';
            });
        }

        // Smooth scroll for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
    </script>

    <style>
        .scroll-mt-6 {
            scroll-margin-top: 1.5rem;
        }
    </style>
</x-dashboard-layout>

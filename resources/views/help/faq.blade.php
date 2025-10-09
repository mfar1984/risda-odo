<x-dashboard-layout title="Soalan Lazim (FAQ)">
    <x-ui.page-header 
        title="Soalan Lazim (FAQ)" 
        description="Jawapan kepada soalan yang kerap ditanya mengenai sistem"
    >
        <div x-data="faqData()" class="space-y-6">
            
            {{-- Hero Search Section --}}
            <div class="faq-hero">
                <div class="faq-hero-content">
                    <div class="flex items-center justify-center mb-4">
                        <div class="faq-hero-icon">
                            <span class="material-symbols-outlined text-white text-[32px]">help_center</span>
                        </div>
                    </div>
                    <h2 class="text-white text-center font-bold mb-2" style="font-family: Poppins, sans-serif !important; font-size: 20px !important;">
                        Ada Soalan?
                    </h2>
                    <p class="text-white/80 text-center mb-6" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        Cari jawapan kepada soalan anda di sini
                    </p>
                    
                    {{-- Search Bar --}}
                    <div class="faq-search-wrapper">
                        <span class="material-symbols-outlined faq-search-icon">search</span>
                        <input 
                            type="text" 
                            x-model="searchTerm"
                            @input="filterFAQs($event.target.value)"
                            placeholder="Cari soalan atau kata kunci..." 
                            class="faq-search-input"
                        >
                        <button 
                            x-show="searchTerm.length > 0"
                            @click="searchTerm = ''; filterFAQs('')"
                            class="faq-search-clear"
                        >
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    {{-- Stats --}}
                    <div class="faq-stats">
                        <div class="faq-stat-item">
                            <span class="faq-stat-number">47</span>
                            <span class="faq-stat-label">Soalan</span>
                        </div>
                        <div class="faq-stat-divider"></div>
                        <div class="faq-stat-item">
                            <span class="faq-stat-number">8</span>
                            <span class="faq-stat-label">Kategori</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category Cards Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="#umum" class="faq-category-card faq-card-blue">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">info</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Soalan Umum</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">5 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>

                <a href="#login" class="faq-category-card faq-card-purple">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">login</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Log Masuk</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">4 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>

                <a href="#program" class="faq-category-card faq-card-indigo">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">event</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Program</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">5 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>

                <a href="#perjalanan" class="faq-category-card faq-card-green">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">directions_car</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Perjalanan</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">5 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>

                <a href="#tuntutan" class="faq-category-card faq-card-orange">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Tuntutan</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">5 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>

                <a href="#mobile" class="faq-category-card faq-card-teal">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">phone_android</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Aplikasi Mobile</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">4 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>
                <a href="#offline" class="faq-category-card faq-card-emerald">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">sync_saved_locally</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Offline-First</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">4 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>
                <a href="#sokongan" class="faq-category-card faq-card-blue">
                    <div class="faq-category-icon">
                        <span class="material-symbols-outlined text-[24px]">support_agent</span>
                    </div>
                    <div class="flex-1">
                        <h3 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Hubungi Sokongan</h3>
                        <p style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">8 soalan</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] opacity-50">arrow_forward</span>
                </a>
            </div>

            {{-- FAQ Sections --}}
            <div class="space-y-8">
                
                {{-- Category 1: Umum --}}
                <div id="umum" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-blue">
                        <span class="material-symbols-outlined text-[20px]">info</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Soalan Umum</h2>
                        <span class="faq-count">5</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="umum" data-question="Apakah itu Sistem JARA?" data-keywords="jara sistem tentang">
                            <button @click="toggle('umum_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">1</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apakah itu Sistem JARA?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'umum_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    JARA adalah singkatan kepada <strong>Jejak Aset & Rekod Automatif</strong>. Ia merupakan sistem pengurusan kenderaan dan perjalanan yang direka khas untuk RISDA bagi merekod penggunaan kenderaan, menguruskan program, dan memproses tuntutan pemandu secara automatik dan efisien.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="umum" data-question="Siapa yang boleh menggunakan sistem ini?" data-keywords="pengguna akses siapa">
                            <button @click="toggle('umum_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">2</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Siapa yang boleh menggunakan sistem ini?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'umum_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; margin-bottom: 8px;">Sistem ini boleh digunakan oleh:</p>
                                <ul class="faq-list">
                                    <li><span class="material-symbols-outlined text-[12px] text-blue-600">check_circle</span><strong>Administrator</strong> - Akses penuh ke semua modul dan tetapan</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-blue-600">check_circle</span><strong>Pengurus Bahagian</strong> - Urus program, kenderaan, dan staf di bahagian masing-masing</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-blue-600">check_circle</span><strong>Pengurus Stesen</strong> - Urus program dan kenderaan di stesen masing-masing</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-blue-600">check_circle</span><strong>Pemandu</strong> - Rekod perjalanan dan hantar tuntutan melalui aplikasi mobile</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item" data-category="umum" data-question="Adakah sistem ini percuma?" data-keywords="percuma bayaran kos">
                            <button @click="toggle('umum_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">3</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Adakah sistem ini percuma?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'umum_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Ya, sistem JARA adalah sistem dalaman RISDA dan percuma untuk semua pengguna yang diberi akses. Tiada sebarang bayaran atau kos langganan yang dikenakan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="umum" data-question="Bagaimana untuk mendapatkan akses ke sistem?" data-keywords="akses daftar register">
                            <button @click="toggle('umum_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">4</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk mendapatkan akses ke sistem?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'umum_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Untuk mendapatkan akses, sila hubungi Administrator sistem atau Pengurus Bahagian anda. Mereka akan mencipta akaun pengguna untuk anda dan memberikan maklumat log masuk. Pendaftaran sendiri tidak dibenarkan untuk tujuan keselamatan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="umum" data-question="Apakah perbezaan antara Bahagian dan Stesen?" data-keywords="bahagian stesen organisasi">
                            <button @click="toggle('umum_5')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">5</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apakah perbezaan antara Bahagian dan Stesen?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'umum_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'umum_5'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    <strong>Bahagian</strong> merujuk kepada bahagian utama dalam RISDA (contoh: Bahagian Pengurusan Ladang), manakala <strong>Stesen</strong> adalah lokasi fizikal di bawah bahagian tersebut (contoh: Stesen Kuala Lumpur, Stesen Johor Bahru). Setiap bahagian boleh mempunyai beberapa stesen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 2: Log Masuk --}}
                <div id="login" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-purple">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Log Masuk & Akaun</h2>
                        <span class="faq-count">4</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="login" data-question="Saya lupa kata laluan saya. Bagaimana?" data-keywords="lupa password reset">
                            <button @click="toggle('login_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">6</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Saya lupa kata laluan saya. Bagaimana?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'login_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Jika anda lupa kata laluan, klik pautan "Lupa Kata Laluan?" di halaman log masuk. Masukkan alamat e-mel anda, dan pautan reset kata laluan akan dihantar ke e-mel anda. Ikut arahan dalam e-mel untuk menetapkan kata laluan baharu.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="login" data-question="Akaun saya dikunci. Apa yang perlu saya lakukan?" data-keywords="kunci locked banned">
                            <button @click="toggle('login_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">7</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Akaun saya dikunci. Apa yang perlu saya lakukan?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'login_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Akaun akan dikunci secara automatik selepas 5 cubaan log masuk yang gagal untuk tujuan keselamatan. Sila hubungi Administrator sistem atau Pengurus Bahagian anda untuk membuka kunci akaun. Mereka boleh menetapkan semula kata laluan anda juga jika perlu.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="login" data-question="Bagaimana untuk menukar kata laluan saya?" data-keywords="tukar change password">
                            <button @click="toggle('login_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">8</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk menukar kata laluan saya?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'login_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Selepas log masuk, pergi ke menu <strong>Profil</strong> di bahagian atas kanan, kemudian pilih <strong>Kemaskini Kata Laluan</strong>. Masukkan kata laluan semasa anda, diikuti dengan kata laluan baharu. Pastikan kata laluan baharu sekurang-kurangnya 8 aksara.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="login" data-question="Bolehkah saya log masuk di beberapa peranti serentak?" data-keywords="multiple devices concurrent">
                            <button @click="toggle('login_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">9</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah saya log masuk di beberapa peranti serentak?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'login_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'login_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Ya, anda boleh log masuk di beberapa peranti pada masa yang sama. Walau bagaimanapun, untuk keselamatan, anda disarankan untuk log keluar selepas selesai menggunakan sistem, terutamanya pada peranti awam atau berkongsi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 3: Program --}}
                <div id="program" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-indigo">
                        <span class="material-symbols-outlined text-[20px]">event</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Pengurusan Program</h2>
                        <span class="faq-count">5</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="program" data-question="Bagaimana untuk membuat program baharu?" data-keywords="cipta create tambah add">
                            <button @click="toggle('program_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">10</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk membuat program baharu?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'program_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6; margin-bottom: 8px;">
                                    Untuk membuat program baharu:
                                </p>
                                <ol class="faq-list-numbered">
                                    <li>Pergi ke menu <strong>Program</strong></li>
                                    <li>Klik butang <strong>Tambah Program</strong></li>
                                    <li>Isikan maklumat program (nama, tarikh mula/tamat, lokasi, dll)</li>
                                    <li>Pilih pemandu yang ditugaskan</li>
                                    <li>Klik <strong>Simpan</strong> untuk cipta program</li>
                                </ol>
                            </div>
                        </div>

                        <div class="faq-item" data-category="program" data-question="Apakah status program yang ada?" data-keywords="status draft aktif selesai batal">
                            <button @click="toggle('program_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">11</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apakah status program yang ada?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'program_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; margin-bottom: 8px;">Status program:</p>
                                <ul class="faq-list">
                                    <li><span class="material-symbols-outlined text-[12px] text-gray-600">fiber_manual_record</span><strong>Draf</strong> - Program baru dicipta, belum aktif</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-blue-600">fiber_manual_record</span><strong>Aktif</strong> - Program sedang berjalan</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-green-600">fiber_manual_record</span><strong>Selesai</strong> - Program telah tamat</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">fiber_manual_record</span><strong>Dibatalkan</strong> - Program dibatalkan</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item" data-category="program" data-question="Bolehkah saya edit program yang sudah aktif?" data-keywords="edit ubah kemaskini update">
                            <button @click="toggle('program_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">12</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah saya edit program yang sudah aktif?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'program_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Ya, anda boleh edit program yang aktif, tetapi dengan had tertentu. Anda boleh mengemas kini maklumat asas seperti penerangan dan nota, tetapi tarikh mula/tamat dan pemandu mungkin terhad jika log perjalanan sudah wujud. Hubungi Administrator jika perlu tukar maklumat kritikal.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="program" data-question="Bagaimana program menjadi 'Selesai' secara automatik?" data-keywords="automatic status complete">
                            <button @click="toggle('program_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">13</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana program menjadi 'Selesai' secara automatik?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'program_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Sistem akan secara automatik menukar status program kepada 'Selesai' apabila tarikh tamat program telah berlalu. Proses ini berjalan setiap hari pada tengah malam. Notifikasi akan dihantar kepada pemandu dan pengurus yang berkaitan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="program" data-question="Bolehkah satu pemandu ditugaskan ke beberapa program serentak?" data-keywords="multiple concurrent pemandu">
                            <button @click="toggle('program_5')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">14</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah satu pemandu ditugaskan ke beberapa program serentak?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'program_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'program_5'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Ya, seorang pemandu boleh ditugaskan ke beberapa program pada masa yang sama, selagi tarikh program tidak bertindih. Sistem akan memberi amaran jika terdapat konflik tarikh semasa penugasan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 4: Perjalanan --}}
                <div id="perjalanan" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-green">
                        <span class="material-symbols-outlined text-[20px]">directions_car</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Log Perjalanan</h2>
                        <span class="faq-count">5</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="perjalanan" data-question="Bagaimana pemandu merekod perjalanan?" data-keywords="rekod record log journey">
                            <button @click="toggle('journey_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">15</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana pemandu merekod perjalanan?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'journey_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'journey_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Pemandu menggunakan aplikasi mobile JARA untuk merekod perjalanan. Mereka perlu klik butang "Mula Perjalanan" sebelum berlepas, dan "Tamat Perjalanan" selepas sampai. Sistem akan automatik merekod masa, lokasi GPS, dan jarak perjalanan. Pemandu juga boleh tambah nota dan gambar jika perlu.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="perjalanan" data-question="Apa yang berlaku jika pemandu terlupa tamat perjalanan?" data-keywords="forget end complete">
                            <button @click="toggle('journey_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">16</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apa yang berlaku jika pemandu terlupa tamat perjalanan?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'journey_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'journey_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Jika pemandu terlupa klik "Tamat Perjalanan", mereka masih boleh tamatkan perjalanan tersebut melalui aplikasi dengan memilih perjalanan yang sedang berjalan. Sistem juga akan hantar peringatan selepas 12 jam jika perjalanan masih belum ditamatkan. Pengurus juga boleh tamatkan perjalanan melalui sistem web.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="perjalanan" data-question="Bolehkah perjalanan diedit selepas tamat?" data-keywords="edit modify after complete">
                            <button @click="toggle('journey_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">17</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah perjalanan diedit selepas tamat?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'journey_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'journey_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Pemandu boleh edit nota dan gambar perjalanan dalam tempoh 24 jam selepas perjalanan tamat. Walau bagaimanapun, maklumat seperti masa mula/tamat dan jarak tidak boleh diedit oleh pemandu. Jika ada kesilapan data, hubungi Pengurus untuk bantuan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="perjalanan" data-question="Mengapa jarak GPS tidak sama dengan odometer?" data-keywords="distance difference gps odometer">
                            <button @click="toggle('journey_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">18</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Mengapa jarak GPS tidak sama dengan odometer?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'journey_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'journey_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Perbezaan kecil (5-10%) antara jarak GPS dan odometer adalah normal disebabkan perbezaan cara pengiraan. GPS mengira jarak garis lurus antara koordinat, manakala odometer mengukur jarak sebenar roda. Sistem menggunakan bacaan odometer sebagai rujukan utama untuk pengiraan tuntutan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="perjalanan" data-question="Bolehkah saya lihat sejarah perjalanan yang lepas?" data-keywords="history past previous">
                            <button @click="toggle('journey_5')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">19</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah saya lihat sejarah perjalanan yang lepas?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'journey_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'journey_5'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Ya, pergi ke menu <strong>Log Pemandu</strong> di sistem web atau tab <strong>Sejarah</strong> di aplikasi mobile. Anda boleh lihat semua perjalanan lepas dengan butiran lengkap termasuk masa, lokasi, jarak, dan gambar. Gunakan penapis tarikh untuk cari perjalanan tertentu.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 5: Tuntutan --}}
                <div id="tuntutan" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-orange">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Tuntutan</h2>
                        <span class="faq-count">5</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="tuntutan" data-question="Bagaimana untuk membuat tuntutan?" data-keywords="create submit hantar claim">
                            <button @click="toggle('claim_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">20</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk membuat tuntutan?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'claim_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'claim_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6; margin-bottom: 8px;">
                                    Untuk membuat tuntutan:
                                </p>
                                <ol class="faq-list-numbered">
                                    <li>Buka aplikasi mobile JARA</li>
                                    <li>Pergi ke tab <strong>Tuntutan</strong></li>
                                    <li>Klik <strong>Buat Tuntutan</strong></li>
                                    <li>Pilih jenis tuntutan (Minyak, Tol, Lain-lain)</li>
                                    <li>Isikan jumlah dan muat naik resit</li>
                                    <li>Klik <strong>Hantar</strong></li>
                                </ol>
                            </div>
                        </div>

                        <div class="faq-item" data-category="tuntutan" data-question="Berapa lama tuntutan diproses?" data-keywords="process time duration approval">
                            <button @click="toggle('claim_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">21</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Berapa lama tuntutan diproses?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'claim_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'claim_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Tuntutan biasanya diproses dalam tempoh 3-5 hari bekerja selepas dihantar. Anda akan terima notifikasi melalui aplikasi apabila tuntutan diluluskan atau ditolak. Jika tuntutan memerlukan pengesahan tambahan, proses mungkin mengambil masa lebih lama.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="tuntutan" data-question="Apakah jenis tuntutan yang boleh dibuat?" data-keywords="types categories jenis">
                            <button @click="toggle('claim_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">22</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apakah jenis tuntutan yang boleh dibuat?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'claim_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'claim_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; margin-bottom: 8px;">Jenis tuntutan yang tersedia:</p>
                                <ul class="faq-list">
                                    <li><span class="material-symbols-outlined text-[12px] text-orange-600">local_gas_station</span><strong>Minyak</strong> - Tuntutan kos minyak kenderaan</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-orange-600">toll</span><strong>Tol</strong> - Tuntutan bayaran tol</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-orange-600">local_parking</span><strong>Parking</strong> - Tuntutan bayaran parking</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-orange-600">fastfood</span><strong>Makan</strong> - Elaun makan (mengikut syarat)</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-orange-600">more_horiz</span><strong>Lain-lain</strong> - Perbelanjaan lain yang berkaitan</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item" data-category="tuntutan" data-question="Kenapa tuntutan saya ditolak?" data-keywords="rejected denied why alasan">
                            <button @click="toggle('claim_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">23</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Kenapa tuntutan saya ditolak?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'claim_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'claim_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; margin-bottom: 8px;">Tuntutan mungkin ditolak kerana:</p>
                                <ul class="faq-list">
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">close</span>Resit tidak jelas atau tidak lengkap</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">close</span>Jumlah tuntutan tidak sepadan dengan resit</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">close</span>Tuntutan melebihi had yang dibenarkan</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">close</span>Tuntutan tidak berkaitan dengan program</li>
                                    <li><span class="material-symbols-outlined text-[12px] text-red-600">close</span>Dokumentasi sokongan tidak mencukupi</li>
                                </ul>
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6; margin-top: 8px;">
                                    Anda boleh semak nota penolakan dan hantar semula tuntutan dengan maklumat yang betul.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="tuntutan" data-question="Bolehkah saya batalkan tuntutan yang sudah dihantar?" data-keywords="cancel withdraw delete">
                            <button @click="toggle('claim_5')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">24</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bolehkah saya batalkan tuntutan yang sudah dihantar?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'claim_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'claim_5'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Anda boleh batalkan tuntutan selagi ia masih berstatus <strong>Pending</strong> (belum diproses). Selepas tuntutan diluluskan atau ditolak, ia tidak boleh dibatalkan. Untuk batalkan, pergi ke senarai tuntutan anda dan klik butang <strong>Batalkan</strong> pada tuntutan yang berkenaan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 6: Mobile App --}}
                <div id="mobile" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-teal">
                        <span class="material-symbols-outlined text-[20px]">phone_android</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Aplikasi Mobile</h2>
                        <span class="faq-count">4</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="mobile" data-question="Bagaimana untuk muat turun aplikasi mobile?" data-keywords="download install app">
                            <button @click="toggle('mobile_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">25</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk muat turun aplikasi mobile?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'mobile_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Aplikasi JARA tersedia untuk Android sahaja buat masa ini. Dapatkan pautan muat turun daripada Pengurus anda atau melalui e-mel pemberitahuan. Install fail APK dan berikan kebenaran yang diperlukan. Pastikan tetapan "Install from Unknown Sources" diaktifkan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="mobile" data-question="Adakah aplikasi perlu sambungan Internet?" data-keywords="offline internet connection">
                            <button @click="toggle('mobile_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">26</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Adakah aplikasi perlu sambungan Internet?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'mobile_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Aplikasi boleh berfungsi dalam mod offline untuk merekod perjalanan. Data akan disimpan dalam telefon dan automatik disegerakkan ke pelayan apabila sambungan Internet tersedia. Walau bagaimanapun, untuk hantar tuntutan dan lihat maklumat program terkini, sambungan Internet diperlukan.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="mobile" data-question="Mengapa aplikasi menggunakan lokasi GPS?" data-keywords="location permission privacy gps">
                            <button @click="toggle('mobile_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">27</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Mengapa aplikasi menggunakan lokasi GPS?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'mobile_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    GPS diperlukan untuk merekod lokasi mula dan tamat perjalanan serta mengira jarak perjalanan dengan tepat. Ini memastikan ketepatan data untuk tujuan audit dan memudahkan pengesahan tuntutan. Data lokasi hanya digunakan semasa perjalanan dan tidak direkod pada masa lain.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="mobile" data-question="Bagaimana untuk kemaskini aplikasi ke versi terbaru?" data-keywords="update version upgrade">
                            <button @click="toggle('mobile_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">28</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana untuk kemaskini aplikasi ke versi terbaru?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'mobile_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'mobile_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Anda akan terima notifikasi apabila versi baharu tersedia. Klik notifikasi untuk muat turun dan install versi terkini. Pastikan data anda telah disegerakkan sebelum kemaskini. Versi semasa aplikasi boleh dilihat di menu <strong>Tetapan > Tentang</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 8: Offline-First --}}
                <div id="offline" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-emerald">
                        <span class="material-symbols-outlined text-[20px]">sync_saved_locally</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Offline-First</h2>
                        <span class="faq-count">4</span>
                    </div>

                    <div class="faq-accordion">
                        <div class="faq-item" data-category="offline" data-question="Apa maksud Offline-First dalam aplikasi JARA?" data-keywords="offline first maksud">
                            <button @click="toggle('offline_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">29</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apa maksud Offline-First dalam aplikasi JARA?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'offline_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'offline_1'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Offline-First bermaksud aplikasi direka untuk berfungsi penuh tanpa Internet: Start/End Journey, simpan resit, dan lihat data yang telah disegerakkan. Data akan di-sync automatik ke pelayan apabila sambungan Internet kembali.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="offline" data-question="Apa yang berlaku pada data semasa Logout?" data-keywords="logout clear hive kosong data">
                            <button @click="toggle('offline_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">30</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apa yang berlaku pada data semasa Logout?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'offline_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'offline_2'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Semasa Logout, semua data tempatan (Hive) akan dikosongkan untuk keselamatan dan mengelakkan data bercampur antara pengguna. Pastikan semua data telah berjaya sync sebelum Logout.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="offline" data-question="Apa data yang dimuat turun semasa Login?" data-keywords="login sync hive muat turun cache">
                            <button @click="toggle('offline_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">31</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Apa data yang dimuat turun semasa Login?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'offline_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'offline_3'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Selepas Login, aplikasi akan memuat turun dan menyimpan ke Hive: program aktif/berkaitan, kenderaan, log perjalanan, tuntutan, dan tetapan pengguna supaya semua ini boleh diakses secara offline.
                                </p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="offline" data-question="Bagaimana resit dan No. Resit berfungsi secara offline?" data-keywords="resit no resit tuntutan offline">
                            <button @click="toggle('offline_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="faq-number">32</span>
                                    <span style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500;">Bagaimana resit dan No. Resit berfungsi secara offline?</span>
                                </div>
                                <span class="material-symbols-outlined faq-arrow" :class="{ 'rotate-180': open === 'offline_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'offline_4'" x-collapse class="faq-answer">
                                <p style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 1.6;">
                                    Gambar resit disimpan dalam storan tempatan dan metadata (termasuk <strong>No. Resit</strong>) disimpan dalam Hive. Apabila talian pulih, servis sync akan muat naik resit dan menghantar No. Resit ke pelayan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category 7: Hubungi Sokongan --}}
                <div id="sokongan" class="faq-category scroll-mt-6">
                    <div class="faq-section-header faq-header-blue">
                        <span class="material-symbols-outlined text-[20px]">support_agent</span>
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600;">Hubungi Sokongan</h2>
                        <span class="faq-count">8</span>
                    </div>
                    
                    <div class="faq-accordion">
                        <div class="faq-item" data-category="sokongan" data-question="Bagaimana cara untuk buat tiket sokongan?" data-keywords="create ticket buat tiket">
                            <button @click="toggle('sokongan_1')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bagaimana cara untuk buat tiket sokongan?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_1' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_1'" x-collapse class="faq-answer">
                                <p>Untuk membuat tiket sokongan:</p>
                                <ol>
                                    <li>Pergi ke <strong>Bantuan → Hubungi Sokongan</strong></li>
                                    <li>Klik butang <strong>"Buat Tiket Baru"</strong></li>
                                    <li>Isi maklumat: Subjek, Kategori, Keutamaan, Mesej</li>
                                    <li>Tambah lampiran jika perlu (optional)</li>
                                    <li>Klik <strong>"Hantar Tiket"</strong></li>
                                </ol>
                                <p>Tiket akan dihantar kepada administrator dan anda akan dapat notifikasi bila ada balasan.</p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Bagaimana cara untuk reply tiket?" data-keywords="reply balas chat">
                            <button @click="toggle('sokongan_2')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bagaimana cara untuk reply tiket?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_2' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_2'" x-collapse class="faq-answer">
                                <p>Untuk membalas tiket:</p>
                                <ol>
                                    <li>Klik butang <strong>"Lihat & Respond"</strong> pada tiket</li>
                                    <li>Scroll ke bahagian <strong>"Balas Tiket"</strong> di bawah</li>
                                    <li>Taip balasan anda dalam text area</li>
                                    <li>Klik <strong>"Lampiran"</strong> untuk upload fail (optional)</li>
                                    <li>Klik <strong>"Hantar Respons"</strong></li>
                                </ol>
                                <p><strong>Real-time:</strong> Chat auto-update setiap 3 saat. User lain akan nampak mesej anda automatik tanpa refresh!</p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Apa itu Assign dan Participant dalam tiket?" data-keywords="assign participant tugaskan peserta">
                            <button @click="toggle('sokongan_3')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Apa itu Assign dan Participant dalam tiket?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_3' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_3'" x-collapse class="faq-answer">
                                <p><strong>Assign (Tugaskan):</strong> Orang yang bertanggungjawab menyelesaikan tiket.</p>
                                <ul>
                                    <li>Hanya 1 orang boleh di-assign pada satu masa</li>
                                    <li>Staff boleh assign kepada staff lain dalam organisasi sama</li>
                                    <li>Administrator boleh assign kepada sesiapa sahaja</li>
                                </ul>
                                <p><strong>Participant (Peserta):</strong> Orang yang terlibat dalam perbincangan.</p>
                                <ul>
                                    <li>Multiple participants boleh ditambah</li>
                                    <li>Semua participants boleh lihat dan balas dalam tiket</li>
                                    <li>Gunakan untuk "loop in" staff lain untuk discussion</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Bagaimana cara escalate tiket ke administrator?" data-keywords="escalate kritikal urgent">
                            <button @click="toggle('sokongan_4')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bagaimana cara escalate tiket ke administrator?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_4' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_4'" x-collapse class="faq-answer">
                                <p>Untuk escalate tiket (staff sahaja, untuk tiket dari Android):</p>
                                <ol>
                                    <li>Buka tiket yang perlu di-escalate</li>
                                    <li>Klik butang <strong>"Escalate to Administrator"</strong></li>
                                    <li>Tiket akan di-set kepada prioriti <strong>KRITIKAL</strong></li>
                                    <li>Semua administrators akan dapat notification</li>
                                </ol>
                                <p><strong>Nota:</strong> Button escalate hanya appear untuk staff bila view Android tickets.</p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Siapa boleh lihat tiket saya?" data-keywords="access privacy who can see">
                            <button @click="toggle('sokongan_5')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Siapa boleh lihat tiket saya?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_5' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_5'" x-collapse class="faq-answer">
                                <p>Tiket boleh dilihat oleh:</p>
                                <ul>
                                    <li><strong>Creator</strong> - Yang buat tiket (always dapat lihat)</li>
                                    <li><strong>Assigned Person</strong> - Yang bertanggungjawab selesaikan tiket</li>
                                    <li><strong>Participants</strong> - Yang ditambah untuk perbincangan</li>
                                    <li><strong>Administrators</strong> - Boleh lihat semua tiket</li>
                                </ul>
                                <p><strong>Privacy:</strong> Staff lain yang bukan creator/assigned/participant TIDAK boleh lihat tiket anda.</p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Bolehkah saya attach fail dalam reply?" data-keywords="attachment lampiran file upload">
                            <button @click="toggle('sokongan_6')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bolehkah saya attach fail dalam reply?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_6' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_6'" x-collapse class="faq-answer">
                                <p>Ya! Anda boleh attach fail bila reply tiket:</p>
                                <ol>
                                    <li>Dalam form reply, klik butang <strong>"Lampiran"</strong></li>
                                    <li>Pilih fail (PDF, gambar, Excel, Word, etc.)</li>
                                    <li>Boleh pilih multiple files sekaligus</li>
                                    <li>Fail akan preview sebelum hantar</li>
                                    <li>Klik "Hantar Respons" untuk send dengan attachments</li>
                                </ol>
                                <p><strong>Max size:</strong> 5MB per file. <strong>Format:</strong> PDF, JPG, PNG, XLS, XLSX, DOC, DOCX</p>
                                <p>User lain boleh klik attachment untuk preview atau download.</p>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Bagaimana cara export chat history?" data-keywords="export download history">
                            <button @click="toggle('sokongan_7')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bagaimana cara export chat history?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_7' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_7'" x-collapse class="faq-answer">
                                <p>Untuk export chat history (tiket yang dah selesai):</p>
                                <ol>
                                    <li>Buka tiket yang dah ditutup/selesai</li>
                                    <li>Klik butang <strong>"Eksport Chat History"</strong></li>
                                    <li>Fail teks (.txt) akan auto-download</li>
                                </ol>
                                <p><strong>Kandungan export:</strong></p>
                                <ul>
                                    <li>Ticket info (number, subject, priority, status)</li>
                                    <li>Creator, assigned person, participants</li>
                                    <li>Semua messages dengan timestamp</li>
                                    <li>List attachments</li>
                                    <li>Export metadata (who exported, when)</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item" data-category="sokongan" data-question="Bila saya dapat notification untuk tiket?" data-keywords="notification bell sound">
                            <button @click="toggle('sokongan_8')" class="faq-question">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="material-symbols-outlined faq-q-icon">help</span>
                                    <span>Bila saya dapat notification untuk tiket?</span>
                                </div>
                                <span class="material-symbols-outlined text-[20px] faq-toggle-icon" :class="{ 'rotate-180': open === 'sokongan_8' }">expand_more</span>
                            </button>
                            <div x-show="open === 'sokongan_8'" x-collapse class="faq-answer">
                                <p>Anda akan dapat bell notification bila:</p>
                                <ul>
                                    <li><strong>Tiket baru</strong> - Bila ada tiket baru untuk anda (admin atau assigned)</li>
                                    <li><strong>Reply baru</strong> - Bila ada reply dalam tiket yang anda terlibat</li>
                                    <li><strong>Assigned</strong> - Bila tiket di-assign kepada anda</li>
                                    <li><strong>Added as participant</strong> - Bila ditambah ke tiket</li>
                                    <li><strong>Escalated</strong> - Bila tiket di-escalate (admin sahaja)</li>
                                    <li><strong>Closed</strong> - Bila tiket ditutup</li>
                                </ul>
                                <p><strong>Sound:</strong> Bell notification akan bunyi bila ada notification baru. Click bell icon atau refresh page untuk lihat notifications.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Still Need Help Section --}}
            <div class="faq-help-section">
                <div class="faq-help-icon">
                    <span class="material-symbols-outlined text-[32px] text-blue-600">contact_support</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-gray-900 font-semibold mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        Masih Perlukan Bantuan?
                    </h3>
                    <p class="text-gray-600 mb-3" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        Tidak jumpa jawapan yang anda cari? Hubungi pasukan sokongan kami.
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('help.hubungi-sokongan') }}" class="faq-help-button faq-button-primary">
                            <span class="material-symbols-outlined text-[16px]">support_agent</span>
                            Hubungi Sokongan
                        </a>
                        <a href="{{ route('help.panduan-pengguna') }}" class="faq-help-button faq-button-secondary">
                            <span class="material-symbols-outlined text-[16px]">menu_book</span>
                            Panduan Pengguna
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
                searchTerm: '',

                toggle(id) {
                    this.open = this.open === id ? null : id;
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
</x-dashboard-layout>

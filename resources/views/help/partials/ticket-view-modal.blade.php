{{-- TICKET VIEW MODAL (Staff & Admin) --}}

<div x-show="viewTicketModal" 
     x-cloak
     @keydown.escape.window="viewTicketModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="viewTicketModal = false"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] my-8"
             @click.away="viewTicketModal = false">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">confirmation_number</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            TICKET-0001
                        </h3>
                        <p class="text-blue-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Dibuka: 2 jam lalu
                        </p>
                    </div>
                </div>
                <button @click="viewTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Content --}}
            <div class="overflow-y-auto" style="max-height: calc(85vh - 180px);">
                
                {{-- Ticket Info --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Subjek</div>
                            <div class="text-[12px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                Tak boleh login di aplikasi mobile
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Status</div>
                            <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-blue-100 text-blue-800 border border-blue-200">
                                Baru
                            </span>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Keutamaan</div>
                            <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-red-100 text-red-800 border border-red-200">
                                <span class="material-symbols-outlined text-[12px] mr-1">circle</span>
                                Tinggi
                            </span>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kategori</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">Teknikal</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Dibuka Oleh</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                fairiz@jara.my (Pemandu, Stesen A)
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Mesej</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">3 mesej</div>
                        </div>
                    </div>
                </div>

                {{-- Thread Messages --}}
                <div class="p-6 space-y-4">
                    <h4 class="text-[12px] font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px] align-middle mr-1">forum</span>
                        Thread Mesej
                    </h4>

                    {{-- Message 1 (Original from Driver) --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600 text-[18px]">person</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                            Fairiz Ahmad
                                        </div>
                                        <div class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                            fairiz@jara.my · Pemandu · 2 jam lalu
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm bg-purple-100 text-purple-800">
                                        PEMANDU
                                    </span>
                                </div>
                                <div class="text-[11px] text-gray-700 leading-relaxed" style="font-family: Poppins, sans-serif !important;">
                                    Assalamualaikum, saya ada masalah login di aplikasi mobile. Bila masukkan email dan password, keluar error "Invalid credentials". Tapi saya pasti password saya betul sebab baru tukar semalam. Tolong bantu saya.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message 2 (Reply from Staff) --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]">support_agent</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-blue-50 rounded-sm p-4 border border-blue-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                            Faizan Abdullah
                                        </div>
                                        <div class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                            faizan@jara.my · Staff · 1 jam lalu
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm bg-blue-600 text-white">
                                        STAFF
                                    </span>
                                </div>
                                <div class="text-[11px] text-gray-700 leading-relaxed" style="font-family: Poppins, sans-serif !important;">
                                    Waalaikumussalam En. Fairiz. Terima kasih atas laporan. Saya akan semak akaun anda. Boleh cuba reset password sekali lagi?
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message 3 (Reply from Driver) --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600 text-[18px]">person</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                            Fairiz Ahmad
                                        </div>
                                        <div class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                            fairiz@jara.my · Pemandu · 30 min lalu
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm bg-purple-100 text-purple-800">
                                        PEMANDU
                                    </span>
                                </div>
                                <div class="text-[11px] text-gray-700 leading-relaxed" style="font-family: Poppins, sans-serif !important;">
                                    Dah cuba reset tapi masih tak boleh login juga. Ada cara lain tak?
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <div class="flex gap-2">
                    <button @click="replyTicketModal = true; viewTicketModal = false" 
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">reply</span>
                        Balas
                    </button>
                    <button class="h-8 px-4 text-[11px] rounded-sm border border-yellow-300 text-yellow-700 hover:bg-yellow-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">flag</span>
                        Tandakan Urgent
                    </button>
                </div>
                <div class="flex gap-2">
                    <button class="h-8 px-4 text-[11px] rounded-sm border border-green-300 text-green-700 hover:bg-green-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Selesaikan
                    </button>
                    <button @click="viewTicketModal = false" 
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ESCALATE TICKET MODAL (Staff to Admin) --}}

<div x-show="escalateTicketModal" 
     x-cloak
     @keydown.escape.window="escalateTicketModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="escalateTicketModal = false"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-2xl max-h-[85vh] my-8 flex flex-col"
             @click.away="escalateTicketModal = false">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">trending_up</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Escalate ke Administrator
                        </h3>
                        <p class="text-red-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            TICKET-0001: Tak boleh login di aplikasi mobile
                        </p>
                    </div>
                </div>
                <button @click="escalateTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <form class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 180px);">
                
                {{-- Warning Banner --}}
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-sm">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600 text-[18px]">warning</span>
                        <div>
                            <div class="text-[11px] font-semibold text-red-900 mb-1" style="font-family: Poppins, sans-serif !important;">
                                ⚠️ Escalate Tiket ke Administrator
                            </div>
                            <div class="text-[10px] text-red-800" style="font-family: Poppins, sans-serif !important;">
                                Tiket ini akan di-escalate kepada <strong>Administrator</strong> untuk tindakan lanjut. Pastikan anda telah cuba selesaikan isu ini terlebih dahulu.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Escalation Reason --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Sebab Escalate <span class="text-red-600">*</span>
                    </label>
                    <select class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <option value="">Pilih Sebab</option>
                        <option value="technical">Masalah Teknikal Kompleks</option>
                        <option value="permission">Memerlukan Kebenaran Admin</option>
                        <option value="access">Isu Akses Sistem</option>
                        <option value="policy">Berkaitan Dasar & Polisi</option>
                        <option value="urgent">Isu Kritikal/Urgent</option>
                        <option value="other">Lain-lain</option>
                    </select>
                </div>

                {{-- Escalation Details --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Keterangan Lengkap <span class="text-red-600">*</span>
                    </label>
                    <textarea 
                        rows="5"
                        placeholder="Huraikan kenapa tiket ini perlu di-escalate dan tindakan yang telah diambil..."
                        class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 resize-none"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                    >Tiket ini memerlukan akses sistem billing yang hanya boleh diberikan oleh Administrator. Saya telah cuba bantu user tetapi tidak mempunyai kebenaran untuk memberikan akses tersebut.

Tindakan yang telah diambil:
1. Semak akaun user - status aktif
2. Cuba reset password - berjaya
3. Semak permission - tiada akses billing

Mohon admin dapat berikan akses billing kepada user ini.</textarea>
                </div>

                {{-- Priority Selection --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Tahap Keutamaan <span class="text-red-600">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center gap-2 cursor-pointer px-3 py-3 border-2 border-yellow-300 bg-yellow-50 rounded-sm hover:bg-yellow-100 transition-colors">
                            <input type="radio" name="escalate_priority" value="sederhana" checked class="text-yellow-600 focus:ring-yellow-500">
                            <span class="material-symbols-outlined text-yellow-600 text-[20px]">circle</span>
                            <span class="text-[10px] font-medium" style="font-family: Poppins, sans-serif !important;">Sederhana</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer px-3 py-3 border-2 border-orange-300 bg-orange-50 rounded-sm hover:bg-orange-100 transition-colors">
                            <input type="radio" name="escalate_priority" value="tinggi" class="text-orange-600 focus:ring-orange-500">
                            <span class="material-symbols-outlined text-orange-600 text-[20px]">circle</span>
                            <span class="text-[10px] font-medium" style="font-family: Poppins, sans-serif !important;">Tinggi</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 cursor-pointer px-3 py-3 border-2 border-red-300 bg-red-50 rounded-sm hover:bg-red-100 transition-colors">
                            <input type="radio" name="escalate_priority" value="kritikal" class="text-red-600 focus:ring-red-500">
                            <span class="material-symbols-outlined text-red-600 text-[20px]">circle</span>
                            <span class="text-[10px] font-medium" style="font-family: Poppins, sans-serif !important;">Kritikal</span>
                        </label>
                    </div>
                </div>

                {{-- Current Ticket Summary --}}
                <div class="mb-4 bg-gray-50 border border-gray-200 rounded-sm p-4">
                    <div class="text-[10px] font-medium text-gray-700 mb-3" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[14px] align-middle mr-1">summarize</span>
                        Ringkasan Tiket
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-[10px]" style="font-family: Poppins, sans-serif !important;">
                        <div>
                            <span class="text-gray-500">Dibuka oleh:</span>
                            <span class="text-gray-900 font-medium ml-2">fairiz@jara.my</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Dibuka:</span>
                            <span class="text-gray-900 font-medium ml-2">2 jam lalu</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Kategori:</span>
                            <span class="text-gray-900 font-medium ml-2">Teknikal</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Mesej:</span>
                            <span class="text-gray-900 font-medium ml-2">3 mesej</span>
                        </div>
                    </div>
                </div>

                {{-- Notify Admin --}}
                <div class="mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded text-red-600 focus:ring-red-500">
                        <span class="text-[10px]" style="font-family: Poppins, sans-serif !important;">
                            <strong>Hantar notifikasi urgent kepada Administrator</strong> (Email + Push Notification)
                        </span>
                    </label>
                </div>

            </form>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <button @click="escalateTicketModal = false" 
                        type="button"
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="h-8 px-4 text-[11px] font-medium rounded-sm bg-red-600 text-white hover:bg-red-700 transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    Escalate ke Admin
                </button>
            </div>

        </div>
    </div>
</div>


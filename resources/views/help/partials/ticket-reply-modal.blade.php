{{-- TICKET REPLY MODAL --}}

<div x-show="replyTicketModal" 
     x-cloak
     @keydown.escape.window="replyTicketModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="replyTicketModal = false"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] my-8 flex flex-col"
             @click.away="replyTicketModal = false">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">reply</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Balas Tiket: TICKET-0001
                        </h3>
                        <p class="text-green-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Tak boleh login di aplikasi mobile
                        </p>
                    </div>
                </div>
                <button @click="replyTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <div class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 180px);">
                
                {{-- Reply Type --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Jenis Respons
                    </label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reply_type" value="message" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Mesej Biasa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reply_type" value="escalate" class="text-red-600 focus:ring-red-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Escalate ke Admin</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reply_type" value="resolve" class="text-green-600 focus:ring-green-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Selesaikan Tiket</span>
                        </label>
                    </div>
                </div>

                {{-- Message Textarea --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Mesej Anda
                    </label>
                    <textarea 
                        rows="6"
                        placeholder="Taip mesej balasan anda di sini..."
                        class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 resize-none"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                    >Terima kasih atas maklum balas. Saya akan escalate isu ini kepada pihak IT untuk penyelesaian segera. Anda akan menerima update dalam masa 24 jam.</textarea>
                </div>

                {{-- Internal Notes (Staff/Admin only) --}}
                <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-sm p-4">
                    <label class="text-[11px] font-medium text-yellow-900 mb-2 block flex items-center gap-2" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        Nota Dalaman (Tidak dilihat oleh pemandu)
                    </label>
                    <textarea 
                        rows="3"
                        placeholder="Tambah nota dalaman untuk staff/admin sahaja..."
                        class="w-full px-3 py-2 text-[11px] rounded-sm border border-yellow-300 focus:ring-0 focus:border-yellow-500 resize-none bg-white"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                    ></textarea>
                </div>

                {{-- Quick Responses --}}
                <div class="mb-4">
                    <div class="text-[10px] font-medium text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[14px] align-middle mr-1">quick_phrases</span>
                        Respons Pantas
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Sedang disiasat
                        </button>
                        <button class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Memerlukan maklumat lanjut
                        </button>
                        <button class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Dalam proses
                        </button>
                        <button class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Selesai
                        </button>
                    </div>
                </div>

                {{-- Attachment --}}
                <div class="mb-6">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Lampiran (Optional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-sm p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-[32px]">upload_file</span>
                        <div class="text-[10px] text-gray-600 mt-2" style="font-family: Poppins, sans-serif !important;">
                            Klik untuk upload atau drag & drop
                        </div>
                        <div class="text-[9px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important;">
                            PDF, JPG, PNG (Max 5MB)
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <button @click="replyTicketModal = false; viewTicketModal = true" 
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Kembali ke Tiket
                </button>
                <div class="flex gap-2">
                    <button class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        Draft
                    </button>
                    <button class="h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Hantar Respons
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


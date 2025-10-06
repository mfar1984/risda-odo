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
                            <span id="ticket-number-display">-</span>
                        </h3>
                        <p class="text-blue-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            <span id="ticket-opened-ago">&nbsp;</span>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Subjek</div>
                            <div id="ticket-subject-display" class="text-[12px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">-</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Status</div>
                            <span id="ticket-status-badge" class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-blue-100 text-blue-800 border border-blue-200">-</span>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Keutamaan</div>
                            <span id="ticket-priority-badge" class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-gray-100 text-gray-800 border border-gray-200">-</span>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kategori</div>
                            <div id="ticket-category-display" class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">-</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Dibuka Oleh</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                <span id="ticket-creator-name">-</span>
                                <span class="text-gray-500"> · </span>
                                <span id="ticket-organization-display" class="text-gray-700">-</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Mesej</div>
                            <div id="ticket-message-count" class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">-</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">IP Address</div>
                            <div id="ticket-ip-display" class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">-</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Peranti / Platform</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                <span id="ticket-device-display">-</span>
                                <span class="text-gray-500"> · </span>
                                <span id="ticket-platform-display">-</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Lokasi</div>
                            <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                <span id="ticket-location-display">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Original Message/Issue --}}
                <div id="original-message-section" class="p-6 bg-blue-50 border-b border-blue-200" style="display: none;">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">description</span>
                        <h3 class="text-[12px] font-semibold text-blue-900" style="font-family: Poppins, sans-serif !important;">📋 Aduan / Masalah Asal</h3>
                    </div>
                    <div class="bg-white rounded-sm p-4 border border-blue-300">
                        <div id="original-message-content" class="text-[11px] text-gray-900 leading-relaxed" style="font-family: Poppins, sans-serif !important; white-space: pre-line;"></div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                <span class="material-symbols-outlined text-[12px] align-middle mr-1">person</span>
                                <span id="original-message-author">-</span> · 
                                <span id="original-message-time">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thread Messages --}}
                <div class="p-6 space-y-4">
                    <h4 class="text-[12px] font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px] align-middle mr-1">forum</span>
                        Thread Mesej
                    </h4>

                    <div id="ticket-messages-container" class="space-y-3 mb-6">
                        <div class="text-[11px] text-gray-500" style="font-family: Poppins, sans-serif !important;">Tiada mesej.</div>
                    </div>

                    {{-- Inline Reply Form --}}
                    <div class="border-t border-gray-200 pt-4">
                        <h5 class="text-[11px] font-semibold text-gray-900 mb-3 flex items-center gap-2" style="font-family: Poppins, sans-serif !important;">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                            Balas Tiket
                        </h5>
                        <form id="inline-reply-ticket-form">
                            @csrf
                            <textarea 
                                name="message"
                                rows="4"
                                required
                                placeholder="Taip balasan anda di sini..."
                                class="w-full px-3 py-2 text-[11px] border border-gray-300 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                style="font-family: Poppins, sans-serif !important;"></textarea>
                            
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-[10px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                    <span class="material-symbols-outlined text-[12px] align-middle mr-1">info</span>
                                    Balasan akan dihantar kepada pemandu
                                </div>
                                <button type="submit" class="h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">send</span>
                                    Hantar Respons
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <div class="flex gap-2">
                    {{-- Escalate button (for staff viewing Android tickets only) --}}
                    <button id="btn-escalate" onclick="escalateTicket()" class="h-8 px-4 text-[11px] rounded-sm border border-red-300 text-red-700 hover:bg-red-50 transition-colors inline-flex items-center gap-1.5" style="display: none; font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">trending_up</span>
                        Escalate to Administrator
                    </button>
                    
                    {{-- Admin-only buttons --}}
                    <button id="btn-assign" onclick="openAssignModal()" class="h-8 px-4 text-[11px] rounded-sm border border-blue-300 text-blue-700 hover:bg-blue-50 transition-colors inline-flex items-center gap-1.5" style="display: none; font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">person_add</span>
                        Tugaskan
                    </button>
                    
                    <button id="btn-close" onclick="closeTicket()" class="h-8 px-4 text-[11px] rounded-sm border border-green-300 text-green-700 hover:bg-green-50 transition-colors inline-flex items-center gap-1.5" style="display: none; font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Selesaikan
                    </button>
                </div>
                <button @click="viewTicketModal = false" 
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>


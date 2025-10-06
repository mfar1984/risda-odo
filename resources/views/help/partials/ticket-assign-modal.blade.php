{{-- ASSIGN TICKET MODAL (Admin Only) --}}

<div x-show="assignTicketModal" 
     x-cloak
     @keydown.escape.window="assignTicketModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="assignTicketModal = false"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-2xl max-h-[85vh] my-8 flex flex-col"
             @click.away="assignTicketModal = false">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">person_add</span>
                    <div>
                        <h3 id="assign-modal-ticket-number" class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Assign Tiket
                        </h3>
                        <p id="assign-modal-subject" class="text-purple-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Loading...
                        </p>
                    </div>
                </div>
                <button @click="assignTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <form id="assign-ticket-form" class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 180px);">
                @csrf
                
                {{-- Current Assignment Info --}}
                <div id="current-assignment-info" class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-6 rounded-sm">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                        <div class="text-[10px] text-blue-800" style="font-family: Poppins, sans-serif !important;">
                            <strong>Status Semasa:</strong> <span id="current-assigned-text">Loading...</span>
                        </div>
                    </div>
                </div>

                {{-- Section 1: Assign To (Main Responsible Person) --}}
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="text-[12px] font-semibold text-gray-900 mb-3 flex items-center gap-2" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">person</span>
                        Tugaskan Kepada (Orang Bertanggungjawab)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                            Pilih Pengguna <span class="text-gray-400">(Optional)</span>
                        </label>
                        <select id="assign-user-select" name="assigned_to" class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <option value="">Tidak berubah (kekal assigned semasa)</option>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important;">
                            Kosongkan jika hanya nak tambah peserta sahaja.
                        </p>
                    </div>
                </div>

                {{-- Section 2: Add Participants (For Discussion) --}}
                <div class="mb-6">
                    <h4 class="text-[12px] font-semibold text-gray-900 mb-3 flex items-center gap-2" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        Tambah Peserta (Untuk Perbincangan)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                            Pilih Pengguna
                        </label>
                        <div class="flex gap-2">
                            <select id="participant-user-select" class="flex-1 h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                                <option value="">Pilih pengguna...</option>
                            </select>
                            <button type="button" onclick="addParticipantToTicket()" class="h-8 px-4 text-[11px] rounded-sm bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                Tambah
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important;">
                            Peserta boleh lihat dan balas dalam tiket ini.
                        </p>
                    </div>

                    {{-- Current Participants List --}}
                    <div id="participants-list" class="space-y-2">
                        <!-- Participants will be loaded here dynamically -->
                    </div>
                </div>

            </form>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <button @click="assignTicketModal = false" 
                        type="button"
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button type="submit" form="assign-ticket-form" class="h-8 px-4 text-[11px] font-medium rounded-sm bg-purple-600 text-white hover:bg-purple-700 transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Simpan Assignment
                </button>
            </div>

        </div>
    </div>
</div>


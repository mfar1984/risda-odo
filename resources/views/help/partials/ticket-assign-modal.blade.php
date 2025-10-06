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
            <div class="support-modal-header-purple">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">person_add</span>
                    <div>
                        <h3 id="assign-modal-ticket-number" class="support-modal-title">
                            Assign Tiket
                        </h3>
                        <p id="assign-modal-subject" class="support-modal-subtitle text-purple-100">
                            Loading...
                        </p>
                    </div>
                </div>
                <button @click="assignTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <form id="assign-ticket-form" class="support-modal-body" style="max-height: calc(85vh - 180px);">
                @csrf
                
                {{-- Current Assignment Info --}}
                <div id="current-assignment-info" class="support-info-box support-info-box-blue">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                        <div class="support-info-box-text">
                            <strong>Status Semasa:</strong> <span id="current-assigned-text">Loading...</span>
                        </div>
                    </div>
                </div>

                {{-- Section 1: Assign To (Main Responsible Person) --}}
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h4 class="support-section-title">
                        <span class="material-symbols-outlined text-[16px]">person</span>
                        Tugaskan Kepada (Orang Bertanggungjawab)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="support-form-label">
                            Pilih Pengguna <span class="text-gray-400">(Optional)</span>
                        </label>
                        <select id="assign-user-select" name="assigned_to" class="support-form-select">
                            <option value="">Tidak berubah (kekal assigned semasa)</option>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif;">
                            Kosongkan jika hanya nak tambah peserta sahaja.
                        </p>
                    </div>
                </div>

                {{-- Section 2: Add Participants (For Discussion) --}}
                <div class="mb-6">
                    <h4 class="support-section-title">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        Tambah Peserta (Untuk Perbincangan)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="support-form-label">
                            Pilih Pengguna
                        </label>
                        <div class="flex gap-2">
                            <select id="participant-user-select" class="flex-1 support-form-select">
                                <option value="">Pilih pengguna...</option>
                            </select>
                            <button type="button" onclick="addParticipantToTicket()" class="support-btn bg-green-600 text-white hover:bg-green-700 font-medium">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                Tambah
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif;">
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
            <div class="support-modal-footer">
                <button @click="assignTicketModal = false" 
                        type="button"
                        class="support-btn support-btn-secondary">
                    Tutup
                </button>
                <button type="submit" form="assign-ticket-form" class="support-btn support-btn-purple">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Simpan Assignment
                </button>
            </div>

        </div>
    </div>
</div>


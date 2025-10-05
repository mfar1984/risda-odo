{{-- Cancel Tuntutan Modal (Centralized Component) --}}

<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
    {{-- Backdrop --}}
    <div class="fixed inset-0" onclick="closeCancelModal()"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">block</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Batal Tuntutan
                        </h3>
                        <p class="text-red-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Tindakan kekal - Pemandu tidak boleh edit
                        </p>
                    </div>
                </div>
                <button onclick="closeCancelModal()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="cancelForm" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label for="alasan_gantung" class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Alasan Pembatalan <span class="text-red-600">*</span>
                    </label>
                    <textarea id="alasan_gantung" name="alasan_gantung" rows="5" required minlength="10" maxlength="1000"
                              class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-300 focus:ring-0 focus:border-red-500 resize-none"
                              style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                              placeholder="Sila berikan alasan pembatalan (minimum 10 aksara)"></textarea>
                    <div class="mt-2 flex items-start gap-2 bg-red-50 border border-red-200 rounded-sm p-3">
                        <span class="material-symbols-outlined text-red-600 text-[16px]">warning</span>
                        <p class="text-[10px] text-red-700 font-medium" style="font-family: Poppins, sans-serif !important;">
                            AMARAN: Tindakan ini kekal dan tidak boleh dibatalkan. Pemandu tidak akan dapat mengedit tuntutan ini.
                        </p>
                    </div>
                </div>
                
                {{-- Footer Actions --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()"
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-red-600 text-white hover:bg-red-700 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">block</span>
                        Batal Tuntutan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


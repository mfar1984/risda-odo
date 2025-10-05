{{-- Reject Program Modal (Centralized Component) --}}

<div id="rejectProgramModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
    {{-- Backdrop --}}
    <div class="fixed inset-0" onclick="closeRejectProgramModal()"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">cancel</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Tolak Program
                        </h3>
                        <p class="text-orange-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Pengesahan penolakan program
                        </p>
                    </div>
                </div>
                <button onclick="closeRejectProgramModal()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="rejectProgramForm" method="POST" class="p-6">
                @csrf
                @method('PATCH')
                
                {{-- Generated Code Display --}}
                <div class="mb-6">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Kod Pengesahan Sistem
                    </label>
                    <div class="bg-orange-50 border-2 border-orange-300 rounded-sm p-4 text-center">
                        <p class="text-[10px] text-orange-700 mb-2" style="font-family: Poppins, sans-serif !important;">
                            Kod ini dijana oleh sistem untuk pengesahan penolakan:
                        </p>
                        <div id="generatedRejectProgramCode" class="text-[28px] font-bold text-orange-700 tracking-[0.3em] font-mono select-all">
                            ------
                        </div>
                        <p class="text-[9px] text-orange-600 mt-2" style="font-family: Poppins, sans-serif !important;">
                            Salin atau ingati kod ini
                        </p>
                    </div>
                </div>

                {{-- Confirmation Input --}}
                <div class="mb-6">
                    <label for="rejectProgramCodeConfirm" class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Taip Semula Kod untuk Pengesahan <span class="text-red-600">*</span>
                    </label>
                    <input type="text" 
                           id="rejectProgramCodeConfirm" 
                           name="reject_code" 
                           required 
                           minlength="6" 
                           maxlength="6"
                           pattern="[A-Z0-9]{6}"
                           class="w-full px-3 py-2 text-[14px] font-mono tracking-widest uppercase rounded-sm border border-gray-300 focus:ring-0 focus:border-orange-500"
                           style="font-family: 'Courier New', monospace !important; font-size: 14px !important; letter-spacing: 0.2em;"
                           placeholder="TAIP KOD DI SINI"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); validateRejectProgramCode()" />
                    <input type="hidden" id="generatedRejectProgramCodeHidden" name="generated_code" />
                    <p id="rejectProgramCodeMatchMsg" class="mt-2 text-[10px] hidden" style="font-family: Poppins, sans-serif !important;"></p>
                </div>
                
                {{-- Footer Actions --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectProgramModal()"
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit" id="rejectProgramSubmitBtn"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-orange-600 text-white hover:bg-orange-700 transition-colors inline-flex items-center gap-1.5"
                            disabled>
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Tolak Program
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

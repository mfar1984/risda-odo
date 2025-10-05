{{-- Reject Tuntutan Modal (Centralized Component) --}}

<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
    {{-- Backdrop --}}
    <div class="fixed inset-0" onclick="closeRejectModal()"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">cancel</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Tolak Tuntutan
                        </h3>
                        <p class="text-orange-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Pemandu boleh edit & hantar semula
                        </p>
                    </div>
                </div>
                <button onclick="closeRejectModal()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="rejectForm" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label for="alasan_tolak" class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Alasan Penolakan <span class="text-red-600">*</span>
                    </label>
                    <textarea id="alasan_tolak" name="alasan_tolak" rows="5" required minlength="10" maxlength="1000"
                              class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-300 focus:ring-0 focus:border-orange-500 resize-none"
                              style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                              placeholder="Sila berikan alasan penolakan (minimum 10 aksara)"></textarea>
                    <div class="mt-2 flex items-start gap-2 bg-orange-50 border border-orange-200 rounded-sm p-3">
                        <span class="material-symbols-outlined text-orange-600 text-[16px]">info</span>
                        <p class="text-[10px] text-orange-700" style="font-family: Poppins, sans-serif !important;">
                            Pemandu akan menerima notifikasi dan boleh mengedit serta menghantar semula tuntutan ini.
                        </p>
                    </div>
                </div>
                
                {{-- Footer Actions --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-orange-600 text-white hover:bg-orange-700 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Tolak Tuntutan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


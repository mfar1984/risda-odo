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
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Assign Tiket: TICKET-0001
                        </h3>
                        <p class="text-purple-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Tak boleh login di aplikasi mobile
                        </p>
                    </div>
                </div>
                <button @click="assignTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <form class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 180px);">
                
                {{-- Current Assignment Info --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-6 rounded-sm">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                        <div class="text-[10px] text-blue-800" style="font-family: Poppins, sans-serif !important;">
                            <strong>Status Semasa:</strong> Tiket ini belum di-assign kepada sesiapa. Pilih staff untuk handle tiket ini.
                        </div>
                    </div>
                </div>

                {{-- Select Staff --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Assign Kepada <span class="text-red-600">*</span>
                    </label>
                    <select class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <option value="">Pilih Staff</option>
                        <optgroup label="Stesen A">
                            <option value="staff1">Faizan Abdullah (faizan@jara.my) - Staff</option>
                            <option value="staff2">Ahmad Ali (ahmad@jara.my) - Staff</option>
                            <option value="staff3">Siti Nurhaliza (siti@jara.my) - Staff</option>
                        </optgroup>
                        <optgroup label="Stesen B">
                            <option value="staff4">Rahman Hassan (rahman@jara.my) - Staff</option>
                            <option value="staff5">Nurul Aina (nurul@jara.my) - Staff</option>
                        </optgroup>
                        <optgroup label="Administrator">
                            <option value="admin1">Admin JARA (admin@jara.my) - Administrator</option>
                        </optgroup>
                    </select>
                </div>

                {{-- Priority Level --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Set Keutamaan
                    </label>
                    <div class="flex gap-2">
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2 border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors flex-1">
                            <input type="radio" name="priority" value="rendah" class="text-green-600 focus:ring-green-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Rendah</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2 border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors flex-1">
                            <input type="radio" name="priority" value="sederhana" checked class="text-yellow-600 focus:ring-yellow-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Sederhana</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2 border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors flex-1">
                            <input type="radio" name="priority" value="tinggi" class="text-red-600 focus:ring-red-500">
                            <span class="text-[11px]" style="font-family: Poppins, sans-serif !important;">Tinggi</span>
                        </label>
                    </div>
                </div>

                {{-- Due Date --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Tarikh Akhir (Optional)
                    </label>
                    <input 
                        type="date"
                        class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                    />
                </div>

                {{-- Assignment Notes --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Nota Assignment (Optional)
                    </label>
                    <textarea 
                        rows="4"
                        placeholder="Tambah nota atau arahan untuk staff yang di-assign..."
                        class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 resize-none"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                    >Sila handle tiket ini dengan segera. User adalah pemandu penting di bahagian kita.</textarea>
                </div>

                {{-- Notify Options --}}
                <div class="mb-4 bg-gray-50 border border-gray-200 rounded-sm p-4">
                    <div class="text-[11px] font-medium text-gray-700 mb-3" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[14px] align-middle mr-1">notifications</span>
                        Notifikasi
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-[10px]" style="font-family: Poppins, sans-serif !important;">Hantar email kepada staff yang di-assign</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-[10px]" style="font-family: Poppins, sans-serif !important;">Hantar notifikasi push (jika ada)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-[10px]" style="font-family: Poppins, sans-serif !important;">Maklumkan kepada user yang buka tiket</span>
                        </label>
                    </div>
                </div>

            </form>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <button @click="assignTicketModal = false" 
                        type="button"
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="h-8 px-4 text-[11px] font-medium rounded-sm bg-purple-600 text-white hover:bg-purple-700 transition-colors inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Assign Tiket
                </button>
            </div>

        </div>
    </div>
</div>


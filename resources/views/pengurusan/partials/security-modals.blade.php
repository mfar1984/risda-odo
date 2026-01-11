{{-- Reset Password Modal --}}
<div id="resetPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
    {{-- Backdrop --}}
    <div class="fixed inset-0" onclick="closeResetPasswordModal()"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">lock_reset</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Reset Kata Laluan
                        </h3>
                        <p class="text-blue-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Masukkan kata laluan baharu untuk pengguna
                        </p>
                    </div>
                </div>
                <button onclick="closeResetPasswordModal()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="resetPasswordForm" method="POST" action="" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="new_password" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Kata Laluan Baharu <span class="text-red-600">*</span>
                        </label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               required
                               minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                               style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                               placeholder="Minimum 8 aksara">
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Sahkan Kata Laluan <span class="text-red-600">*</span>
                        </label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required
                               minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-sm text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                               style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                               placeholder="Masukkan semula kata laluan">
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-sm p-3">
                        <div class="flex">
                            <span class="material-symbols-outlined text-yellow-600 mr-2" style="font-size: 16px;">warning</span>
                            <p class="text-xs text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Pengguna akan dilog keluar dari semua sesi dan perlu log masuk semula dengan kata laluan baharu.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6">
                    <button type="button" 
                            onclick="closeResetPasswordModal()"
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5"
                            style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5"
                            style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                        Reset Kata Laluan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Lock Account Modal --}}
<div id="lockAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
    {{-- Backdrop --}}
    <div class="fixed inset-0" onclick="closeLockAccountModal()"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-md">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">lock</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Kunci Akaun
                        </h3>
                        <p class="text-red-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Nyatakan sebab mengunci akaun ini
                        </p>
                    </div>
                </div>
                <button onclick="closeLockAccountModal()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="lockAccountForm" method="POST" action="" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="reason" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Sebab (Pilihan)
                        </label>
                        <textarea id="reason" 
                                  name="reason" 
                                  rows="3"
                                  maxlength="500"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-sm text-xs focus:outline-none focus:ring-1 focus:ring-red-500"
                                  style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                  placeholder="Contoh: Pelanggaran dasar keselamatan"></textarea>
                        <p class="mt-1 text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Maksimum 500 aksara</p>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-sm p-3">
                        <div class="flex">
                            <span class="material-symbols-outlined text-red-600 mr-2" style="font-size: 16px;">warning</span>
                            <p class="text-xs text-red-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Pengguna akan dilog keluar dari semua sesi dan tidak dapat log masuk sehingga akaun dibuka kunci.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6">
                    <button type="button" 
                            onclick="closeLockAccountModal()"
                            class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5"
                            style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-red-600 text-white hover:bg-red-700 transition-colors inline-flex items-center gap-1.5"
                            style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        Kunci Akaun
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

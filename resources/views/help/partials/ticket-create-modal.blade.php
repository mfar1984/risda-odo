{{-- CREATE TICKET MODAL (Staff to Admin) --}}

<div x-show="createTicketModal" 
     x-cloak
     @keydown.escape.window="createTicketModal = false"
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999 !important;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="createTicketModal = false"></div>
    
    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] my-8 flex flex-col"
             @click.away="createTicketModal = false">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-white text-[20px]">add_circle</span>
                    <div>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            Buat Tiket Baru
                        </h3>
                        <p class="text-green-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Hantar pertanyaan atau isu kepada Administrator
                        </p>
                    </div>
                </div>
                <button @click="createTicketModal = false" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            {{-- Form (Scrollable) --}}
            <form id="create-ticket-form" class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 180px);">
                @csrf
                
                {{-- Info Banner --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-6 rounded-sm">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[16px]">info</span>
                        <div class="text-[10px] text-blue-800" style="font-family: Poppins, sans-serif !important;">
                            Tiket ini akan dihantar kepada <strong>Administrator</strong> untuk tindakan lanjut. Anda akan menerima maklum balas dalam masa 24-48 jam.
                        </div>
                    </div>
                </div>

                {{-- Subject --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Subjek <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text"
                        name="subject"
                        placeholder="Masukkan subjek tiket anda..."
                        class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                        required
                    />
                </div>

                {{-- Category & Priority Row --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    
                    {{-- Category --}}
                    <div>
                        <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                            Kategori <span class="text-red-600">*</span>
                        </label>
                        <select name="category" class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Technical">Teknikal</option>
                            <option value="Account">Akaun & Akses</option>
                            <option value="Admin">Pentadbiran</option>
                            <option value="System">Sistem</option>
                            <option value="Data">Data & Laporan</option>
                            <option value="Other">Lain-lain</option>
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                            Keutamaan <span class="text-red-600">*</span>
                        </label>
                        <select name="priority" class="w-full h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important;" required>
                            <option value="">Pilih Keutamaan</option>
                            <option value="rendah">Rendah</option>
                            <option value="sederhana">Sederhana</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="kritikal">Kritikal</option>
                        </select>
                    </div>

                </div>

                {{-- Message --}}
                <div class="mb-4">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Keterangan Masalah <span class="text-red-600">*</span>
                    </label>
                    <textarea 
                        name="message"
                        rows="6"
                        placeholder="Huraikan masalah atau pertanyaan anda dengan terperinci..."
                        class="w-full px-3 py-2 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 resize-none"
                        style="font-family: Poppins, sans-serif !important; font-size: 11px !important;"
                        required
                    ></textarea>
                </div>

                {{-- Current User Info --}}
                <div class="mb-4 bg-gray-50 border border-gray-200 rounded-sm p-4">
                    <div class="text-[10px] font-medium text-gray-700 mb-3" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[14px] align-middle mr-1">badge</span>
                        Maklumat Penghantar
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-[10px]" style="font-family: Poppins, sans-serif !important;">
                        <div>
                            <span class="text-gray-500">Nama:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ auth()->user()->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Peranan:</span>
                            <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm bg-blue-100 text-blue-800 ml-2">
                                {{ auth()->user()->jenis_organisasi === 'semua' ? 'Administrator' : 'Staff' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Organisasi:</span>
                            <span class="text-gray-900 font-medium ml-2">
                                @if(auth()->user()->jenis_organisasi === 'semua')
                                    RISDA Pusat
                                @elseif(auth()->user()->jenis_organisasi === 'bahagian')
                                    {{ auth()->user()->bahagian->nama ?? 'N/A' }}
                                @elseif(auth()->user()->jenis_organisasi === 'stesen')
                                    {{ auth()->user()->stesen->nama ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Attachment --}}
                <div class="mb-6">
                    <label class="text-[11px] font-medium text-gray-700 mb-2 block" style="font-family: Poppins, sans-serif !important;">
                        Lampiran (Optional)
                    </label>
                    <input 
                        type="file" 
                        name="attachments[]" 
                        id="ticket-attachments"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx"
                        class="hidden"
                        onchange="updateFileList(this)"
                    />
                    <div onclick="document.getElementById('ticket-attachments').click()" class="border-2 border-dashed border-gray-300 rounded-sm p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-gray-400 text-[32px]">upload_file</span>
                        <div class="text-[10px] text-gray-600 mt-2" style="font-family: Poppins, sans-serif !important;">
                            Klik untuk upload
                        </div>
                        <div class="text-[9px] text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important;">
                            PDF, JPG, PNG, Excel (Max 5MB setiap file)
                        </div>
                    </div>
                    <div id="file-list" class="mt-2 space-y-1"></div>
                </div>

                <script>
                function updateFileList(input) {
                    const fileList = document.getElementById('file-list');
                    fileList.innerHTML = '';
                    
                    if (input.files.length > 0) {
                        Array.from(input.files).forEach((file, index) => {
                            const fileItem = document.createElement('div');
                            fileItem.className = 'flex items-center justify-between bg-gray-50 p-2 rounded-sm text-[10px]';
                            fileItem.style.fontFamily = 'Poppins, sans-serif';
                            fileItem.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-600 text-[14px]">description</span>
                                    <span class="text-gray-900">${file.name}</span>
                                    <span class="text-gray-500">(${(file.size / 1024).toFixed(1)} KB)</span>
                                </div>
                                <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </button>
                            `;
                            fileList.appendChild(fileItem);
                        });
                    }
                }
                
                function removeFile(index) {
                    const input = document.getElementById('ticket-attachments');
                    const dt = new DataTransfer();
                    const files = Array.from(input.files);
                    
                    files.forEach((file, i) => {
                        if (i !== index) dt.items.add(file);
                    });
                    
                    input.files = dt.files;
                    updateFileList(input);
                }
                </script>

                {{-- Quick Issue Templates --}}
                <div class="mb-4">
                    <div class="text-[10px] font-medium text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[14px] align-middle mr-1">quick_phrases</span>
                        Templat Pantas
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Akses Sistem
                        </button>
                        <button type="button" class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Masalah Teknikal
                        </button>
                        <button type="button" class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Pertanyaan Data
                        </button>
                        <button type="button" class="h-6 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            Permintaan Khas
                        </button>
                    </div>
                </div>

            </form>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                <button @click="createTicketModal = false" 
                        type="button"
                        class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" style="font-family: Poppins, sans-serif !important;">
                    Batal
                </button>
                <div class="flex gap-2">
                    <button type="submit" form="create-ticket-form" class="h-8 px-4 text-[11px] font-medium rounded-sm bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center gap-1.5" style="font-family: Poppins, sans-serif !important;">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Hantar Tiket
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


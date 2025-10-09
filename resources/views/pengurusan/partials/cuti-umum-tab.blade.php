<!-- Cuti Umum Tab (Administrator Only) -->
<div x-show="activeTab === 'cuti'" x-transition x-data="{
    viewMode: 'all',
    selectedYear: {{ date('Y') }},
    selectedNegeri: 'Sarawak',
    holidays: [],
    manualHolidays: @json($cutiOverrides),
    loading: false,
    showAddModal: false,
    editingHoliday: null,
    formData: {
        tarikh_mula: '',
        tarikh_akhir: '',
        nama_cuti: '',
        negeri: [],
        semuaNegeri: true,
        catatan: ''
    },
    allNegeriList: ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya'],
    toggleSemuaNegeri() {
        if (this.formData.semuaNegeri) {
            this.formData.negeri = [];
        }
    },
    toggleNegeriCheckbox() {
        if (this.formData.negeri.length > 0) {
            this.formData.semuaNegeri = false;
        }
    },
    async loadPreview() {
        this.loading = true;
        try {
            const response = await fetch(`/pengurusan/integrasi/cuti-umum/preview?year=${this.selectedYear}&mode=${this.viewMode}&negeri=${this.selectedNegeri}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const result = await response.json();
            if (result.success) {
                this.holidays = result.data;
            }
        } catch (error) {
            console.error('Error loading holidays:', error);
            alert('Gagal memuatkan data cuti');
        }
        this.loading = false;
    },
    async tambahCuti() {
        // Validation
        if (!this.formData.semuaNegeri && this.formData.negeri.length === 0) {
            alert('Sila pilih sekurang-kurangnya satu negeri atau tick &quot;Semua Negeri&quot;');
            return;
        }

        try {
            const response = await fetch('/pengurusan/integrasi/cuti-umum/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formData)
            });
            const result = await response.json();
            if (result.success) {
                alert(result.message || 'Cuti khas berjaya ditambah!');
                this.showAddModal = false;
                this.resetForm();
                location.reload();
            } else {
                alert('Ralat: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Gagal menambah cuti khas');
        }
    },
    async deleteCuti(id) {
        if (!confirm('Adakah anda pasti untuk padam cuti khas ini?')) return;
        
        try {
            const response = await fetch(`/pengurusan/integrasi/cuti-umum/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const result = await response.json();
            if (result.success) {
                alert('Cuti khas berjaya dipadam!');
                location.reload();
            }
        } catch (error) {
            alert('Gagal memadam cuti khas');
        }
    },
    resetForm() {
        this.formData = {
            tarikh_mula: '',
            tarikh_akhir: '',
            nama_cuti: '',
            negeri: [],
            semuaNegeri: true,
            catatan: ''
        };
        this.editingHoliday = null;
    },
    init() {
        this.loadPreview();
    }
}">
    <x-ui.container>
        <!-- Header Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-sm p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">info</span>
                <div style="font-family: Poppins, sans-serif; font-size: 12px;">
                    <p class="text-blue-800 font-semibold mb-2">Maklumat Cuti Umum</p>
                    <p class="text-blue-700">
                        Cuti umum diambil automatik dari package <code class="bg-blue-100 px-1 rounded">afiqiqmal/malaysiaholiday</code> 
                        berdasarkan negeri stesen/bahagian pemandu. Tiada konfigurasi tambahan diperlukan.
                    </p>
                    <p class="text-blue-600 text-xs mt-2">
                        💡 Untuk update tahun baharu: <code class="bg-blue-100 px-1 rounded">composer update afiqiqmal/malaysiaholiday</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <section class="mb-8">
            <h3 class="text-base font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif;">
                🔍 Semak Cuti Umum (Preview)
            </h3>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <x-forms.input-label for="preview_year" value="Tahun" />
                    <select x-model="selectedYear" @change="loadPreview()" class="form-select mt-1">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <x-forms.input-label value="Mod Paparan" />
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" x-model="viewMode" value="all" @change="loadPreview()" class="form-radio text-green-600">
                            <span class="ml-2" style="font-family: Poppins, sans-serif; font-size: 12px;">Semua Negeri</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" x-model="viewMode" value="single" @change="loadPreview()" class="form-radio text-green-600">
                            <span class="ml-2" style="font-family: Poppins, sans-serif; font-size: 12px;">Satu Negeri</span>
                        </label>
                    </div>
                </div>

                <div x-show="viewMode === 'single'">
                    <x-forms.input-label for="preview_negeri" value="Pilih Negeri" />
                    <select x-model="selectedNegeri" @change="loadPreview()" class="form-select mt-1">
                        <option value="Johor">Johor</option>
                        <option value="Kedah">Kedah</option>
                        <option value="Kelantan">Kelantan</option>
                        <option value="Melaka">Melaka</option>
                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                        <option value="Pahang">Pahang</option>
                        <option value="Penang">Penang</option>
                        <option value="Perak">Perak</option>
                        <option value="Perlis">Perlis</option>
                        <option value="Sabah">Sabah</option>
                        <option value="Sarawak">Sarawak</option>
                        <option value="Selangor">Selangor</option>
                        <option value="Terengganu">Terengganu</option>
                        <option value="Kuala Lumpur">Kuala Lumpur</option>
                        <option value="Labuan">Labuan</option>
                        <option value="Putrajaya">Putrajaya</option>
                    </select>
                </div>
            </div>

            <!-- Loading state -->
            <div x-show="loading" class="text-center py-8">
                <span class="material-symbols-outlined animate-spin text-4xl text-gray-400">refresh</span>
                <p class="text-gray-500 mt-2" style="font-family: Poppins, sans-serif; font-size: 12px;">Memuatkan data cuti...</p>
            </div>

            <!-- Preview Table: All States -->
            <div x-show="!loading && viewMode === 'all'" class="bg-white border border-gray-300 rounded-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Tarikh</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Hari</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Cuti Umum</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Negeri Yang Cuti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(h, idx) in holidays" :key="idx">
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.date"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.day"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.name"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 11px;">
                                        <span x-show="h.states.length === 16" class="text-green-700 font-semibold">✅ SEMUA NEGERI (16/16)</span>
                                        <span x-show="h.states.length < 16" x-text="h.states.join(', ') + ` (${h.states.length}/16)`"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="holidays.length === 0">
                                <td colspan="4" class="border border-gray-300 px-3 py-8 text-center text-gray-500" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    Tiada data cuti untuk tahun <span x-text="selectedYear"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Preview Table: Single State -->
            <div x-show="!loading && viewMode === 'single'" class="bg-white border border-gray-300 rounded-sm overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-300">
                    <h4 class="font-semibold text-gray-800" style="font-family: Poppins, sans-serif; font-size: 13px;">
                        <span x-text="`Cuti Umum ${selectedNegeri} ${selectedYear}`"></span>
                        <span x-text="`(${holidays.length} hari)`" class="text-gray-600 font-normal ml-2"></span>
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px; width: 15%;">Tarikh</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px; width: 12%;">Hari</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Cuti Umum</th>
                                <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px; width: 25%;">Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(h, idx) in holidays" :key="idx">
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.date"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.day"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="h.name"></td>
                                    <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 11px;">
                                        <span class="px-2 py-1 rounded-sm text-xs" 
                                              :class="h.type === 'National Holiday' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                                              x-text="h.type"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Manual Overrides Section -->
        <section>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif;">
                    ➕ Cuti Khas Tambahan (Manual)
                </h3>
                <button @click="showAddModal = true; resetForm()" class="h-8 px-3 bg-green-600 text-white rounded-sm hover:bg-green-700" style="font-family: Poppins, sans-serif; font-size: 12px;">
                    + Tambah Cuti Khas
                </button>
            </div>

            <div class="bg-white border border-gray-300 rounded-sm overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Tarikh</th>
                            <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Nama Cuti</th>
                            <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Negeri</th>
                            <th class="border border-gray-300 px-3 py-2 text-left" style="font-family: Poppins, sans-serif; font-size: 12px;">Catatan</th>
                            <th class="border border-gray-300 px-3 py-2 text-center" style="font-family: Poppins, sans-serif; font-size: 12px; width: 100px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="cuti in manualHolidays" :key="cuti.id">
                            <tr class="odd:bg-white even:bg-gray-50">
                                <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    <span x-text="cuti.tarikh_mula === cuti.tarikh_akhir ? cuti.tarikh_mula : `${cuti.tarikh_mula} - ${cuti.tarikh_akhir}`"></span>
                                </td>
                                <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;" x-text="cuti.nama_cuti"></td>
                                <td class="border border-gray-300 px-3 py-2" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    <span class="px-2 py-1 rounded-sm text-xs"
                                          :class="cuti.negeri === 'Semua' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
                                          x-text="cuti.negeri"></span>
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-gray-600" style="font-family: Poppins, sans-serif; font-size: 11px;" x-text="cuti.catatan || '-'"></td>
                                <td class="border border-gray-300 px-3 py-2 text-center">
                                    <button @click="deleteCuti(cuti.id)" class="text-red-600 hover:text-red-800" title="Padam">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="manualHolidays.length === 0">
                            <td colspan="5" class="border border-gray-300 px-3 py-6 text-center text-gray-500" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                Tiada cuti khas ditambah. Klik butang "Tambah Cuti Khas" untuk menambah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </x-ui.container>

    <!-- Add/Edit Modal -->
    <div x-show="showAddModal" 
         x-cloak
         @keydown.escape.window="showAddModal = false"
         class="fixed inset-0 overflow-y-auto" 
         style="z-index: 9999;">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showAddModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-sm shadow-xl w-full max-w-lg" @click.away="showAddModal = false">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif; font-size: 14px;">Tambah Cuti Khas</h3>
                    <button @click="showAddModal = false" class="text-white hover:text-gray-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <x-forms.input-label for="tarikh_mula" value="Tarikh Mula" />
                            <input type="date" x-model="formData.tarikh_mula" class="form-input mt-1 w-full" required>
                        </div>

                        <div>
                            <x-forms.input-label for="tarikh_akhir" value="Tarikh Akhir" />
                            <input type="date" x-model="formData.tarikh_akhir" class="form-input mt-1 w-full" required>
                            <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif;">Sama dengan Tarikh Mula jika cuti 1 hari sahaja</p>
                        </div>

                        <div>
                            <x-forms.input-label for="nama_cuti" value="Nama Cuti" />
                            <input type="text" x-model="formData.nama_cuti" class="form-input mt-1 w-full" placeholder="Contoh: Cuti Pilihan Raya" required>
                        </div>

                        <div>
                            <x-forms.input-label value="Negeri Yang Bercuti" />
                            
                            <!-- Semua Negeri checkbox -->
                            <div class="mt-2 mb-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="formData.semuaNegeri" 
                                           @change="toggleSemuaNegeri()"
                                           class="form-checkbox text-green-600 rounded">
                                    <span class="ml-2 font-semibold text-gray-800" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                        ✅ Semua Negeri (16 negeri)
                                    </span>
                                </label>
                            </div>

                            <!-- Individual state checkboxes -->
                            <div class="border border-gray-300 rounded-sm p-3 max-h-48 overflow-y-auto bg-gray-50"
                                 :class="{ 'opacity-50 pointer-events-none': formData.semuaNegeri }">
                                <p class="text-xs text-gray-500 mb-2" style="font-family: Poppins, sans-serif;">Atau pilih negeri tertentu:</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="negeri in allNegeriList" :key="negeri">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   :value="negeri"
                                                   x-model="formData.negeri"
                                                   @change="toggleNegeriCheckbox()"
                                                   class="form-checkbox text-blue-600 rounded text-sm">
                                            <span class="ml-2 text-gray-700" style="font-family: Poppins, sans-serif; font-size: 11px;" x-text="negeri"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2" style="font-family: Poppins, sans-serif;">
                                <span x-show="formData.semuaNegeri">Cuti ini akan terpakai untuk semua 16 negeri</span>
                                <span x-show="!formData.semuaNegeri && formData.negeri.length > 0" x-text="`Cuti ini akan terpakai untuk ${formData.negeri.length} negeri terpilih`"></span>
                                <span x-show="!formData.semuaNegeri && formData.negeri.length === 0" class="text-red-600">⚠️ Sila pilih sekurang-kurangnya 1 negeri</span>
                            </p>
                        </div>

                        <div>
                            <x-forms.input-label for="catatan" value="Catatan (Opsional)" />
                            <textarea x-model="formData.catatan" class="form-input mt-1 w-full" rows="2" placeholder="Contoh: Cuti khas RISDA"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <button @click="showAddModal = false" type="button" class="h-8 px-4 rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50" style="font-family: Poppins, sans-serif; font-size: 11px;">Batal</button>
                    <button @click="tambahCuti()" type="button" class="h-8 px-4 rounded-sm bg-green-600 text-white hover:bg-green-700" style="font-family: Poppins, sans-serif; font-size: 11px;">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>


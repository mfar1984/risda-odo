@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout
    title="Tambah Kenderaan"
    description="Daftarkan kenderaan baharu dalam sistem"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Kenderaan') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Daftarkan kenderaan baharu dalam sistem') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.store-kenderaan') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf

                <!-- Row 1: No. Plat & Jenama -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="no_plat" value="No. Plat" />
                        <x-forms.text-input
                            id="no_plat"
                            name="no_plat"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('no_plat') }}"
                            required
                            autofocus
                            placeholder="Contoh: ABC1234"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('no_plat')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="jenama" value="Jenama" />
                        <x-forms.text-input
                            id="jenama"
                            name="jenama"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('jenama') }}"
                            required
                            placeholder="Contoh: Toyota"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('jenama')" />
                    </div>
                </div>

                <!-- Row 2: Model & Tahun -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="model" value="Model" />
                        <x-forms.text-input
                            id="model"
                            name="model"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('model') }}"
                            required
                            placeholder="Contoh: Hilux"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('model')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="tahun" value="Tahun" />
                        <x-forms.text-input
                            id="tahun"
                            name="tahun"
                            type="number"
                            class="mt-1 block w-full"
                            value="{{ old('tahun') }}"
                            required
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            placeholder="Contoh: 2020"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('tahun')" />
                    </div>
                </div>

                <!-- Row 3: No. Enjin & No. Casis -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="no_enjin" value="No. Enjin" />
                        <x-forms.text-input
                            id="no_enjin"
                            name="no_enjin"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('no_enjin') }}"
                            required
                            placeholder="Masukkan no. enjin"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('no_enjin')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="no_casis" value="No. Casis" />
                        <x-forms.text-input
                            id="no_casis"
                            name="no_casis"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('no_casis') }}"
                            required
                            placeholder="Masukkan no. casis"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('no_casis')" />
                    </div>
                </div>

                <!-- Row 4: Jenis Bahan Api & Kapasiti Muatan -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="jenis_bahan_api" value="Jenis Bahan Api" />
                        <select
                            id="jenis_bahan_api"
                            name="jenis_bahan_api"
                            class="form-select mt-1"
                            required
                        >
                            <option value="">Pilih Jenis Bahan Api</option>
                            <option value="petrol" {{ old('jenis_bahan_api') === 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="diesel" {{ old('jenis_bahan_api') === 'diesel' ? 'selected' : '' }}>Diesel</option>
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('jenis_bahan_api')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="kapasiti_muatan" value="Kapasiti Muatan" />
                        <x-forms.text-input
                            id="kapasiti_muatan"
                            name="kapasiti_muatan"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('kapasiti_muatan') }}"
                            placeholder="Contoh: 1000kg"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('kapasiti_muatan')" />
                    </div>
                </div>

                <!-- Row 5: Warna & Status -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="warna" value="Warna" />
                        <x-forms.text-input
                            id="warna"
                            name="warna"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('warna') }}"
                            required
                            placeholder="Contoh: Putih"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('warna')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="status" value="Status" />
                        <select
                            id="status"
                            name="status"
                            class="form-select mt-1"
                            required
                        >
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="penyelenggaraan" {{ old('status') == 'penyelenggaraan' ? 'selected' : '' }}>Penyelenggaraan</option>
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>
                </div>

                <!-- Row 6: Cukai Tamat Tempoh & Tarikh Pendaftaran -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="cukai_tamat_tempoh" value="Cukai Tamat Tempoh" />
                        <x-forms.date-input
                            id="cukai_tamat_tempoh"
                            name="cukai_tamat_tempoh"
                            class="mt-1 block w-full"
                            value="{{ old('cukai_tamat_tempoh') }}"
                            required
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('cukai_tamat_tempoh')" />
                    </div>
                    <div style="flex: 1;">
                        <x-forms.input-label for="tarikh_pendaftaran" value="Tarikh Pendaftaran" />
                        <x-forms.date-input
                            id="tarikh_pendaftaran"
                            name="tarikh_pendaftaran"
                            class="mt-1 block w-full"
                            value="{{ old('tarikh_pendaftaran') }}"
                            required
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('tarikh_pendaftaran')" />
                    </div>
                </div>

                <!-- Dokumen Kenderaan -->
                <div style="margin-top: 24px;">
                    <x-forms.file-input
                        id="dokumen_kenderaan"
                        name="dokumen_kenderaan[]"
                        label="Muat Naik Dokumen / Fail Kenderaan (Opsional)"
                        accept=".pdf,.jpeg,.jpg,.png"
                        :multiple="true"
                        maxSize="10MB"
                        allowedTypes="PDF, JPEG, JPG, PNG"
                        helpText="Anda boleh memuat naik beberapa fail sekaligus dengan menyeret dan melepaskannya ke kawasan ini."
                    />
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6">
                    <x-buttons.primary-button type="submit">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                        Tambah Kenderaan
                    </x-buttons.primary-button>
                </div>
            </form>
            </section>
        </x-ui.container>
</x-dashboard-layout>

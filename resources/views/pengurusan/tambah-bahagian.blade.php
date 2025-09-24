<x-dashboard-layout 
    title="Tambah RISDA Bahagian"
    description="Tambah maklumat RISDA Bahagian baru"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('RISDA Bahagian') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Tambah informasi RISDA Bahagian') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.store-bahagian') }}" class="mt-6 space-y-6" x-data="postcodeHandler()">
                    @csrf
                
                <!-- Row 1: Nama Bahagian & No. Telefon -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="nama_bahagian" value="Nama Bahagian" />
                        <x-forms.text-input 
                            id="nama_bahagian" 
                            name="nama_bahagian" 
                            type="text" 
                            class="mt-1 block w-full" 
                            required 
                            autofocus 
                            autocomplete="organization" 
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('nama_bahagian')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="no_telefon" value="No. Telefon" />
                        <x-forms.text-input
                            id="no_telefon"
                            name="no_telefon"
                            type="tel"
                            class="mt-1 block w-full"
                            required
                            maxlength="20"
                            autocomplete="tel"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('no_telefon')" />
                    </div>
                </div>

                <!-- Row 2: No. Fax & Email -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="no_fax" value="No. Fax" />
                        <x-forms.text-input
                            id="no_fax"
                            name="no_fax"
                            type="tel"
                            class="mt-1 block w-full"
                            maxlength="20"
                            autocomplete="tel"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('no_fax')" />
                    </div>

                    <div style="flex: 1;">
                        <x-forms.input-label for="email" value="Email" />
                        <x-forms.text-input
                            id="email"
                            name="email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="email"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>
                </div>

                <!-- Row 3: Status (Dropdown) -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="status" value="Status" />
                        <select
                            id="status_dropdown"
                            name="status_dropdown"
                            class="form-select mt-1"
                            required
                        >
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="dalam_pembinaan">Dalam Pembinaan</option>
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('status_dropdown')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <!-- Empty space to maintain layout -->
                    </div>
                </div>

                <!-- Separator -->
                <div class="my-6">
                    <div class="border-t border-gray-200"></div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                        Maklumat Alamat
                    </h3>
                </div>

                <!-- Row 4: Alamat 1 & Alamat 2 -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="alamat_1" value="Alamat 1" />
                        <x-forms.text-input 
                            id="alamat_1" 
                            name="alamat_1" 
                            type="text" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="address-line1" 
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_1')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="alamat_2" value="Alamat 2" />
                        <x-forms.text-input 
                            id="alamat_2" 
                            name="alamat_2" 
                            type="text" 
                            class="mt-1 block w-full" 
                            autocomplete="address-line2" 
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_2')" />
                    </div>
                </div>

                <!-- Row 5: Poskod & Bandar -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="poskod" value="Poskod" />
                        <x-forms.text-input 
                            id="poskod" 
                            name="poskod" 
                            type="text" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="postal-code"
                            x-model="poskod"
                            @input="handlePostcodeChange()"
                            maxlength="5"
                            pattern="[0-9]{5}"
                            placeholder="Contoh: 40000"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('poskod')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="bandar" value="Bandar" />
                        <x-forms.text-input 
                            id="bandar" 
                            name="bandar" 
                            type="text" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="address-level2"
                            x-model="bandar"
                            readonly
                            placeholder="Auto-detect dari poskod"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('bandar')" />
                    </div>
                </div>

                <!-- Row 6: Negeri & Malaysia -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="negeri" value="Negeri" />
                        <x-forms.text-input 
                            id="negeri" 
                            name="negeri" 
                            type="text" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="address-level1"
                            x-model="negeri"
                            readonly
                            placeholder="Auto-detect dari poskod"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('negeri')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="negara" value="Negara" />
                        <x-forms.text-input 
                            id="negara" 
                            name="negara" 
                            type="text" 
                            class="mt-1 block w-full" 
                            value="Malaysia" 
                            readonly 
                            autocomplete="country-name" 
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('negara')" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                    <x-buttons.primary-button type="submit">
                        Simpan RISDA Bahagian
                    </x-buttons.primary-button>
                </div>
                </form>
            </section>
        </x-ui.container>

    <!-- Malaysia Postcodes Script -->
    <script src="/js/malaysia-postcodes.min.js"></script>
    <!-- Custom Postcodes Extension -->
    <script src="/js/custom-postcodes.js"></script>
    <script>
        function postcodeHandler() {
            return {
                poskod: '',
                bandar: '',
                negeri: '',

                handlePostcodeChange() {
                    if (this.poskod.length === 5) {
                        try {
                            // Use enhanced postcode finder (checks custom postcodes first)
                            const result = findPostcodeEnhanced(this.poskod);

                            if (result.found) {
                                this.bandar = result.city;
                                this.negeri = result.state;
                            } else {
                                this.bandar = '';
                                this.negeri = '';
                                console.log('Poskod tidak dijumpai:', this.poskod);
                            }
                        } catch (error) {
                            console.error('Error finding postcode:', error);
                            this.bandar = '';
                            this.negeri = '';
                        }
                    } else {
                        this.bandar = '';
                        this.negeri = '';
                    }
                }
            }
        }
    </script>
</x-dashboard-layout>

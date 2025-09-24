<x-dashboard-layout
    title="Tambah RISDA Stesen"
    description="Tambah maklumat RISDA Stesen baru"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('RISDA Stesen') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Tambah informasi RISDA Stesen') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.store-stesen') }}" class="mt-6 space-y-6" x-data="postcodeHandler()">
                    @csrf
                    
                    <!-- Row 1: RISDA Bahagian & Nama Stesen -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="risda_bahagian_id" value="RISDA Bahagian" />
                            <select
                                id="risda_bahagian_id"
                                name="risda_bahagian_id"
                                class="form-select mt-1"
                                required
                            >
                                <option value="">Pilih RISDA Bahagian</option>
                                @foreach($bahagians as $bahagian)
                                    <option value="{{ $bahagian->id }}" {{ old('risda_bahagian_id') == $bahagian->id ? 'selected' : '' }}>
                                        {{ $bahagian->nama_bahagian }}
                                    </option>
                                @endforeach
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('risda_bahagian_id')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_stesen" value="Nama Stesen" />
                            <x-forms.text-input 
                                id="nama_stesen" 
                                name="nama_stesen" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('nama_stesen') }}"
                                required 
                                autofocus 
                                autocomplete="organization" 
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('nama_stesen')" />
                        </div>
                    </div>

                    <!-- Row 2: No. Telefon & No. Fax -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_telefon" value="No. Telefon" />
                            <x-forms.text-input 
                                id="no_telefon" 
                                name="no_telefon" 
                                type="tel" 
                                class="mt-1 block w-full" 
                                value="{{ old('no_telefon') }}"
                                required 
                                autocomplete="tel" 
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_telefon')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_fax" value="No. Fax" />
                            <x-forms.text-input 
                                id="no_fax" 
                                name="no_fax" 
                                type="tel" 
                                class="mt-1 block w-full" 
                                value="{{ old('no_fax') }}"
                                autocomplete="tel" 
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_fax')" />
                        </div>
                    </div>

                    <!-- Row 3: Email & Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="email" value="Email" />
                            <x-forms.text-input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="mt-1 block w-full" 
                                value="{{ old('email') }}"
                                required 
                                autocomplete="email" 
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="status_dropdown" value="Status" />
                            <select
                                id="status_dropdown"
                                name="status_dropdown"
                                class="form-select mt-1"
                                required
                            >
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status_dropdown') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status_dropdown') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="dalam_pembinaan" {{ old('status_dropdown') == 'dalam_pembinaan' ? 'selected' : '' }}>Dalam Pembinaan</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('status_dropdown')" />
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
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
                                value="{{ old('alamat_1') }}"
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
                                value="{{ old('alamat_2') }}"
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
                                value="{{ old('poskod') }}"
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
                                value="{{ old('bandar') }}"
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
                                value="{{ old('negeri') }}"
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
                            Simpan RISDA Stesen
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>

    <!-- Malaysia Postcodes Script -->
    <script src="/js/malaysia-postcodes.min.js"></script>
    <script>
        function postcodeHandler() {
            return {
                poskod: '',
                bandar: '',
                negeri: '',

                handlePostcodeChange() {
                    if (this.poskod.length === 5) {
                        try {
                            // Use the global malaysiaPostcodes object
                            const result = malaysiaPostcodes.findPostcode(this.poskod);
                            
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

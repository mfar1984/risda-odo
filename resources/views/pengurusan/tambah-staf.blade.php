@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout 
    title="Tambah RISDA Staf"
    description="Tambah maklumat RISDA Staf baharu"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('RISDA Staf') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Tambah maklumat RISDA Staf baharu') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.store-staf') }}" class="mt-6 space-y-6" x-data="stafHandler()">
                    @csrf
                    
                    <!-- Row 1: No. Pekerja & Nama Penuh -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_pekerja" value="No. Pekerja" />
                            <x-forms.text-input 
                                id="no_pekerja" 
                                name="no_pekerja" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('no_pekerja') }}"
                                required 
                                autofocus 
                                placeholder="Contoh: EMP001"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_pekerja')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_penuh" value="Nama Penuh" />
                            <x-forms.text-input 
                                id="nama_penuh" 
                                name="nama_penuh" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('nama_penuh') }}"
                                required 
                                placeholder="Nama penuh staf"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('nama_penuh')" />
                        </div>
                    </div>

                    <!-- Row 2: No. Kad Pengenalan & Jantina -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_kad_pengenalan" value="No. Kad Pengenalan" />
                            <x-forms.text-input
                                id="no_kad_pengenalan"
                                name="no_kad_pengenalan"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ old('no_kad_pengenalan') }}"
                                required
                                x-model="icNumber"
                                @input="formatICNumber()"
                                placeholder="Contoh: 900120-13-1882"
                                maxlength="14"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_kad_pengenalan')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="jantina" value="Jantina" />
                            <select id="jantina" name="jantina" class="form-select mt-1" required>
                                <option value="">Pilih Jantina</option>
                                <option value="lelaki" {{ old('jantina') == 'lelaki' ? 'selected' : '' }}>Lelaki</option>
                                <option value="perempuan" {{ old('jantina') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('jantina')" />
                        </div>
                    </div>

                    <!-- Row 3: Bahagian & Stesen -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="bahagian_id" value="RISDA Bahagian" />
                            <select
                                id="bahagian_id"
                                name="bahagian_id"
                                class="form-select mt-1"
                                required
                                x-model="bahagianId"
                                @change="loadStesen()"
                            >
                                <option value="">Pilih RISDA Bahagian</option>
                                @foreach($bahagians as $bahagian)
                                <option value="{{ $bahagian->id }}" {{ old('bahagian_id') == $bahagian->id ? 'selected' : '' }}>
                                    {{ $bahagian->nama_bahagian }}
                                </option>
                                @endforeach
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('bahagian_id')" />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="stesen_id" value="RISDA Stesen (Pilihan)" />
                            <select
                                id="stesen_id"
                                name="stesen_id"
                                class="form-select mt-1"
                                x-model="stesenId"
                                :disabled="bahagianId === ''"
                                x-html="stesenOptions"
                            >
                            </select>
                            <p class="mt-1 text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Kosongkan untuk akses semua stesen dalam bahagian
                            </p>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('stesen_id')" />
                        </div>
                    </div>

                    <!-- Row 4: Jawatan & No. Telefon -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="jawatan" value="Jawatan" />
                            <x-forms.text-input 
                                id="jawatan" 
                                name="jawatan" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('jawatan') }}"
                                required 
                                placeholder="Contoh: Pegawai Eksekutif"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('jawatan')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_telefon" value="No. Telefon" />
                            <x-forms.text-input
                                id="no_telefon"
                                name="no_telefon"
                                type="tel"
                                class="mt-1 block w-full"
                                value="{{ old('no_telefon') }}"
                                required
                                maxlength="20"
                                placeholder="Contoh: 013-1234567"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_telefon')" />
                        </div>
                    </div>

                    <!-- Row 5: Email & No. Fax -->
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
                                placeholder="Contoh: nama@risda.gov.my"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_fax" value="No. Fax (Pilihan)" />
                            <x-forms.text-input
                                id="no_fax"
                                name="no_fax"
                                type="tel"
                                class="mt-1 block w-full"
                                value="{{ old('no_fax') }}"
                                maxlength="20"
                                placeholder="Contoh: 03-12345678"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('no_fax')" />
                        </div>
                    </div>

                    <!-- Row 6: Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status" />
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="gantung" {{ old('status') == 'gantung' ? 'selected' : '' }}>Gantung</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                        <div style="flex: 1;"></div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Alamat
                        </h3>
                    </div>

                    <!-- Row 7: Alamat 1 & Alamat 2 -->
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
                                placeholder="Alamat baris pertama"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_1')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="alamat_2" value="Alamat 2 (Pilihan)" />
                            <x-forms.text-input 
                                id="alamat_2" 
                                name="alamat_2" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('alamat_2') }}"
                                placeholder="Alamat baris kedua"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_2')" />
                        </div>
                    </div>

                    <!-- Row 8: Poskod & Bandar -->
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
                                placeholder="Contoh: 50450"
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

                    <!-- Row 9: Negeri & Negara -->
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
                                value="{{ old('negara', 'Malaysia') }}"
                                required 
                                readonly
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('negara')" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        @php
                            $backRoute = auth()->user()->jenis_organisasi === 'semua' 
                                ? route('pengurusan.senarai-risda') 
                                : route('pengurusan.senarai-staf');
                        @endphp
                        <a href="{{ $backRoute }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Batal
                            </x-buttons.secondary-button>
                        </a>
                        
                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Tambah RISDA Staf
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>

    <!-- Malaysia Postcodes Script -->
    <script src="/js/malaysia-postcodes.min.js"></script>
    <!-- Custom Postcodes Extension -->
    <script src="/js/custom-postcodes.js"></script>

    <!-- JavaScript for Dynamic Loading -->
    <script>
        function stafHandler() {
            return {
                bahagianId: '{{ old('bahagian_id') }}',
                stesenId: '{{ old('stesen_id') }}',
                availableStesen: [],
                allStesens: @json($stesens),
                stesenOptions: '<option value="">Semua Stesen dalam Bahagian</option>',
                poskod: '{{ old('poskod') }}',
                bandar: '{{ old('bandar') }}',
                negeri: '{{ old('negeri') }}',
                icNumber: '{{ old('no_kad_pengenalan') }}',

                init() {
                    // Load stesen on page load if bahagian already selected
                    if (this.bahagianId) {
                        this.loadStesen();
                    }
                },

                loadStesen() {
                    this.stesenId = '';
                    this.availableStesen = [];

                    // Reset to default option
                    this.stesenOptions = '<option value="">Semua Stesen dalam Bahagian</option>';

                    if (this.bahagianId) {
                        // Filter stesen berdasarkan bahagian yang dipilih
                        this.availableStesen = this.allStesens.filter(stesen =>
                            stesen.risda_bahagian_id == this.bahagianId
                        );

                        // Generate HTML options
                        this.availableStesen.forEach(stesen => {
                            this.stesenOptions += `<option value="${stesen.id}">${stesen.nama_stesen}</option>`;
                        });
                    }
                },

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
                },

                formatICNumber() {
                    // Remove all non-numeric characters
                    let numbers = this.icNumber.replace(/\D/g, '');

                    // Limit to 12 digits
                    if (numbers.length > 12) {
                        numbers = numbers.substring(0, 12);
                    }

                    // Format with dashes: YYMMDD-PP-GGGG
                    let formatted = '';
                    if (numbers.length > 0) {
                        formatted = numbers.substring(0, 6); // YYMMDD
                        if (numbers.length > 6) {
                            formatted += '-' + numbers.substring(6, 8); // PP
                            if (numbers.length > 8) {
                                formatted += '-' + numbers.substring(8, 12); // GGGG
                            }
                        }
                    }

                    this.icNumber = formatted;
                }
            }
        }
    </script>
</x-dashboard-layout>

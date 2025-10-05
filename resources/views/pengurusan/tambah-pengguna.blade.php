@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout
    title="Tambah Pengguna"
    description="Tambah pengguna baharu dalam sistem"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Tambah pengguna baharu dalam sistem') }}
                    </p>
                </header>

            <form method="POST" action="{{ route('pengurusan.store-pengguna') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Row 1: Staf RISDA -->
                <div>
                    <x-forms.input-label for="staf_id" value="Staf RISDA" />
                    <select 
                        id="staf_id" 
                        name="staf_id" 
                        class="form-select mt-1" 
                        required

                    >
                        <option value="">Pilih Staf RISDA</option>
                        @foreach($stafs as $staf)
                        <option value="{{ $staf->id }}" {{ old('staf_id') == $staf->id ? 'selected' : '' }}>
                            {{ $staf->nama_penuh }} ({{ $staf->no_pekerja }})
                        </option>
                        @endforeach
                    </select>
                    <x-forms.input-error class="mt-2" :messages="$errors->get('staf_id')" />
                    <p class="mt-1 text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                        Pilih staf yang akan diberikan akses ke sistem. Email staf akan digunakan sebagai username.
                    </p>
                </div>

                <!-- Row 2: Kata Laluan & Sahkan Kata Laluan -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="password" value="Kata Laluan" />
                        <x-forms.text-input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Minimum 8 aksara"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="password_confirmation" value="Sahkan Kata Laluan" />
                        <x-forms.text-input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="mt-1 block w-full" 
                            required 
                            autocomplete="new-password"
                            placeholder="Taip semula kata laluan"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                    </div>
                </div>

                <!-- Row 3: Peranan Kumpulan & Status Akaun -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="kumpulan_id" value="Peranan Kumpulan" />
                        <select
                            id="kumpulan_id"
                            name="kumpulan_id"
                            class="form-select mt-1"
                        >
                            <option value="">Semua Akses</option>
                            @foreach($kumpulans as $kumpulan)
                            <option value="{{ $kumpulan->id }}" {{ old('kumpulan_id') == $kumpulan->id ? 'selected' : '' }}>
                                {{ $kumpulan->nama_kumpulan }}
                            </option>
                            @endforeach
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('kumpulan_id')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="status_akaun" value="Status Akaun" />
                        <select 
                            id="status_akaun" 
                            name="status_akaun" 
                            class="form-select mt-1" 
                            required
                        >
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="digantung">Digantung</option>
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('status_akaun')" />
                    </div>
                </div>

                <!-- Separator -->
                <div class="my-6">
                    <div class="border-t border-gray-200"></div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                        Maklumat Akses
                    </h3>
                </div>

                <!-- Row 4: Bahagian Akses & Stesen Akses -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="bahagian_akses_id" value="Bahagian Akses" />
                        <select 
                            id="bahagian_akses_id" 
                            name="bahagian_akses_id" 
                            class="form-select mt-1" 
                            required
                            onchange="loadStesenByBahagian(this.value)"
                        >
                            <option value="">Pilih Bahagian Akses</option>
                            @php
                                $currentUser = auth()->user();
                                $isAdministrator = $currentUser && $currentUser->jenis_organisasi === 'semua';
                                $canCreateAdmin = $isAdministrator || ($currentUser && $currentUser->adaKebenaran('senarai_pengguna', 'tambah'));
                            @endphp
                            @if($isAdministrator)
                            <option value="semua" {{ old('bahagian_akses_id') == 'semua' ? 'selected' : '' }}>
                                Semua Bahagian
                            </option>
                            @endif
                            @foreach($bahagians as $bahagian)
                            <option value="{{ $bahagian->id }}" {{ old('bahagian_akses_id') == $bahagian->id ? 'selected' : '' }}>
                                {{ $bahagian->nama_bahagian }}
                            </option>
                            @endforeach
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('bahagian_akses_id')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="stesen_akses_ids" value="Stesen Akses (Pilihan)" />
                        <input
                            id="stesen_akses_ids"
                            name="stesen_akses_ids"
                            class="form-select mt-1 block w-full"
                            placeholder="Kosongkan untuk akses semua stesen dalam bahagian"
                            value="{{ old('stesen_akses_ids') }}"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('stesen_akses_ids')" />
                        <p style="font-size: 10px; color: #6b7280; margin-top: 4px; font-family: Poppins, sans-serif;">
                            Kosongkan untuk akses semua stesen dalam bahagian
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6">
                    <x-buttons.primary-button type="submit">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                        Tambah Pengguna
                    </x-buttons.primary-button>
                </div>
            </form>
        </section>
    </x-ui.container>

    <!-- Tagify CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script>
        // Global variables for dynamic stesen loading
        window.stesenData = [];
        window.stesenMapping = {};
        window.tagifyStesen = null;

        // Function to load stesen by bahagian (AJAX)
        function loadStesenByBahagian(bahagianId) {
            const stesenInput = document.getElementById('stesen_akses_ids');

            // Clear current tagify
            if (window.tagifyStesen) {
                window.tagifyStesen.removeAllTags();
                window.tagifyStesen.destroy();
                window.tagifyStesen = null;
            }

            // Reset data
            window.stesenData = [];
            window.stesenMapping = {};

            if (!bahagianId) {
                // No bahagian selected, disable stesen input
                stesenInput.disabled = true;
                stesenInput.placeholder = "Pilih Bahagian Akses dahulu";
                return;
            }

            // Handle "Semua Bahagian" selection
            if (bahagianId === 'semua') {
                stesenInput.disabled = false;
                stesenInput.placeholder = "Loading semua stesen...";

                // Fetch ALL stesen from ALL bahagians
                fetch('/pengurusan/senarai-pengguna/get-all-stesen')
                    .then(response => response.json())
                    .then(stesens => {
                        // Populate data arrays with ALL stesen
                        window.stesenData = stesens.map(stesen => stesen.nama_stesen);
                        window.stesenMapping = {};
                        stesens.forEach(stesen => {
                            window.stesenMapping[stesen.nama_stesen] = stesen.id;
                        });

                        // Initialize Tagify with ALL stesen data
                        initializeTagify();

                        // Update placeholder
                        stesenInput.placeholder = "Kosongkan untuk akses semua stesen dalam semua bahagian";
                    })
                    .catch(error => {
                        console.error('Error loading all stesen:', error);
                        stesenInput.placeholder = "Error loading stesen";
                    });
                return;
            }

            // Enable input and show loading
            stesenInput.disabled = false;
            stesenInput.placeholder = "Loading stesen...";

            // Fetch stesen by bahagian
            fetch(`/pengurusan/senarai-pengguna/get-stesen/${bahagianId}`)
                .then(response => response.json())
                .then(stesens => {
                    // Populate data arrays
                    window.stesenData = stesens.map(stesen => stesen.nama_stesen);
                    window.stesenMapping = {};
                    stesens.forEach(stesen => {
                        window.stesenMapping[stesen.nama_stesen] = stesen.id;
                    });

                    // Initialize Tagify with new data
                    initializeTagify();

                    // Update placeholder
                    stesenInput.placeholder = "Kosongkan untuk akses semua stesen dalam bahagian";
                })
                .catch(error => {
                    console.error('Error loading stesen:', error);
                    stesenInput.placeholder = "Error loading stesen";
                });
        }

        // Function to initialize Tagify
        function initializeTagify() {
            const stesenInput = document.getElementById('stesen_akses_ids');

            if (stesenInput && window.stesenData.length >= 0) { // Allow empty for "Semua Stesen" only
                // Add "Semua Stesen" option to the beginning of whitelist
                const whitelistWithSemua = ['Semua Stesen', ...window.stesenData];

                window.tagifyStesen = new Tagify(stesenInput, {
                    whitelist: whitelistWithSemua, // Include "Semua Stesen" option
                    enforceWhitelist: true,
                    skipInvalid: true,
                    maxTags: 10,
                    autoComplete: {
                        enabled: true,
                        rightKey: true
                    },
                    dropdown: {
                        enabled: 0, // Always show dropdown on focus
                        maxItems: 20,
                        classname: 'tags-look',
                        closeOnSelect: false
                    }
                });

                // Force show dropdown on focus (RISDA Pattern)
                stesenInput.addEventListener('focus', function() {
                    if (window.tagifyStesen) {
                        window.tagifyStesen.dropdown.show();
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const stesenInput = document.getElementById('stesen_akses_ids');

            // Initially disable stesen input
            stesenInput.disabled = true;
            stesenInput.placeholder = "Pilih Bahagian Akses dahulu";

            // On form submit, convert names to IDs (RISDA Pattern)
            const form = stesenInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!window.tagifyStesen) {
                        // No tagify initialized, just submit
                        stesenInput.value = JSON.stringify([]);
                        return;
                    }

                    // Get selected stesen names
                    let stesenNames = window.tagifyStesen.value.map(item => item.value);

                    // Check if "Semua Stesen" is selected
                    if (stesenNames.includes('Semua Stesen')) {
                        // If "Semua Stesen" selected, set special value
                        stesenInput.value = JSON.stringify(['semua']);
                        return;
                    }

                    // Convert names to IDs using mapping
                    let stesenIds = stesenNames.map(name => window.stesenMapping[name]).filter(id => id);

                    // Validation - check for invalid names (exclude "Semua Stesen")
                    let invalidStesen = stesenNames.filter(name => name !== 'Semua Stesen' && !window.stesenMapping[name]);
                    if (invalidStesen.length > 0) {
                        e.preventDefault();
                        alert('Stesen tidak sah: ' + invalidStesen.join(', ') + '. Sila pilih dari senarai yang tersedia.');
                        return false;
                    }

                    // Set value as JSON array of IDs
                    stesenInput.value = JSON.stringify(stesenIds);
                });
            }
        });
    </script>
</x-dashboard-layout>

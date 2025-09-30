<x-dashboard-layout 
    title="Edit Pengguna"
    description="Kemaskini maklumat pengguna"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Edit Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Kemaskini maklumat pengguna') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.update-pengguna', $pengguna) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Row 1: Nama & Email -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="name" value="Nama Pengguna" />
                            <x-forms.text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('name', $pengguna->name) }}"
                                required 
                                autofocus 
                                placeholder="Nama pengguna"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="email" value="Email" />
                            <x-forms.text-input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="mt-1 block w-full" 
                                value="{{ old('email', $pengguna->email) }}"
                                required 
                                placeholder="email@risda.gov.my"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <!-- Row 2: Kata Laluan & Sahkan Kata Laluan -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="password" value="Kata Laluan Baharu (Kosongkan jika tidak mahu tukar)" />
                            <x-forms.text-input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="mt-1 block w-full" 
                                placeholder="Kata laluan baharu"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('password')" />
                            <p style="font-size: 10px; color: #6b7280; margin-top: 4px; font-family: Poppins, sans-serif;">
                                Kosongkan jika tidak mahu menukar kata laluan
                            </p>
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="password_confirmation" value="Sahkan Kata Laluan" />
                            <x-forms.text-input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                class="mt-1 block w-full" 
                                placeholder="Sahkan kata laluan"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>
                    </div>

                    <!-- Row 3: Peranan Kumpulan & Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="kumpulan_id" value="Peranan Kumpulan" />
                            <select id="kumpulan_id" name="kumpulan_id" class="form-select mt-1">
                                <option value="" {{ old('kumpulan_id', $pengguna->kumpulan_id) == '' ? 'selected' : '' }}>Semua Akses</option>
                                @foreach($kumpulans as $kumpulan)
                                <option value="{{ $kumpulan->id }}" {{ old('kumpulan_id', $pengguna->kumpulan_id) == $kumpulan->id ? 'selected' : '' }}>
                                    {{ $kumpulan->nama_kumpulan }}
                                </option>
                                @endforeach
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('kumpulan_id')" />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status Akaun" />
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $pengguna->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status', $pengguna->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="digantung" {{ old('status', $pengguna->status) == 'digantung' ? 'selected' : '' }}>Digantung</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
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
                                @endphp
                                @if($isAdministrator)
                                <option value="semua" {{ old('bahagian_akses_id', $pengguna->jenis_organisasi == 'semua' ? 'semua' : '') == 'semua' ? 'selected' : '' }}>
                                    Semua Bahagian
                                </option>
                                @endif
                                @foreach($bahagians as $bahagian)
                                @php
                                    // Determine selected bahagian based on user's jenis_organisasi
                                    $selectedBahagianId = '';
                                    if ($pengguna->jenis_organisasi === 'semua') {
                                        $selectedBahagianId = 'semua';
                                    } elseif ($pengguna->jenis_organisasi === 'bahagian') {
                                        $selectedBahagianId = $pengguna->organisasi_id;
                                    } elseif ($pengguna->jenis_organisasi === 'stesen' && $pengguna->stesen_akses_ids) {
                                        // For stesen users, find bahagian from first stesen
                                        $firstStesenId = $pengguna->stesen_akses_ids[0] ?? null;
                                        if ($firstStesenId) {
                                            $firstStesen = $stesens->firstWhere('id', $firstStesenId);
                                            $selectedBahagianId = $firstStesen ? $firstStesen->risda_bahagian_id : '';
                                        }
                                    }
                                @endphp
                                <option value="{{ $bahagian->id }}" {{ old('bahagian_akses_id', $selectedBahagianId) == $bahagian->id ? 'selected' : '' }}>
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
                            value="{{ old('stesen_akses_ids', $pengguna->stesen_akses_ids ? implode(', ', array_map(function ($id) use ($stesens) {
                                $stesen = $stesens->firstWhere('id', $id);
                                return $stesen ? $stesen->nama_stesen : $id;
                            }, $pengguna->stesen_akses_ids)) : ($pengguna->jenis_organisasi === 'semua' ? 'Semua Stesen' : '')) }}"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('stesen_akses_ids')" />
                            <p style="font-size: 10px; color: #6b7280; margin-top: 4px; font-family: Poppins, sans-serif;">
                                Kosongkan untuk akses semua stesen dalam bahagian
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-pengguna') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Batal
                            </x-buttons.secondary-button>
                        </a>

                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Kemaskini Pengguna
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

        // Store existing stesen data for edit mode
        const existingStesenValue = '{{ old('stesen_akses_ids', $pengguna->stesen_akses_ids ? implode(', ', array_map(function($id) use ($stesens) {
            $stesen = $stesens->firstWhere('id', $id);
            return $stesen ? $stesen->nama_stesen : 'Unknown';
        }, $pengguna->stesen_akses_ids)) : '') }}';

        // Function to load stesen by bahagian (AJAX)
        function loadStesenByBahagian(bahagianId) {
            console.log('loadStesenByBahagian called with:', bahagianId);
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

                    // Pre-populate existing tags if in edit mode
                    if (existingStesenValue && existingStesenValue.trim()) {
                        const existingNames = existingStesenValue.split(',').map(name => name.trim()).filter(name => name);
                        if (existingNames.length > 0 && window.tagifyStesen) {
                            window.tagifyStesen.addTags(existingNames);
                        }
                    }
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
            const bahagianSelect = document.getElementById('bahagian_akses_id');

            // Check if bahagian already selected (edit mode)
            if (bahagianSelect.value) {
                loadStesenByBahagian(bahagianSelect.value);
            } else {
                // Initially disable stesen input
                stesenInput.disabled = true;
                stesenInput.placeholder = "Pilih Bahagian Akses dahulu";
            }

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

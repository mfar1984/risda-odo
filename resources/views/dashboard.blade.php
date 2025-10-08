<x-dashboard-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php
        // Get current user's profile for report header
        $currentUser = auth()->user();
        $currentStaf = $currentUser->risdaStaf;
        
        // Get current user's staff list for filters
        $userList = \App\Models\User::query()
            ->with('risdaStaf')
            ->when($currentUser->jenis_organisasi === 'stesen', function($q) use ($currentUser) {
                $q->where('organisasi_id', $currentUser->organisasi_id)
                  ->where('jenis_organisasi', 'stesen');
            })
            ->when($currentUser->jenis_organisasi === 'bahagian', function($q) use ($currentUser) {
                $stesenIds = \App\Models\RisdaStesen::where('bahagian_id', $currentUser->organisasi_id)
                    ->pluck('id');
                $q->where('jenis_organisasi', 'stesen')
                  ->whereIn('organisasi_id', $stesenIds);
            })
            ->orderBy('name')
            ->get();

        // Get vehicle list for filters
        $kenderaanList = \App\Models\Kenderaan::query()
            ->when($currentUser->jenis_organisasi === 'stesen', function($q) use ($currentUser) {
                $q->where('stesen_id', $currentUser->organisasi_id);
            })
            ->when($currentUser->jenis_organisasi === 'bahagian', function($q) use ($currentUser) {
                $q->where('bahagian_id', $currentUser->organisasi_id);
            })
            ->where('status', 'aktif')
            ->orderBy('no_plat')
            ->get();
    @endphp

    <!-- Dashboard Container -->
    <x-ui.page-header
        title="Jana Laporan"
        description="Penapis laporan — pilih jenis, julat tarikh, dan parameter, kemudian klik Generate"
    >
        <div x-data="{ 
            reportType: 'kenderaan', 
            profile: { 
                namaPenuh: '{{ $currentStaf ? $currentStaf->nama_penuh : $currentUser->name }}', 
                noPekerja: '{{ $currentStaf ? $currentStaf->no_pekerja : "-" }}', 
                ic: '{{ $currentStaf ? $currentStaf->no_kad_pengenalan : "-" }}', 
                tel: '{{ $currentStaf ? $currentStaf->no_telefon : "-" }}', 
                ref: '' 
            },
            vehicle: { noPlat: 'QAB1234', jenama: 'Toyota Alphard', noEnjin: 'Q18150101-HAF18159', noCasis: '749101581', cukaiTamat: '31 Disember 2025', ref: '' },
            otRows: [],
            otSummary: { totalHours: 0, totalRecords: 0 },
            tuntutanRows: [],
            tuntutanSummary: { totalAmount: 0, totalRecords: 0 },
            kenderaanRows: [],
            kenderaanSummary: { totalDistance: 0, totalRecords: 0 },
            // Staff dropdown with search
            staffList: [
                @foreach($userList as $user)
                { id: '{{ $user->id }}', name: '{{ $user->risdaStaf ? $user->risdaStaf->nama_penuh : $user->name }}' },
                @endforeach
            ],
            staffDropdownOpen: false,
            staffSearch: '',
            selectedStaffId: '',
            selectedStaffName: '- Semua Staf -',
            get filteredStaff() {
                if (!this.staffSearch) return this.staffList;
                return this.staffList.filter(s => s.name.toLowerCase().includes(this.staffSearch.toLowerCase()));
            },
            selectStaff(id, name) {
                this.selectedStaffId = id;
                this.selectedStaffName = name || '- Semua Staf -';
                this.staffDropdownOpen = false;
                this.staffSearch = '';
            },
            // Vehicle dropdown with search
            vehicleList: [
                @foreach($kenderaanList as $kenderaan)
                { id: '{{ $kenderaan->id }}', name: '{{ $kenderaan->no_plat }} - {{ $kenderaan->jenama }} {{ $kenderaan->model }}' },
                @endforeach
            ],
            vehicleDropdownOpen: false,
            vehicleSearch: '',
            selectedVehicleId: '',
            selectedVehicleName: '- Pilih Kenderaan -',
            get filteredVehicle() {
                if (!this.vehicleSearch) return this.vehicleList;
                return this.vehicleList.filter(v => v.name.toLowerCase().includes(this.vehicleSearch.toLowerCase()));
            },
            selectVehicle(id, name) {
                this.selectedVehicleId = id;
                this.selectedVehicleName = name || '- Pilih Kenderaan -';
                this.vehicleDropdownOpen = false;
                this.vehicleSearch = '';
            },
            genRef(prefix = 'OT') {
                const d = new Date();
                const p = (n) => String(n).padStart(2, '0');
                const stamp = `${d.getFullYear()}${p(d.getMonth()+1)}${p(d.getDate())}${p(d.getHours())}${p(d.getMinutes())}${p(d.getSeconds())}`;
                return `${prefix}-${stamp}-${Math.random().toString(36).slice(2,6).toUpperCase()}`;
            },
            // Helpers to compute total as hours+minutes (e.g., 5jam 45min)
            parseJamTextToMinutes(txt) {
                if (!txt || typeof txt !== 'string') return 0;
                const m1 = txt.match(/(\d+)\s*jam/i);
                const m2 = txt.match(/(\d+)\s*min/i);
                const h = m1 ? parseInt(m1[1]) : 0;
                const m = m2 ? parseInt(m2[1]) : 0;
                return h * 60 + m;
            },
            totalOtMinutes() {
                return Math.round(this.otRows.reduce((acc, r) => {
                    if (typeof r.jam === 'number') return acc + Math.round(r.jam * 60);
                    return acc + this.parseJamTextToMinutes(r.jamText);
                }, 0));
            },
            totalOtText() {
                const mins = this.totalOtMinutes();
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                return `${h}jam ${m}min`;
            },
            formatRM(amount) {
                return 'RM' + amount.toFixed(2);
            },
            async generateReport() {
                // Clear all data first
                this.otRows = [];
                this.otSummary = { totalHours: 0, totalRecords: 0 };
                this.tuntutanRows = [];
                this.tuntutanSummary = { totalAmount: 0, totalRecords: 0 };
                this.kenderaanRows = [];
                this.kenderaanSummary = { totalDistance: 0, totalRecords: 0 };

                if (this.reportType === 'ot') {
                    // OT - still dummy for now
                    this.profile.ref = this.genRef('OT');
                    this.otRows = [
                        { tarikh: '1 Oktober 2025', program: 'Program Jelajah Sarawak', mula: '17:00 ptg', tamat: '20:30 ptg', jamText: '3jam 30min', jam: 3.5 },
                        { tarikh: '2 Oktober 2025', program: 'Program Jelajah Sarawak', mula: '20:00 ptg', tamat: '11:15 ptg', jamText: '2jam 15min', jam: 2.25 }
                    ];
                    this.otSummary = { totalHours: this.otRows.reduce((a, r) => a + (r.jam || 0), 0), totalRecords: this.otRows.length };
                } else if (this.reportType === 'tuntutan') {
                    // Tuntutan - fetch real data
                    this.profile.ref = this.genRef('TT');
                    const tarikhMula = document.getElementById('tarikh_mula')?.value || '';
                    const tarikhAkhir = document.getElementById('tarikh_akhir')?.value || '';

                    const formData = new FormData();
                    formData.append('jenis_laporan', 'tuntutan');
                    if (tarikhMula) formData.append('tarikh_mula', tarikhMula);
                    if (tarikhAkhir) formData.append('tarikh_akhir', tarikhAkhir);
                    if (this.selectedStaffId) formData.append('staf_id', this.selectedStaffId);

                    try {
                        const response = await fetch('{{ route('dashboard.generate-report') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.tuntutanRows = result.data.rows;
                            this.tuntutanSummary = result.data.summary;
                            
                            // Show message if no data found
                            if (this.tuntutanRows.length === 0) {
                                // Keep arrays empty so the empty state shows
                            }
                        } else {
                            alert('Gagal menjana laporan: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Ralat semasa menjana laporan. Sila cuba lagi.');
                    }
                } else if (this.reportType === 'kenderaan') {
                    // Kenderaan - fetch real data
                    this.vehicle.ref = this.genRef('KD');
                    const tarikhMula = document.getElementById('tarikh_mula')?.value || '';
                    const tarikhAkhir = document.getElementById('tarikh_akhir')?.value || '';

                    const formData = new FormData();
                    formData.append('jenis_laporan', 'kenderaan');
                    if (tarikhMula) formData.append('tarikh_mula', tarikhMula);
                    if (tarikhAkhir) formData.append('tarikh_akhir', tarikhAkhir);
                    if (this.selectedVehicleId) formData.append('kenderaan_id', this.selectedVehicleId);

                    if (!this.selectedVehicleId) {
                        alert('Sila pilih kenderaan terlebih dahulu');
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('dashboard.generate-report') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (result.success) {
                            // Update vehicle header
                            this.vehicle.noPlat = result.data.vehicle.noPlat;
                            this.vehicle.jenama = result.data.vehicle.jenama;
                            this.vehicle.noEnjin = result.data.vehicle.noEnjin;
                            this.vehicle.noCasis = result.data.vehicle.noCasis;
                            this.vehicle.cukaiTamat = result.data.vehicle.cukaiTamat;
                            
                            this.kenderaanRows = result.data.rows;
                            this.kenderaanSummary = result.data.summary;
                        } else {
                            alert('Gagal menjana laporan: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Ralat semasa menjana laporan. Sila cuba lagi.');
                    }
                }
            }
        }">
            <!-- Grid for OT/Kenderaan/Tuntutan: 5 columns -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Col 1: Jenis Laporan -->
                <div>
                    <x-forms.input-label for="jenis_laporan" value="Jenis Laporan" />
                    <select x-model="reportType" id="jenis_laporan" name="jenis_laporan" class="form-select mt-1">
                        <option value="ot">Kerja Lebih Masa</option>
                        <option value="kenderaan">Kenderaan</option>
                        <option value="tuntutan">Tuntutan</option>
                    </select>
                </div>

                <!-- Col 2: Tarikh Mula -->
                <div>
                    <x-forms.input-label for="tarikh_mula" value="Tarikh Mula" />
                    <input type="date" id="tarikh_mula" name="tarikh_mula" class="form-input form-date-input-native mt-1" />
                </div>

                <!-- Col 3: Tarikh Akhir -->
                <div>
                    <x-forms.input-label for="tarikh_akhir" value="Tarikh Akhir" />
                    <input type="date" id="tarikh_akhir" name="tarikh_akhir" class="form-input form-date-input-native mt-1" />
                </div>

                <!-- Col 4: Parameter (Staf untuk OT/Tuntutan, Kenderaan untuk Kenderaan) -->
                <template x-if="reportType === 'ot' || reportType === 'tuntutan'">
                    <div class="relative" @click.away="staffDropdownOpen = false">
                        <x-forms.input-label for="staf_id" value="Nama Staf" />
                        <div @click="staffDropdownOpen = !staffDropdownOpen" 
                             class="form-select mt-1 cursor-pointer flex items-center"
                             style="padding-right: 2.5rem;">
                            <span x-text="selectedStaffName" class="truncate"></span>
                        </div>
                        
                        <!-- Dropdown panel with search -->
                        <div x-show="staffDropdownOpen" 
                             x-transition
                             class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-sm shadow-lg"
                             style="max-height: 300px;">
                            <!-- Search input -->
                            <div class="p-2 border-b border-gray-200">
                                <input type="text" 
                                       x-model="staffSearch" 
                                       @click.stop
                                       placeholder="Cari nama staf..."
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-green-500"
                                       style="font-family: Poppins, sans-serif; font-size: 12px;">
                            </div>
                            <!-- Options list -->
                            <div class="overflow-y-auto" style="max-height: 240px;">
                                <div @click="selectStaff('', '- Semua Staf -')" 
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                     :class="{ 'bg-green-50': selectedStaffId === '' }"
                                     style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    - Semua Staf -
                                </div>
                                <template x-for="staff in filteredStaff" :key="staff.id">
                                    <div @click="selectStaff(staff.id, staff.name)" 
                                         class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                         :class="{ 'bg-green-50': selectedStaffId === staff.id }"
                                         style="font-family: Poppins, sans-serif; font-size: 12px;"
                                         x-text="staff.name"></div>
                                </template>
                                <div x-show="filteredStaff.length === 0" class="px-3 py-2 text-sm text-gray-500 italic" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    Tiada staf dijumpai
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="reportType === 'kenderaan'">
                    <div class="relative" @click.away="vehicleDropdownOpen = false">
                        <x-forms.input-label for="kenderaan_id" value="Kenderaan" />
                        <div @click="vehicleDropdownOpen = !vehicleDropdownOpen" 
                             class="form-select mt-1 cursor-pointer flex items-center"
                             style="padding-right: 2.5rem;">
                            <span x-text="selectedVehicleName" class="truncate"></span>
                        </div>
                        
                        <!-- Dropdown panel with search -->
                        <div x-show="vehicleDropdownOpen" 
                             x-transition
                             class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-sm shadow-lg"
                             style="max-height: 300px;">
                            <!-- Search input -->
                            <div class="p-2 border-b border-gray-200">
                                <input type="text" 
                                       x-model="vehicleSearch" 
                                       @click.stop
                                       placeholder="Cari no plat atau jenama..."
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-green-500"
                                       style="font-family: Poppins, sans-serif; font-size: 12px;">
                            </div>
                            <!-- Options list -->
                            <div class="overflow-y-auto" style="max-height: 240px;">
                                <template x-for="vehicle in filteredVehicle" :key="vehicle.id">
                                    <div @click="selectVehicle(vehicle.id, vehicle.name)" 
                                         class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                                         :class="{ 'bg-green-50': selectedVehicleId === vehicle.id }"
                                         style="font-family: Poppins, sans-serif; font-size: 12px;"
                                         x-text="vehicle.name"></div>
                                </template>
                                <div x-show="filteredVehicle.length === 0" class="px-3 py-2 text-sm text-gray-500 italic" style="font-family: Poppins, sans-serif; font-size: 12px;">
                                    Tiada kenderaan dijumpai
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Col 5: Generate Button -->
                <div>
                    <label class="form-label invisible">Action</label>
                    <button @click="generateReport()" type="button" class="w-full h-8 bg-green-600 text-white rounded-sm hover:bg-green-700 transition-colors mt-1" style="font-family: Poppins, sans-serif; font-size: 12px;">Generate</button>
                </div>
            </div>

            <!-- Result Area (OT Table) -->
            <div class="mt-6 border border-gray-300 rounded-sm bg-gray-50 p-4">
                 <template x-if="reportType === 'ot' && otRows.length">
                     <div class="space-y-4">
                        <!-- Header block (3-grid) -->
                        <div class="space-y-2 text-gray-800" style="font-size: 12px;">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-5 flex items-baseline">
                                    <span class="font-semibold w-32">Nama Penuh</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.namaPenuh"></span>
                                </div>
                                <div class="md:col-span-3 flex items-baseline">
                                    <span class="font-semibold w-28">No Pekerja</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.noPekerja"></span>
                                </div>
                                <div class="md:col-span-4 flex items-baseline md:items-center min-w-0">
                                    <span class="font-semibold w-16">Ref</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700 truncate flex-1" x-text="profile.ref"></span>
                                    <button type="button" class="ml-2 md:ml-3 text-red-600 hover:text-red-700 flex-shrink-0" title="Eksport PDF (Landskap)">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-5 flex items-baseline">
                                    <span class="font-semibold w-32">Kad Pengenalan</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.ic"></span>
                                </div>
                                <div class="md:col-span-3 flex items-baseline">
                                    <span class="font-semibold w-28">No Tel</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.tel"></span>
                                </div>
                                <div class="md:col-span-4"></div>
                            </div>
                        </div>
                        
                        <!-- OT table -->
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Tarikh</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Nama Program</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Masa Mula</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Masa Tamat</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Jam(OT)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(r, idx) in otRows" :key="idx">
                                        <tr class="odd:bg-white even:bg-gray-50">
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikh"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.program"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.mula"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tamat"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.jamText"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td class="border-t border-gray-300 px-3 py-2" colspan="3"></td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2" style="font-size: 12px; white-space: nowrap;">
                                            <span class="font-semibold">Jumlah Jam OT</span>
                                        </td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2 font-semibold" style="font-size: 12px;" x-text="totalOtText()"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
 
                        <div class="text-gray-800" style="font-size: 12px;">
                            <div>Jumlah Rekod: <span x-text="otSummary.totalRecords"></span></div>
                        </div>
                    </div>
                </template>

                <!-- Tuntutan Report -->
                <template x-if="reportType === 'tuntutan' && tuntutanRows.length">
                    <div class="space-y-4">
                        <!-- Header block (3-grid) -->
                        <div class="space-y-2 text-gray-800" style="font-size: 12px;">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-5 flex items-baseline">
                                    <span class="font-semibold w-32">Nama Penuh</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.namaPenuh"></span>
                                </div>
                                <div class="md:col-span-3 flex items-baseline">
                                    <span class="font-semibold w-28">No Pekerja</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.noPekerja"></span>
                                </div>
                                <div class="md:col-span-4 flex items-baseline md:items-center min-w-0">
                                    <span class="font-semibold w-16">Ref</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700 truncate flex-1" x-text="profile.ref"></span>
                                    <button type="button" class="ml-2 md:ml-3 text-red-600 hover:text-red-700 flex-shrink-0" title="Eksport PDF (Landskap)">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-5 flex items-baseline">
                                    <span class="font-semibold w-32">Kad Pengenalan</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.ic"></span>
                                </div>
                                <div class="md:col-span-3 flex items-baseline">
                                    <span class="font-semibold w-28">No Tel</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="profile.tel"></span>
                                </div>
                                <div class="md:col-span-4"></div>
                            </div>
                        </div>
                        
                        <!-- Tuntutan table -->
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Tarikh</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Nama Program</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Tarikh Dituntut</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Jenis Tuntutan</th>
                                        <th class="border border-gray-300 px-3 py-2 text-left text-gray-800" style="font-size: 12px;">Diluluskan Oleh</th>
                                        <th class="border border-gray-300 px-3 py-2 text-right text-gray-800" style="font-size: 12px;">Jumlah (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(r, idx) in tuntutanRows" :key="idx">
                                        <tr class="odd:bg-white even:bg-gray-50">
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikh"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.program"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikhDituntut"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.jenis"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.diluluskanOleh"></td>
                                            <td class="border border-gray-300 px-3 py-2 text-right" style="font-size: 12px;" x-text="formatRM(r.jumlah)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td class="border-t border-gray-300 px-3 py-2" colspan="4"></td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2" style="font-size: 12px; white-space: nowrap;">
                                            <span class="font-semibold">Jumlah Tuntutan</span>
                                        </td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2 font-semibold text-right" style="font-size: 12px;" x-text="formatRM(tuntutanSummary.totalAmount)"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-gray-800" style="font-size: 12px;">
                            <div>Jumlah Rekod: <span x-text="tuntutanSummary.totalRecords"></span></div>
                        </div>
                    </div>
                </template>

                <!-- Kenderaan Report -->
                <template x-if="reportType === 'kenderaan' && kenderaanRows.length">
                    <div class="space-y-4">
                        <!-- Header block (vehicle info) -->
                        <div class="space-y-2 text-gray-800" style="font-size: 12px;">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-28">No. Plat</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="vehicle.noPlat"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-36">Jenama / Model</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="vehicle.jenama"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-32">No. Rujukan</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700 truncate flex-1" x-text="vehicle.ref"></span>
                                    <button type="button" class="ml-2 md:ml-3 text-red-600 hover:text-red-700 flex-shrink-0" title="Eksport PDF (Landskap)">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-28">No. Enjin</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="vehicle.noEnjin"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-36">No. Casis</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="vehicle.noCasis"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-32">Tamat Cukai</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="vehicle.cukaiTamat"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kenderaan table -->
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 12px; width: 10%;">Tarikh</th>
                                        <th class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 12px; width: 15%;">Pemandu</th>
                                        <th class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 12px; width: 20%;">Program</th>
                                        <th class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 12px; width: 22%;">Daftar Masuk</th>
                                        <th class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 12px; width: 22%;">Daftar Keluar</th>
                                        <th class="border border-gray-300 px-2 py-2 text-right text-gray-800" style="font-size: 12px; width: 11%;">Jarak (KM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(r, idx) in kenderaanRows" :key="idx">
                                        <tr class="odd:bg-white even:bg-gray-50">
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikhMasa"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.pemandu"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.program"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;">
                                                <div class="flex flex-col">
                                                    <a :href="`https://www.google.com/maps?q=${r.daftarMasukLat},${r.daftarMasukLong}`" 
                                                       target="_blank" 
                                                       class="text-gray-800 hover:text-gray-900 cursor-pointer text-xs"
                                                       x-text="`${r.daftarMasukLat}, ${r.daftarMasukLong}`"></a>
                                                    <span class="text-gray-600 text-xs mt-0.5" x-text="r.daftarMasukMasa"></span>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;">
                                                <div class="flex flex-col">
                                                    <a :href="`https://www.google.com/maps?q=${r.daftarKeluarLat},${r.daftarKeluarLong}`" 
                                                       target="_blank" 
                                                       class="text-gray-800 hover:text-gray-900 cursor-pointer text-xs"
                                                       x-text="`${r.daftarKeluarLat}, ${r.daftarKeluarLong}`"></a>
                                                    <span class="text-gray-600 text-xs mt-0.5" x-text="r.daftarKeluarMasa"></span>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-right" style="font-size: 12px;" x-text="r.jarak.toFixed(1)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td class="border-t border-gray-300 px-3 py-2" colspan="4"></td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2" style="font-size: 12px; white-space: nowrap;">
                                            <span class="font-semibold">Jumlah Jarak</span>
                                        </td>
                                        <td class="border-x border-t border-b border-gray-300 px-3 py-2 font-semibold text-right" style="font-size: 12px;" x-text="kenderaanSummary.totalDistance.toFixed(1) + ' KM'"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-gray-800" style="font-size: 12px;">
                            <div>Jumlah Rekod: <span x-text="kenderaanSummary.totalRecords"></span></div>
                        </div>
                    </div>
                </template>

                <!-- Empty state for no results after generate (Tuntutan) -->
                <template x-if="reportType === 'tuntutan' && tuntutanRows.length === 0 && tuntutanSummary.totalRecords === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                        <span class="material-symbols-outlined text-6xl mb-4 text-gray-300">description</span>
                        <p class="text-base font-semibold text-gray-700 mb-1" style="font-family: Poppins, sans-serif;">Tiada Rekod Dijumpai</p>
                        <p class="text-sm text-gray-500" style="font-family: Poppins, sans-serif;">Tiada tuntutan diluluskan untuk tempoh yang dipilih.</p>
                        <p class="text-xs text-gray-400 mt-2" style="font-family: Poppins, sans-serif;">Sila ubah julat tarikh atau pilihan staf dan cuba lagi.</p>
                    </div>
                </template>

                <!-- Empty state for no results after generate (Kenderaan) -->
                <template x-if="reportType === 'kenderaan' && kenderaanRows.length === 0 && kenderaanSummary.totalRecords === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                        <span class="material-symbols-outlined text-6xl mb-4 text-gray-300">directions_car</span>
                        <p class="text-base font-semibold text-gray-700 mb-1" style="font-family: Poppins, sans-serif;">Tiada Rekod Dijumpai</p>
                        <p class="text-sm text-gray-500" style="font-family: Poppins, sans-serif;">Tiada log perjalanan untuk kenderaan ini pada tempoh yang dipilih.</p>
                        <p class="text-xs text-gray-400 mt-2" style="font-family: Poppins, sans-serif;">Sila ubah julat tarikh atau pilih kenderaan lain dan cuba lagi.</p>
                    </div>
                </template>

                <!-- Default empty state -->
                <template x-if="!(reportType === 'ot' && otRows.length) && !(reportType === 'tuntutan' && tuntutanRows.length) && !(reportType === 'kenderaan' && kenderaanRows.length) && !(reportType === 'tuntutan' && tuntutanSummary.totalRecords === 0) && !(reportType === 'kenderaan' && kenderaanSummary.totalRecords === 0)">
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <span class="material-symbols-outlined text-5xl mb-3 text-gray-400">filter_alt</span>
                        <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif;">Tiada hasil. Sila pilih penapis dan klik Generate.</p>
                    </div>
                </template>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

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
                $stesenIds = \App\Models\RisdaStesen::where('risda_bahagian_id', $currentUser->organisasi_id)
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
            loadingReport: false,
            snapshotModalOpen: false,
            snapshotList: [
                // Mockup data; will be replaced with API data later
                { noSiri: 'A 316321', bulan: '10/2025', noPlat: 'QSR43', jenis: 'Toyota Alphard', disimpanOleh: 'Admin', tarikhSimpan: '09/10/2025 22:30' },
                { noSiri: 'A 316322', bulan: '10/2025', noPlat: 'QSR43', jenis: 'Toyota Alphard', disimpanOleh: 'Admin', tarikhSimpan: '09/10/2025 22:31' }
            ],
            vehicle: { noPlat: 'QAB1234', jenama: 'Toyota Alphard', noEnjin: 'Q18150101-HAF18159', noCasis: '749101581', cukaiTamat: '31 Disember 2025', ref: '' },
            otRows: [],
            otSummary: { totalHours: 0, totalRecords: 0 },
            otGroups: [],
            tuntutanRows: [],
            tuntutanSummary: { totalAmount: 0, totalRecords: 0 },
            kenderaanRows: [],
            kenderaanSummary: { totalDistance: 0, totalRecords: 0 },
            penggunaanKenderaanRows: [],
            penggunaanKenderaanSummary: { bulan: 0, totalJarak: 0, totalLiter: 0, totalKos: 0, kadarPenggunaan: 0, totalRecords: 0, disahkanOleh: { nama: '-', jawatan: '-' } },
            penggunaanKenderaanVehicle: { noPlat: '', jenama: '', jenis: '', bahagian: '' },
            // Snapshots
            async fetchSnapshots() {
                try {
                    const url = '{{ route('api.snapshots.vehicle-usage.index') }}';
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const json = await res.json();
                    if (json.success) this.snapshotList = json.data; else this.snapshotList = [];
                } catch (e) { console.error(e); this.snapshotList = []; }
            },
            async saveSnapshot() {
                try {
                    const body = {
                        kenderaan_id: this.selectedVehicleId,
                        bulan: (this.penggunaanKenderaanSummary && this.penggunaanKenderaanSummary.bulan_iso) || (new Date()).toISOString().slice(0,10),
                        header: this.penggunaanKenderaanVehicle,
                        rows: this.penggunaanKenderaanRows,
                        summary: this.penggunaanKenderaanSummary,
                    };
                    const res = await fetch('{{ route('api.snapshots.vehicle-usage.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(body)
                    });
                    const json = await res.json();
                    if (json.success) {
                        await this.fetchSnapshots();
                        const d = json.data;
                        const msg = d.numPages && d.numPages > 1
                            ? `Snapshot disimpan: ${d.noSiri} (${d.numPages} halaman, ${d.noSiriFrom} - ${d.noSiriTo})`
                            : `Snapshot disimpan: ${d.noSiri}`;
                        alert(msg);
                    } else {
                        alert('Gagal simpan snapshot');
                    }
                } catch (e) { console.error(e); alert('Ralat simpan snapshot'); }
            },
            async deleteSnapshot(id) {
                if (!confirm('Padam snapshot ini?')) return;
                try {
                    await fetch(`{{ url('/api/snapshots/vehicle-usage') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                    await this.fetchSnapshots();
                } catch (e) { console.error(e); }
            },
            // Staff dropdown with search
            staffList: [
                @foreach($userList as $user)
                { 
                    id: '{{ $user->id }}', 
                    name: '{{ $user->risdaStaf ? addslashes($user->risdaStaf->nama_penuh) : addslashes($user->name) }}',
                    ic: '{{ $user->risdaStaf ? $user->risdaStaf->no_kad_pengenalan : '-' }}',
                    tel: '{{ $user->risdaStaf ? $user->risdaStaf->no_telefon : '-' }}',
                    noPekerja: '{{ $user->risdaStaf ? $user->risdaStaf->no_pekerja : '-' }}'
                },
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
                // Update profile header when a specific staff is chosen
                if (id) {
                    const s = this.staffList.find(x => String(x.id) === String(id));
                    if (s) {
                        this.profile.namaPenuh = s.name || this.profile.namaPenuh;
                        this.profile.noPekerja = s.noPekerja || '-';
                        this.profile.ic = s.ic || '-';
                        this.profile.tel = s.tel || '-';
                    }
                }
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
            // Normalize browser date input (handles 'YYYY-MM-DD' and 'DD - MM - YYYY')
            normalizeDateInput(value) {
                if (!value) return '';
                // Already ISO
                if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;
                // Pattern like '01 - 09 - 2025'
                const m = value.match(/^(\d{1,2})\s*-\s*(\d{1,2})\s*-\s*(\d{4})$/);
                if (m) {
                    const d = m[1].padStart(2, '0');
                    const mo = m[2].padStart(2, '0');
                    const y = m[3];
                    return `${y}-${mo}-${d}`;
                }
                // Fallback try parse
                const dt = new Date(value);
                if (!isNaN(dt.getTime())) {
                    const p = (n) => String(n).padStart(2, '0');
                    return `${dt.getFullYear()}-${p(dt.getMonth()+1)}-${p(dt.getDate())}`;
                }
                return value;
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
            computeOtText(rows) {
                if (!Array.isArray(rows)) return '0jam 0min';
                const mins = rows.reduce((acc, r) => {
                    if (typeof r.jam === 'number') return acc + Math.round(r.jam * 60);
                    if (typeof r.jamText === 'string') {
                        const m1 = r.jamText.match(/(\d+)\s*jam/i);
                        const m2 = r.jamText.match(/(\d+)\s*min/i);
                        const h = m1 ? parseInt(m1[1]) : 0;
                        const m = m2 ? parseInt(m2[1]) : 0;
                        return acc + (h * 60 + m);
                    }
                    return acc;
                }, 0);
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
                this.loadingReport = true;

                if (this.reportType === 'ot') {
                    // OT - fetch real data
                    this.profile.ref = this.genRef('OT');
                    const tarikhMula = this.normalizeDateInput(document.getElementById('tarikh_mula')?.value || '');
                    const tarikhAkhir = this.normalizeDateInput(document.getElementById('tarikh_akhir')?.value || '');

                    const formData = new FormData();
                    formData.append('jenis_laporan', 'ot');
                    if (tarikhMula) formData.append('tarikh_mula', tarikhMula);
                    if (tarikhAkhir) formData.append('tarikh_akhir', tarikhAkhir);
                    if (this.selectedStaffId) formData.append('staf_id', this.selectedStaffId);

                    try {
                        const response = await fetch('{{ route('dashboard.generate-report') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error('HTTP ' + response.status + ': ' + text.slice(0, 200));
                        }
                        const result = await response.json();
                        if (result.success) {
                            this.otGroups = Array.isArray(result.data.groups) ? result.data.groups : [];
                            if (this.otGroups.length) {
                                this.otRows = [];
                                this.otSummary = { totalHours: 0, totalRecords: 0 };
                            } else {
                                this.otRows = result.data.rows || [];
                                this.otSummary = result.data.summary || { totalHours: 0, totalRecords: this.otRows.length };
                            }
                        } else {
                            alert('Gagal menjana laporan: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Ralat semasa menjana laporan. Sila cuba lagi.');
                    } finally {
                        this.loadingReport = false;
                    }
                } else if (this.reportType === 'tuntutan') {
                    // Tuntutan - fetch real data
                    this.profile.ref = this.genRef('TT');
                    const tarikhMula = this.normalizeDateInput(document.getElementById('tarikh_mula')?.value || '');
                    const tarikhAkhir = this.normalizeDateInput(document.getElementById('tarikh_akhir')?.value || '');

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
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error('HTTP ' + response.status + ': ' + text.slice(0, 200));
                        }
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
                    } finally {
                        this.loadingReport = false;
                    }
                } else if (this.reportType === 'penggunaan_kenderaan') {
                    // Penggunaan Kenderaan - fetch real data
                    const tarikhMula = this.normalizeDateInput(document.getElementById('tarikh_mula')?.value || '');
                    const tarikhAkhir = this.normalizeDateInput(document.getElementById('tarikh_akhir')?.value || '');

                    const formData = new FormData();
                    formData.append('jenis_laporan', 'penggunaan_kenderaan');
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
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error('HTTP ' + response.status + ': ' + text.slice(0, 200));
                        }
                        const result = await response.json();
                        if (result.success) {
                            this.penggunaanKenderaanVehicle = result.data.vehicle;
                            this.penggunaanKenderaanRows = result.data.rows;
                            this.penggunaanKenderaanSummary = result.data.summary;
                        } else {
                            alert('Gagal menjana laporan: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Ralat semasa menjana laporan. Sila cuba lagi.');
                    } finally {
                        this.loadingReport = false;
                    }
                } else if (this.reportType === 'kenderaan') {
                    // Kenderaan - fetch real data
                    this.vehicle.ref = this.genRef('KD');
                    const tarikhMula = this.normalizeDateInput(document.getElementById('tarikh_mula')?.value || '');
                    const tarikhAkhir = this.normalizeDateInput(document.getElementById('tarikh_akhir')?.value || '');

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
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            throw new Error('HTTP ' + response.status + ': ' + text.slice(0, 200));
                        }
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
                    } finally {
                        this.loadingReport = false;
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
                        <option value="penggunaan_kenderaan">Penggunaan Kenderaan</option>
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

                <!-- Col 4: Parameter (dynamic based on report type) -->
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

                <template x-if="reportType === 'kenderaan' || reportType === 'penggunaan_kenderaan'">
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

                <!-- Col 5: Actions (Generate | Snapshot | Save) -->
                <div>
                    <label class="form-label invisible">Tindakan</label>
                    <!-- For Penggunaan Kenderaan: show 3-button layout -->
                    <template x-if="reportType === 'penggunaan_kenderaan'">
                        <div class="mt-1 flex items-center gap-2 w-full">
                            <button @click="generateReport()" type="button" class="h-8 w-28 bg-green-600 text-white rounded-sm hover:bg-green-700 transition-colors" style="font-family: Poppins, sans-serif; font-size: 12px;">Generate</button>
                            <button type="button" @click="snapshotModalOpen = true; fetchSnapshots()" class="h-8 w-28 bg-slate-600 text-white rounded-sm hover:bg-slate-700 transition-colors" style="font-family: Poppins, sans-serif; font-size: 12px;">Snapshot</button>
                            <button type="button" @click="saveSnapshot()" x-show="!loadingReport && Array.isArray(penggunaanKenderaanRows) && penggunaanKenderaanRows.length" class="h-8 w-28 bg-amber-600 text-white rounded-sm hover:bg-amber-700 transition-colors" style="font-family: Poppins, sans-serif; font-size: 12px;" x-cloak>Save</button>
                        </div>
                    </template>
                    <!-- For other report types: keep original full-width Generate -->
                    <template x-if="reportType !== 'penggunaan_kenderaan'">
                        <button @click="generateReport()" type="button" class="w-full h-8 bg-green-600 text-white rounded-sm hover:bg-green-700 transition-colors mt-1" style="font-family: Poppins, sans-serif; font-size: 12px;">Generate</button>
                    </template>
                </div>
            </div>

            <!-- Result Area (Single-table reports: OT single-staff, Tuntutan, Kenderaan) -->
            <div class="mt-6 border border-gray-300 rounded-sm bg-gray-50 p-4 relative" x-show="!(reportType === 'ot' && Array.isArray(otGroups) && otGroups.length)">
                <!-- Loading overlay -->
                <div x-show="loadingReport" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10" x-cloak>
                    <div class="flex items-center gap-3 text-gray-600">
                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                        <span style="font-family: Poppins, sans-serif; font-size: 12px;">Menjana laporan...</span>
                    </div>
                </div>
                <template x-if="reportType === 'ot' && Array.isArray(otRows) && otRows.length">
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
                                        <tr :class="r.bgColor || 'bg-white'">
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikh"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.program"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.mula"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tamat"></td>
                                            <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;">
                                                <div class="flex items-center justify-between">
                                                    <span x-text="r.jamText"></span>
                                                    <span class="text-xs text-gray-500 ml-2" x-text="`(${r.multiplier}x)`"></span>
                                                </div>
                                            </td>
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
                            <!-- Legend for OT row highlights -->
                            <div class="mt-2 flex items-center gap-4 text-gray-700" style="font-family: Poppins, sans-serif; font-size: 11px;">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-white border border-gray-300 rounded-sm"></span>
                                    <span>Hari Bekerja (1.5x)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-yellow-50 border border-yellow-200 rounded-sm"></span>
                                    <span>Hujung Minggu (2.0x)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3 h-3 bg-red-50 border border-red-200 rounded-sm"></span>
                                    <span>Cuti Umum (3.0x)</span>
                                </div>
                            </div>
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

                <!-- Penggunaan Kenderaan Report -->
                <template x-if="reportType === 'penggunaan_kenderaan' && penggunaanKenderaanRows.length">
                    <div class="space-y-4">
                        <!-- Header Info -->
                        <div class="space-y-2 text-gray-800" style="font-size: 12px;">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-bold text-gray-900 flex-1 text-center" style="font-size: 14px;">BUTIR-BUTIR PENGGUNAAN KENDERAAN</h2>
                                <div class="text-right" style="font-size: 11px;">
                                    <span class="text-gray-700">No. Siri : </span>
                                    <span class="text-red-600 font-semibold" style="font-size: 16px;">A 316321</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-40">Jenis Kenderaan</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="penggunaanKenderaanVehicle.jenis"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-40">No. Pendaftaran</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="penggunaanKenderaanVehicle.noPlat"></span>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="font-semibold w-40">Bahagian/Unit</span>
                                    <span class="w-3 text-center">:</span>
                                    <span class="text-gray-700" x-text="penggunaanKenderaanVehicle.bahagian"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Table with 2-row header -->
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 70px;">Tarikh</th>
                                        <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Masa</th>
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 120px;">Nama Pemandu</th>
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-left text-gray-800" style="font-size: 11px; min-width: 150px;">Tujuan & Destinasi (dari — ke)</th>
                                        <th colspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Nama Tandatangan</th>
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px; min-width: 80px;">Bacaan Odometer (KM)</th>
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px; min-width: 90px;">Jarak Perjalanan / Trip Meter (KM)</th>
                                        <th colspan="3" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px;">Pembelian Bahan Api (Petrol/Diesel/Gas)</th>
                                        <th rowspan="2" class="border border-gray-300 px-2 py-2 text-center text-gray-800" style="font-size: 11px; min-width: 100px;">Arahan Khas Pengguna Kenderaan</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 60px;">Mulai</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 60px;">Hingga</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 140px;">Pelulus</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; min-width: 140px;">Pengguna</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; width: 10%;">No. Resit</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; width: 10%;">RM</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-gray-800" style="font-size: 10px; width: 10%;">Liter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(r, idx) in penggunaanKenderaanRows" :key="idx">
                                        <tr class="odd:bg-white even:bg-gray-50">
                                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;" x-text="r.tarikh"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;" x-text="r.masaMulai"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;" x-text="r.masaHingga"></td>
                                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;" x-text="r.pemandu"></td>
                                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;">
                                                <div class="text-xs leading-relaxed">
                                                    <div x-text="r.destinasiDari"></div>
                                                    <div class="text-gray-500">ke</div>
                                                    <div x-text="r.destinasiKe"></div>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;" x-text="r.pelulus"></td>
                                            <td class="border border-gray-300 px-2 py-2" style="font-size: 11px;"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;">
                                                <div x-text="r.odometerKeluar + ' KM'"></div>
                                                <div class="my-1"></div>
                                                <div x-text="r.odometerMasuk + ' KM'"></div>
                                            </td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;" x-text="r.jarakPerjalanan + ' KM'"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;" x-text="r.resitNo"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;" x-text="r.resitRM"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-right" style="font-size: 11px;" x-text="r.liter"></td>
                                            <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;" x-text="r.arahanKhas"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td class="border-t border-gray-300 px-2 py-2" colspan="6"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-center font-semibold" style="font-size: 11px;">JUMLAH</td>
                                        <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-center font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.totalJarak.toFixed(0) + ' KM'"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-right font-semibold" style="font-size: 11px;" x-text="'RM ' + penggunaanKenderaanSummary.totalKos.toFixed(2)"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-right font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.totalLiter.toFixed(2) + ' L'"></td>
                                        <td class="border border-gray-300 px-2 py-2 text-center" style="font-size: 11px;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Summary Footer Table -->
                        <div class="mt-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3 text-center" style="font-size: 12px;">KADAR PENGGUNAAN BAHAN API BULANAN</h3>
                            <div class="w-full overflow-x-auto">
                                <table class="min-w-full table-auto border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 8%;">Bulan</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 15%;">
                                                <div>Jumlah Jarak</div>
                                                <div>Perjalanan (KM)</div>
                                            </th>
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 15%;">
                                                <div>Jumlah Penggunaan</div>
                                                <div>Bahan Api (Liter)</div>
                                            </th>
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 15%;">
                                                <div>Jumlah Pembelian</div>
                                                <div>Bahan Api (RM)</div>
                                            </th>
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 15%;">
                                                <div>Kadar Penggunaan</div>
                                                <div>Bahan Api (KM/Liter)</div>
                                            </th>
                                            <th class="border border-gray-300 px-3 py-2 text-center text-gray-800" style="font-size: 11px; width: 32%;">Disahkan Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-white">
                                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.bulan"></td>
                                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.totalJarak.toFixed(1) + ' KM'"></td>
                                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.totalLiter.toFixed(2) + ' Liter'"></td>
                                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold" style="font-size: 11px;" x-text="'RM ' + penggunaanKenderaanSummary.totalKos.toFixed(2)"></td>
                                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold" style="font-size: 11px;" x-text="penggunaanKenderaanSummary.kadarPenggunaan.toFixed(2) + ' KM/Liter'"></td>
                                            <td class="border border-gray-300 px-3 py-3 align-bottom" style="font-size: 11px; vertical-align: bottom;">
                                                <div class="flex flex-col gap-0 text-left" style="min-height: 80px; justify-content: flex-end;">
                                                    <div class="flex items-baseline py-0.5">
                                                        <span class="font-medium w-24">Tandatangan</span>
                                                        <span class="w-3 text-center">:</span>
                                                        <span class="text-gray-700"></span>
                                                    </div>
                                                    <div class="flex items-baseline py-0.5">
                                                        <span class="font-medium w-24">Nama</span>
                                                        <span class="w-3 text-center">:</span>
                                                        <span class="text-gray-700"></span>
                                                    </div>
                                                    <div class="flex items-baseline py-0.5">
                                                        <span class="font-medium w-24">Jawatan</span>
                                                        <span class="w-3 text-center">:</span>
                                                        <span class="text-gray-700"></span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Record count & notes -->
                        <div class="text-gray-800" style="font-size: 12px;">
                            <div>Jumlah Rekod: <span x-text="penggunaanKenderaanSummary.totalRecords"></span></div>
                            <div class="mt-2 text-gray-600 italic" style="font-size: 10px;">
                                <p>* Potong yang tidak berkenaan</p>
                                <p>** Formula Pengiraan: Kadar = Jumlah Jarak / Jumlah Liter</p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty state for no results after generate (Penggunaan Kenderaan) -->
                <template x-if="reportType === 'penggunaan_kenderaan' && penggunaanKenderaanRows.length === 0 && penggunaanKenderaanSummary.totalRecords === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                        <span class="material-symbols-outlined text-6xl mb-4 text-gray-300">local_shipping</span>
                        <p class="text-base font-semibold text-gray-700 mb-1" style="font-family: Poppins, sans-serif;">Tiada Rekod Dijumpai</p>
                        <p class="text-sm text-gray-500" style="font-family: Poppins, sans-serif;">Tiada log penggunaan kenderaan pada tempoh yang dipilih.</p>
                        <p class="text-xs text-gray-400 mt-2" style="font-family: Poppins, sans-serif;">Sila ubah julat tarikh atau pilih kenderaan lain dan cuba lagi.</p>
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

                <!-- Empty state for no results after generate (OT) - hide when groups exist -->
                <template x-if="reportType === 'ot' && (!Array.isArray(otGroups) || otGroups.length === 0) && Array.isArray(otRows) && otRows.length === 0 && otSummary.totalRecords === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                        <span class="material-symbols-outlined text-6xl mb-4 text-gray-300">schedule</span>
                        <p class="text-base font-semibold text-gray-700 mb-1" style="font-family: Poppins, sans-serif;">Tiada Rekod Dijumpai</p>
                        <p class="text-sm text-gray-500" style="font-family: Poppins, sans-serif;">Tiada kerja lebih masa untuk tempoh yang dipilih.</p>
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
                <template x-if="!(reportType === 'ot' && Array.isArray(otRows) && otRows.length) && !(reportType === 'tuntutan' && Array.isArray(tuntutanRows) && tuntutanRows.length) && !(reportType === 'kenderaan' && Array.isArray(kenderaanRows) && kenderaanRows.length) && (otSummary && otSummary.totalRecords !== 0) && (tuntutanSummary && tuntutanSummary.totalRecords !== 0) && (kenderaanSummary && kenderaanSummary.totalRecords !== 0)">
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <span class="material-symbols-outlined text-5xl mb-3 text-gray-400">filter_alt</span>
                        <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif;">Tiada hasil. Sila pilih penapis dan klik Generate.</p>
                    </div>
                </template>
            </div>

            <!-- OT Grouped (Semua Staf) - each staff in separate grey container outside main Result Area -->
            <template x-if="reportType === 'ot' && Array.isArray(otGroups) && otGroups.length">
                <div class="space-y-6 mt-6">
                    <template x-for="(g, gidx) in otGroups" :key="gidx">
                        <div class="border border-gray-300 rounded-sm bg-gray-50 p-4">
                            <div class="space-y-4">
                                <!-- Header block -->
                                <div class="space-y-2 text-gray-800" style="font-size: 12px;">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-5 flex items-baseline">
                                            <span class="font-semibold w-32">Nama Penuh</span>
                                            <span class="w-3 text-center">:</span>
                                            <span class="text-gray-700" x-text="g.profile.namaPenuh"></span>
                                        </div>
                                        <div class="md:col-span-3 flex items-baseline">
                                            <span class="font-semibold w-28">No Pekerja</span>
                                            <span class="w-3 text-center">:</span>
                                            <span class="text-gray-700" x-text="g.profile.noPekerja"></span>
                                        </div>
                                        <div class="md:col-span-4 flex items-baseline md:items-center min-w-0">
                                            <span class="font-semibold w-16">Ref</span>
                                            <span class="w-3 text-center">:</span>
                                            <span class="text-gray-700 truncate flex-1" x-text="genRef('OT')"></span>
                                            <button type="button" class="ml-2 md:ml-3 text-red-600 hover:text-red-700 flex-shrink-0" title="Eksport PDF (Landskap)">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-5 flex items-baseline">
                                            <span class="font-semibold w-32">Kad Pengenalan</span>
                                            <span class="w-3 text-center">:</span>
                                            <span class="text-gray-700" x-text="g.profile.ic"></span>
                                        </div>
                                        <div class="md:col-span-3 flex items-baseline">
                                            <span class="font-semibold w-28">No Tel</span>
                                            <span class="w-3 text-center">:</span>
                                            <span class="text-gray-700" x-text="g.profile.tel"></span>
                                        </div>
                                        <div class="md:col-span-4"></div>
                                    </div>
                                </div>

                                <!-- Table per staff -->
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
                                            <template x-for="(r, idx) in g.rows" :key="idx">
                                                <tr :class="r.bgColor || 'bg-white'">
                                                    <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tarikh"></td>
                                                    <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.program"></td>
                                                    <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.mula"></td>
                                                    <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;" x-text="r.tamat"></td>
                                                    <td class="border border-gray-300 px-3 py-2" style="font-size: 12px;">
                                                        <div class="flex items-center justify-between">
                                                            <span x-text="r.jamText"></span>
                                                            <span class="text-xs text-gray-500 ml-2" x-text="`(${r.multiplier}x)`"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50">
                                                <td class="border-t border-gray-300 px-3 py-2" colspan="3"></td>
                                                <td class="border-x border-t border-b border-gray-300 px-3 py-2" style="font-size: 12px; white-space: nowrap;">
                                                    <span class="font-semibold">Jumlah Jam OT</span>
                                                </td>
                                                <td class="border-x border-t border-b border-gray-300 px-3 py-2 font-semibold" style="font-size: 12px;" x-text="computeOtText(g.rows)"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="text-gray-800" style="font-size: 12px;">
                                    <div>Jumlah Rekod: <span x-text="g.summary.totalRecords"></span></div>
                                    <div class="mt-2 flex items-center gap-4 text-gray-700" style="font-family: Poppins, sans-serif; font-size: 11px;">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 bg-white border border-gray-300 rounded-sm"></span>
                                            <span>Hari Bekerja (1.5x)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 bg-yellow-50 border border-yellow-200 rounded-sm"></span>
                                            <span>Hujung Minggu (2.0x)</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 bg-red-50 border border-red-200 rounded-sm"></span>
                                            <span>Cuti Umum (3.0x)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <!-- Snapshot History Modal (Mockup only) using Support modal pattern -->
            <div x-show="snapshotModalOpen"
                 x-cloak
                 @keydown.escape.window="snapshotModalOpen = false"
                 class="fixed inset-0 overflow-y-auto"
                 style="display: none; z-index: 9999 !important;">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                     @click="snapshotModalOpen = false"></div>

                <!-- Modal -->
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="relative bg-white rounded-sm shadow-xl w-full max-w-5xl max-h-[85vh] my-8"
                         @click.away="snapshotModalOpen = false">

                        <!-- Header -->
                        <div class="support-modal-header">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-white text-[20px]">history</span>
                                <div>
                                    <h3 class="support-modal-title">Sejarah Snapshot Penggunaan Kenderaan</h3>
                                    <p class="support-modal-subtitle">Senarai snapshot yang telah disimpan</p>
                                </div>
                            </div>
                            <button @click="snapshotModalOpen = false" class="text-white hover:text-gray-200">
                                <span class="material-symbols-outlined text-[24px]">close</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="support-modal-body" style="max-height: calc(85vh - 180px);">
                            <div class="data-table-container">
                                <x-ui.data-table
                                    :headers="[
                                        ['label' => 'No. Siri', 'align' => 'text-left'],
                                        ['label' => 'Bulan', 'align' => 'text-left'],
                                        ['label' => 'No. Pendaftaran', 'align' => 'text-left'],
                                        ['label' => 'Jenis Kenderaan', 'align' => 'text-left'],
                                        ['label' => 'Disimpan Oleh', 'align' => 'text-left'],
                                        ['label' => 'Tarikh Simpan', 'align' => 'text-left']
                                    ]"
                                >
                                    <template x-for="(s, idx) in snapshotList" :key="idx">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 flex items-center gap-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                                    <span x-text="s.noSiri"></span>
                                                    <template x-if="s.noSiriFrom && s.noSiriTo && s.noSiriFrom !== s.noSiriTo">
                                                        <span class="text-xs text-gray-500" x-text="`(${s.noSiriFrom} - ${s.noSiriTo})`"></span>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;" x-text="s.bulan"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;" x-text="s.noPlat"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;" x-text="s.jenis"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;" x-text="s.disimpanOleh"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;" x-text="s.tarikhSimpan"></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center space-x-2">
                                                    <button type="button" class="text-slate-600 hover:text-slate-900" title="Guna Snapshot" @click="(async () => { try { const res = await fetch(`{{ url('/api/snapshots/vehicle-usage') }}/${s.id}`); const json = await res.json(); if (json.success) { const data = json.data; reportType = 'penggunaan_kenderaan'; penggunaanKenderaanVehicle = data.header || penggunaanKenderaanVehicle; penggunaanKenderaanRows = data.rows || []; penggunaanKenderaanSummary = Object.assign({}, penggunaanKenderaanSummary, data.summary || {}); snapshotModalOpen = false; } else { alert('Gagal memuat snapshot'); } } catch(e){ console.error(e); alert('Ralat memuat snapshot'); } })()">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">task_alt</span>
                                                    </button>
                                                    <a :href="`{{ url('/api/snapshots/vehicle-usage') }}/${s.id}/pdf`" target="_blank" class="text-blue-600 hover:text-blue-900" title="Lihat PDF">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                                                    </a>
                                                    <button type="button" class="text-red-600 hover:text-red-900" title="Padam" @click="deleteSnapshot(s.id)">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="!snapshotList || snapshotList.length === 0">
                                        <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada snapshot direkodkan.</td>
                                    </tr>
                                </x-ui.data-table>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="support-modal-footer">
                            <div></div>
                            <button @click="snapshotModalOpen = false" class="support-btn support-btn-secondary">Tutup</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

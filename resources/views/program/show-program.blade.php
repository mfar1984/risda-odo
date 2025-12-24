<x-dashboard-layout title="Lihat Program">
    <x-ui.page-header
        title="Maklumat Program"
        description="Maklumat terperinci program"
    >
        <!-- Header with Export Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Empty div for spacing -->
            </div>
            @if(auth()->user()->adaKebenaran('program', 'eksport'))
                <x-buttons.primary-button type="button" onclick="exportProgram()">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">download</span>
                    Export
                </x-buttons.primary-button>
            @endif
        </div>

                <div class="mt-6 space-y-6">
                    @php
                        $tetapanUmum = $tetapanUmum ?? \App\Models\TetapanUmum::getForCurrentUser();
                        $hasProgramCoordinates = $program->lokasi_lat && $program->lokasi_long;
                    @endphp

                    <!-- Row 1: Nama Program & Lokasi Program -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_program" value="Nama Program" />
                            <x-forms.text-input
                                id="nama_program"
                                name="nama_program"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $program->nama_program }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="lokasi_program" value="Lokasi Program" />
                            <x-forms.text-input
                                id="lokasi_program"
                                name="lokasi_program"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $program->lokasi_program }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="jarak_anggaran" value="Anggaran KM" />
                            <x-forms.text-input
                                id="jarak_anggaran"
                                name="jarak_anggaran"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $program->jarak_anggaran ? formatNombor($program->jarak_anggaran, 1) . ' km' : '-' }}"
                                readonly
                            />
                        </div>
                    </div>

                    @if($hasProgramCoordinates)
                        <div class="mt-4">
                            <x-forms.input-label value="Koordinat" />
                            <x-forms.text-input
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ formatNombor($program->lokasi_lat, 6) }}, {{ formatNombor($program->lokasi_long, 6) }}"
                                readonly
                            />
                        </div>

                        <div class="mt-4">
                            <div id="program-map-{{ $program->id }}" class="w-full rounded-lg border border-gray-200" style="height: 320px"></div>
                        </div>

                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const containerId = 'program-map-{{ $program->id }}';
                                    const lat = {{ $program->lokasi_lat }};
                                    const lng = {{ $program->lokasi_long }};
                                    const provider = @json($tetapanUmum->map_provider ?? 'openstreetmap');
                                    const apiKey = @json($tetapanUmum->map_api_key ?? null);
                                    const styleUrl = @json($tetapanUmum->map_style_url ?? null);

                                    if (!window.L) {
                                        console.error('Leaflet tidak dimuatkan.');
                                        return;
                                    }

                                    const map = window.L.map(containerId).setView([lat, lng], 15);

                                    if (provider === 'maptiler' && apiKey) {
                                        const styleId = (function () {
                                            if (!styleUrl) {
                                                return 'openstreetmap';
                                            }
                                            try {
                                                const url = new URL(styleUrl);
                                                const parts = url.pathname.split('/').filter(Boolean);
                                                const idx = parts.indexOf('maps');
                                                if (idx !== -1 && parts.length > idx + 1) {
                                                    return parts[idx + 1];
                                                }
                                            } catch (error) {
                                                console.warn('Gagal mengekstrak gaya MapTiler:', error);
                                            }
                                            return 'openstreetmap';
                                        })();

                                        window.L.tileLayer(`https://api.maptiler.com/maps/${styleId}/256/{z}/{x}/{y}.png?key=${apiKey}`, {
                                            attribution: '© MapTiler © OpenStreetMap contributors',
                                            maxZoom: 19,
                                            crossOrigin: true,
                                        }).addTo(map);
                                    } else {
                                        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            attribution: '© OpenStreetMap contributors',
                                            maxZoom: 19,
                                        }).addTo(map);
                                    }

                                    window.L.marker([lat, lng]).addTo(map);
                                });
                            </script>
                        @endpush
                    @endif

                    <!-- Row 2: Tarikh Mula & Tarikh Selesai -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="tarikh_mula" value="Tarikh & Masa Mula" />
                            <x-forms.text-input
                                id="tarikh_mula"
                                name="tarikh_mula"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ formatTarikhMasa($program->tarikh_mula) }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="tarikh_selesai" value="Tarikh & Masa Selesai" />
                            <x-forms.text-input
                                id="tarikh_selesai"
                                name="tarikh_selesai"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ formatTarikhMasa($program->tarikh_selesai) }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 3: Penerangan -->
                    <div>
                        <x-forms.input-label for="penerangan" value="Penerangan" />
                        <textarea
                            id="penerangan"
                            name="penerangan"
                            class="mt-1 block w-full form-input"
                            rows="3"
                            readonly
                        >{{ $program->penerangan ?? 'Tiada penerangan' }}</textarea>
                    </div>

                    <!-- Row 3b: Arahan Khas Pengguna Kenderaan -->
                    <div>
                        <x-forms.input-label for="arahan_khas_pengguna_kenderaan" value="Arahan Khas Pengguna Kenderaan" />
                        <textarea
                            id="arahan_khas_pengguna_kenderaan"
                            name="arahan_khas_pengguna_kenderaan"
                            class="mt-1 block w-full form-input"
                            rows="2"
                            readonly
                        >{{ $program->arahan_khas_pengguna_kenderaan ?? '-' }}</textarea>
                    </div>

                    <!-- Row 4: Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status" />
                            <div class="mt-1">
                                @if($program->status === 'draf')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        Draf
                                    </span>
                                @elseif($program->status === 'lulus')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Lulus
                                    </span>
                                @elseif($program->status === 'tolak')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Tolak
                                    </span>
                                @elseif($program->status === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Aktif
                                    </span>
                                @elseif($program->status === 'tertunda')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Tertunda
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="flex: 1;"></div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Pemohon Program
                        </h3>
                    </div>

                    <!-- Table: Maklumat Pemohon Program -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-blue-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">RISDA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jawatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Permohonan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr>
                                    <td data-label="Nama:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $program->pemohon->nama_penuh ?? 'N/A' }}
                                    </td>
                                    <td data-label="RISDA:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->pemohon && $program->pemohon->bahagian)
                                            {{ $program->pemohon->bahagian->nama_bahagian }}
                                            @if($program->pemohon->stesen)
                                                <br><span class="text-gray-500">{{ $program->pemohon->stesen->nama_stesen }}</span>
                                            @else
                                                <br><span class="text-gray-500">Semua Stesen</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Jawatan:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $program->pemohon->jawatan ?? 'N/A' }}
                                    </td>
                                    <td data-label="Permohonan:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ formatTarikh($program->created_at) }}
                                    </td>
                                    <td data-label="Kelulusan:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->tarikh_kelulusan)
                                            {{ formatTarikhMasa($program->tarikh_kelulusan) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Pemandu Program
                        </h3>
                    </div>

                    <!-- Table: Maklumat Pemandu Program -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-blue-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">RISDA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">No Tel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tarikh Aktif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tarikh Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr>
                                    <td data-label="Nama:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $program->pemandu->nama_penuh ?? 'N/A' }}
                                    </td>
                                    <td data-label="RISDA:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->pemandu && $program->pemandu->bahagian)
                                            {{ $program->pemandu->bahagian->nama_bahagian }}
                                            @if($program->pemandu->stesen)
                                                <br><span class="text-gray-500">{{ $program->pemandu->stesen->nama_stesen }}</span>
                                            @else
                                                <br><span class="text-gray-500">Semua Stesen</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="No Tel:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $program->pemandu->no_telefon ?? 'N/A' }}
                                    </td>
                                    <td data-label="Tarikh Aktif:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->tarikh_mula_aktif)
                                            {{ formatTarikhMasa($program->tarikh_mula_aktif) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td data-label="Tarikh Selesai:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->tarikh_sebenar_selesai)
                                            {{ formatTarikhMasa($program->tarikh_sebenar_selesai) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Kenderaan
                        </h3>
                    </div>

                    <!-- Table: Maklumat Kenderaan -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-blue-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">No. Plat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jenama & Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kapasiti & Muatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Cukai Tamat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr>
                                    <td data-label="No. Plat:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $program->kenderaan->no_plat ?? 'N/A' }}
                                    </td>
                                    <td data-label="Jenama & Model:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->kenderaan)
                                            {{ $program->kenderaan->jenama }} {{ $program->kenderaan->model }}
                                            <br><span class="text-gray-500">{{ $program->kenderaan->tahun }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Kapasiti:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->kenderaan && $program->kenderaan->kapasiti_muatan)
                                            {{ $program->kenderaan->kapasiti_muatan }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Cukai Tamat:" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        @if($program->kenderaan && $program->kenderaan->cukai_tamat_tempoh)
                                            {{ formatTarikh($program->kenderaan->cukai_tamat_tempoh) }}
                                            @if($program->kenderaan->is_cukai_expired)
                                                <br><span class="text-red-500 text-xs">Tamat Tempoh</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Audit
                        </h3>
                    </div>

                    <!-- Row 7: Created & Updated -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="created_at" value="Dicipta Pada" />
                            <x-forms.text-input
                                id="created_at"
                                name="created_at"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ formatTarikhMasa($program->created_at) }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="updated_at" value="Dikemaskini Pada" />
                            <x-forms.text-input
                                id="updated_at"
                                name="updated_at"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ formatTarikhMasa($program->updated_at) }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('program.index') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>

                        <div class="flex space-x-3">
                            @if(auth()->user()->adaKebenaran('program', 'kemaskini'))
                                <a href="{{ route('edit-program', $program) }}">
                                    <x-buttons.warning-button type="button">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                        Edit
                                    </x-buttons.warning-button>
                                </a>
                            @endif

                            @if(auth()->user()->adaKebenaran('program', 'padam'))
                                <x-buttons.danger-button type="button" onclick="deleteProgramItem({{ $program->id }})">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">delete</span>
                                    Padam
                                </x-buttons.danger-button>
                            @endif
                        </div>
                    </div>
                </div>
    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>

<script>
function exportProgram() {
    // You can implement different export formats here
    const programId = {{ $program->id }};
    const programName = "{{ $program->nama_program }}";

    // For now, let's create a simple text export
    const exportData = {
        'Nama Program': "{{ $program->nama_program }}",
        'Lokasi Program': "{{ $program->lokasi_program }}",
        'Tarikh Mula': "{{ formatTarikhMasa($program->tarikh_mula) }}",
        'Tarikh Selesai': "{{ formatTarikhMasa($program->tarikh_selesai) }}",
        'Status': "{{ $program->status_label }}",
        'Penerangan': "{{ $program->penerangan ?? 'Tiada penerangan' }}",
        'Pemohon': "{{ $program->pemohon->nama_penuh ?? 'N/A' }}",
        'Pemandu': "{{ $program->pemandu->nama_penuh ?? 'N/A' }}",
        'Kenderaan': "{{ $program->kenderaan->no_plat ?? 'N/A' }}"
    };

    // Convert to JSON and download
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `program_${programId}_${programName.replace(/\s+/g, '_')}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    // Log export activity via AJAX
    fetch('{{ route("program.log-export", $program) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            filename: `program_${programId}_${programName.replace(/\s+/g, '_')}.json`,
            format: 'json'
        })
    }).catch(error => console.error('Failed to log export:', error));
}
</script>

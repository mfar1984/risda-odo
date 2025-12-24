@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Log Pemandu">
    <x-ui.page-header
        title="Log Pemandu"
        description="Pemantauan rekod perjalanan pemandu yang disegerakkan daripada aplikasi mudah alih"
    >
        @php
            $tabLabels = [
                'semua' => 'Semua Log',
                'aktif' => 'Log Aktif',
                'selesai' => 'Log Selesai',
                'tertunda' => 'Log Tertunda',
            ];

            $tabIcons = [
                'semua' => 'list_alt',
                'aktif' => 'directions_car',
                'selesai' => 'task_alt',
                'tertunda' => 'pending_actions',
            ];
        @endphp

        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach($tabLabels as $key => $label)
                        @continue(!($canViewTab($key)))
                        @php
                            $isActive = $activeTab === $key;
                        @endphp
                        <a
                            href="{{ $tabUrls[$key] ?? '#' }}"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; {{ $isActive ? 'border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'border-bottom: 3px solid transparent !important; color: #6b7280 !important;' }}"
                        >
                            <span class="material-symbols-outlined" style="font-size: 16px;">{{ $tabIcons[$key] }}</span>
                            <span>{{ $label }}</span>
                            <span class="ml-1 inline-flex items-center justify-center rounded-full bg-gray-100 text-gray-700 px-2 py-0.5 text-xs" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                {{ number_format($tabCounts[$key] ?? 0) }}
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <form method="GET" action="{{ route('log-pemandu.index') }}" class="mb-6">
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <x-forms.text-input
                        id="search"
                        name="search"
                        type="text"
                        class="block w-full h-9"
                        value="{{ request('search') }}"
                        placeholder="Cari pemandu, kenderaan atau destinasi"
                        style="height: 36px; min-height: 36px;"
                    />
                </div>

                <div class="w-48">
                    <x-forms.date-input
                        id="tarikh_dari"
                        name="tarikh_dari"
                        :value="request('tarikh_dari')"
                        placeholder="Tarikh Dari"
                        class="block w-full"
                    />
                </div>

                <div class="w-48">
                    <x-forms.date-input
                        id="tarikh_hingga"
                        name="tarikh_hingga"
                        :value="request('tarikh_hingga')"
                        placeholder="Tarikh Hingga"
                        class="block w-full"
                    />
                </div>

                <div class="flex space-x-3">
                    <x-buttons.primary-button type="submit">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">search</span>
                        Cari
                    </x-buttons.primary-button>
                    <a href="{{ route('log-pemandu.index', ['tab' => $activeTab]) }}">
                        <x-buttons.danger-button type="button">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">refresh</span>
                            Reset
                        </x-buttons.danger-button>
                    </a>
                </div>
            </div>
        </form>

        <x-ui.data-table
            :headers="[
                ['label' => 'Tarikh & Masa', 'align' => 'text-left'],
                ['label' => 'Pemandu & Destinasi', 'align' => 'text-left'],
                ['label' => 'Kenderaan', 'align' => 'text-left'],
                ['label' => 'Odometer (km)', 'align' => 'text-left'],
                ['label' => 'Minyak', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-left'],
            ]"
            :actions="auth()->user()?->adaKebenaran('log_pemandu', 'lihat_butiran') || auth()->user()?->adaKebenaran('log_pemandu', 'kemaskini_status') || auth()->user()?->adaKebenaran('log_pemandu', 'padam')"
            empty-message="Tiada log ditemui untuk penapis semasa."
        >
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ formatTarikh($log->tarikh_perjalanan) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Keluar: {{ $log->masa_keluar ? formatMasa($log->masa_keluar) : '-' }}
                            @if($log->masa_masuk)
                                &bull; Masuk: {{ formatMasa($log->masa_masuk) }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $log->pemandu->risdaStaf->nama_penuh ?? 'Tidak Dinyatakan' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Destinasi: {{ $log->destinasi ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $log->kenderaan->no_plat ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            {{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) ?: '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Keluar: {{ formatNombor($log->odometer_keluar ?? 0) }} km
                        </div>
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Masuk: {{ $log->odometer_masuk ? formatNombor($log->odometer_masuk) . ' km' : '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Jarak: {{ $log->jarak ? formatNombor($log->jarak) . ' km' : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $log->liter_minyak ? formatNombor($log->liter_minyak, 2) . ' L' : '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Kos: {{ $log->kos_minyak ? formatWang($log->kos_minyak) : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.status-badge
                            :status="$log->status"
                            :status-map="[
                                'dalam_perjalanan' => ['label' => 'Sedang Berjalan', 'class' => 'bg-blue-100 text-blue-800'],
                                'selesai' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                'tertunda' => ['label' => 'Tertunda', 'class' => 'bg-orange-100 text-orange-800'],
                            ]"
                        />
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Dikemaskini: {{ optional($log->updated_at)->diffForHumans() }}
                        </div>
                    </td>
                    @php
                        $user = auth()->user();
                        $statusModule = match($log->status) {
                            'dalam_perjalanan' => 'log_pemandu_aktif',
                            'selesai' => 'log_pemandu_selesai',
                            'tertunda' => 'log_pemandu_tertunda',
                            default => null,
                        };

                        $canView = $user && (
                            $user->adaKebenaran('log_pemandu_semua', 'lihat') ||
                            ($statusModule && $user->adaKebenaran($statusModule, 'lihat')) ||
                            $user->adaKebenaran('log_pemandu', 'lihat') ||
                            $user->adaKebenaran('log_pemandu', 'lihat_butiran')
                        );

                        $canEdit = $user && (
                            $user->adaKebenaran('log_pemandu_semua', 'kemaskini') ||
                            ($statusModule && $user->adaKebenaran($statusModule, 'kemaskini')) ||
                            $user->adaKebenaran('log_pemandu', 'kemaskini') ||
                            $user->adaKebenaran('log_pemandu', 'kemaskini_status')
                        );

                        $canDelete = $user && (
                            $user->adaKebenaran('log_pemandu_semua', 'padam') ||
                            ($statusModule && $user->adaKebenaran($statusModule, 'padam')) ||
                            $user->adaKebenaran('log_pemandu', 'padam')
                        );

                        $hasActions = $canView || $canEdit || $canDelete;
                    @endphp
                    @if($hasActions)
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                @if($canView)
                                    <a href="{{ route('log-pemandu.show', $log) }}" class="text-blue-600 hover:text-blue-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                    </a>
                                @endif

                                @if($canEdit)
                                    <a href="{{ route('log-pemandu.edit', $log) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                    </a>
                                @endif

                                @if($canDelete)
                                    <button onclick="deleteLogPemanduItem({{ $log->id }})" class="text-red-600 hover:text-red-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                @php
                    $user = auth()->user();
                    $hasActions = $user && (
                        $user->adaKebenaran('log_pemandu_semua', 'lihat') ||
                        $user->adaKebenaran('log_pemandu_semua', 'kemaskini') ||
                        $user->adaKebenaran('log_pemandu_semua', 'padam') ||
                        $user->adaKebenaran('log_pemandu', 'lihat') ||
                        $user->adaKebenaran('log_pemandu', 'lihat_butiran') ||
                        $user->adaKebenaran('log_pemandu', 'kemaskini') ||
                        $user->adaKebenaran('log_pemandu', 'kemaskini_status') ||
                        $user->adaKebenaran('log_pemandu', 'padam')
                    );
                @endphp
                <td colspan="{{ 6 + ($hasActions ? 1 : 0) }}" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Tiada log ditemui. Sila cuba ubah penapis atau semak status lain.
                </td>
            @endforelse
        </x-ui.data-table>

        {{-- Mobile Card View --}}
        <div class="mobile-table-card">
            @forelse($logs as $log)
                @php
                    $user = auth()->user();
                    $statusModule = match($log->status) {
                        'dalam_perjalanan' => 'log_pemandu_aktif',
                        'selesai' => 'log_pemandu_selesai',
                        'tertunda' => 'log_pemandu_tertunda',
                        default => null,
                    };

                    $canView = $user && (
                        $user->adaKebenaran('log_pemandu_semua', 'lihat') ||
                        ($statusModule && $user->adaKebenaran($statusModule, 'lihat')) ||
                        $user->adaKebenaran('log_pemandu', 'lihat') ||
                        $user->adaKebenaran('log_pemandu', 'lihat_butiran')
                    );

                    $canEdit = $user && (
                        $user->adaKebenaran('log_pemandu_semua', 'kemaskini') ||
                        ($statusModule && $user->adaKebenaran($statusModule, 'kemaskini')) ||
                        $user->adaKebenaran('log_pemandu', 'kemaskini') ||
                        $user->adaKebenaran('log_pemandu', 'kemaskini_status')
                    );

                    $canDelete = $user && (
                        $user->adaKebenaran('log_pemandu_semua', 'padam') ||
                        ($statusModule && $user->adaKebenaran($statusModule, 'padam')) ||
                        $user->adaKebenaran('log_pemandu', 'padam')
                    );
                @endphp

                <div class="mobile-card">
                    {{-- Card Header: Date & Status --}}
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">
                            <div style="font-weight: 600; font-size: 12px;">{{ formatTarikh($log->tarikh_perjalanan) }}</div>
                            <div style="font-size: 10px; color: #6b7280; margin-top: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 12px; vertical-align: middle;">schedule</span>
                                {{ $log->masa_keluar ? formatMasa($log->masa_keluar) : '-' }}
                                @if($log->masa_masuk)
                                    → {{ formatMasa($log->masa_masuk) }}
                                @endif
                            </div>
                        </div>
                        <x-ui.status-badge
                            :status="$log->status"
                            :status-map="[
                                'dalam_perjalanan' => ['label' => 'Berjalan', 'class' => 'bg-blue-100 text-blue-800'],
                                'selesai' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                'tertunda' => ['label' => 'Tertunda', 'class' => 'bg-orange-100 text-orange-800'],
                            ]"
                        />
                    </div>

                    {{-- Card Body --}}
                    <div class="mobile-card-body">
                        {{-- Driver --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">person</span>
                            </span>
                            <span class="mobile-card-value">
                                <strong style="color: #111827;">{{ $log->pemandu->risdaStaf->nama_penuh ?? 'Tidak Dinyatakan' }}</strong>
                            </span>
                        </div>

                        {{-- Destination --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">location_on</span>
                            </span>
                            <span class="mobile-card-value">
                                {{ $log->destinasi ?? '-' }}
                            </span>
                        </div>

                        {{-- Vehicle --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">directions_car</span>
                            </span>
                            <span class="mobile-card-value">
                                <strong>{{ $log->kenderaan->no_plat ?? 'N/A' }}</strong>
                                @if(trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')))
                                    <div class="mobile-card-value-secondary">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) }}</div>
                                @endif
                            </span>
                        </div>

                        {{-- Odometer --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">speed</span>
                            </span>
                            <span class="mobile-card-value">
                                <div>Keluar: <strong>{{ formatNombor($log->odometer_keluar ?? 0) }}</strong> km</div>
                                <div>Masuk: <strong>{{ $log->odometer_masuk ? formatNombor($log->odometer_masuk) : '-' }}</strong> {{ $log->odometer_masuk ? 'km' : '' }}</div>
                                @if($log->jarak)
                                    <div class="mobile-card-value-secondary">Jarak: {{ formatNombor($log->jarak) }} km</div>
                                @endif
                            </span>
                        </div>

                        {{-- Fuel --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">local_gas_station</span>
                            </span>
                            <span class="mobile-card-value">
                                <div>{{ $log->liter_minyak ? formatNombor($log->liter_minyak, 2) . ' L' : '-' }}</div>
                                @if($log->kos_minyak)
                                    <div class="mobile-card-value-secondary">{{ formatWang($log->kos_minyak) }}</div>
                                @endif
                            </span>
                        </div>

                        {{-- Last Updated --}}
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #6b7280;">schedule</span>
                            </span>
                            <span class="mobile-card-value-secondary">
                                {{ optional($log->updated_at)->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Footer: Actions --}}
                    @if($canView || $canEdit || $canDelete)
                        <div class="mobile-card-footer">
                            @if($canView)
                                <a href="{{ route('log-pemandu.show', $log) }}" class="mobile-card-action mobile-action-view">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                    Lihat
                                </a>
                            @endif

                            @if($canEdit)
                                <a href="{{ route('log-pemandu.edit', $log) }}" class="mobile-card-action mobile-action-edit">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                    Edit
                                </a>
                            @endif

                            @if($canDelete)
                                <button onclick="deleteLogPemanduItem({{ $log->id }})" class="mobile-card-action mobile-action-delete">
                                    <span class="material-symbols-outlined mobile-card-action-icon">delete</span>
                                    <span class="mobile-card-action-label">Padam</span>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #d1d5db;">folder_open</span>
                    <p>Tiada log ditemui</p>
                    <p style="font-size: 10px; color: #9ca3af;">Sila cuba ubah penapis atau semak status lain.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <x-ui.pagination :paginator="$logs" record-label="log" />
        </div>
    </x-ui.page-header>

    {{-- Auto-refresh tab counts AND table data (disabled; switch to on-demand) --}}
    <script>
        let isRefreshing = false;

        // Auto-refresh tab counts every 5 seconds
        async function refreshTabCounts() {
            try {
                const response = await fetch('{{ route('log-pemandu.tab-counts') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                });
                
                const data = await response.json();
                
                // Update each tab count
                const tabs = ['semua', 'aktif', 'selesai', 'tertunda'];
                tabs.forEach(tab => {
                    const badge = document.querySelector(`a[href*="tab=${tab}"] .bg-gray-100`);
                    if (badge && data[tab] !== undefined) {
                        badge.textContent = new Intl.NumberFormat().format(data[tab]);
                    }
                });
            } catch (error) {
                console.error('Error refreshing tab counts:', error);
            }
        }

        // Auto-refresh table data every 10 seconds (longer interval to avoid too much load)
        async function refreshTableData() {
            if (isRefreshing) return; // Prevent multiple simultaneous refreshes
            
            isRefreshing = true;
            try {
                // Get current URL with query params (tab, search, dates, page)
                const currentUrl = window.location.href;
                
                // Fetch the same page content
                const response = await fetch(currentUrl, {
                    headers: {
                        'Accept': 'text/html',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                });
                
                const html = await response.text();
                
                // Parse the response HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract table body from the new HTML
                const newTableBody = doc.querySelector('table tbody');
                const currentTableBody = document.querySelector('table tbody');
                
                if (newTableBody && currentTableBody) {
                    // Replace table content
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                }
                
                // Also update pagination if exists
                const newPagination = doc.querySelector('.mt-6');
                const currentPagination = document.querySelector('.mt-6');
                
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                }
                
            } catch (error) {
                console.error('Error refreshing table data:', error);
            } finally {
                isRefreshing = false;
            }
        }

        // On-demand: refresh when window/tab gains focus
        window.addEventListener('focus', () => {
            refreshTabCounts();
            refreshTableData();
        });
        // Initial one-time refresh
        refreshTabCounts();
    </script>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>

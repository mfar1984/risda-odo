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
                            {{ optional($log->tarikh_perjalanan)->translatedFormat('d M Y') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Keluar: {{ $log->masa_keluar ? \Carbon\Carbon::parse($log->masa_keluar)->format('H:i') : '-' }}
                            @if($log->masa_masuk)
                                &bull; Masuk: {{ \Carbon\Carbon::parse($log->masa_masuk)->format('H:i') }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $log->pemandu->name ?? 'Tidak Dinyatakan' }}
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
                            Keluar: {{ number_format($log->odometer_keluar ?? 0) }} km
                        </div>
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Masuk: {{ $log->odometer_masuk ? number_format($log->odometer_masuk) . ' km' : '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Jarak: {{ $log->jarak ? number_format($log->jarak) . ' km' : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $log->liter_minyak ? number_format($log->liter_minyak, 2) . ' L' : '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Kos: {{ $log->kos_minyak ? 'RM ' . number_format($log->kos_minyak, 2) : '-' }}
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
                                    <form action="{{ route('log-pemandu.destroy', $log) }}" method="POST" class="inline" onsubmit="return confirm('Padam log pemandu pada {{ optional($log->tarikh_perjalanan)->format('d/m/Y') }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                        </button>
                                    </form>
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

        <div class="mt-6">
            <x-ui.pagination :paginator="$logs" record-label="log" />
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

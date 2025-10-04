<x-dashboard-layout title="Program">
    <x-ui.page-header
        title="Program"
        description="Pengurusan program dan aktiviti sistem"
    >
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            <a href="{{ route('program.create') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Tambah Program
                </x-buttons.primary-button>
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <x-ui.success-alert class="mb-6">
                {{ session('success') }}
            </x-ui.success-alert>
        @endif

        @if(session('error'))
            <x-ui.error-alert class="mb-6">
                {{ session('error') }}
            </x-ui.error-alert>
        @endif

        <!-- Filter Section -->
        <x-ui.search-filter
            :action="route('program.index')"
            search-placeholder="Masukkan nama program, lokasi atau pemohon"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'draf' => 'Draf',
                        'lulus' => 'Lulus',
                        'tolak' => 'Tolak',
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'tertunda' => 'Tertunda'
                    ]
                ],
                [
                    'name' => 'tarikh_dari',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Dari'
                ],
                [
                    'name' => 'tarikh_hingga',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Hingga'
                ]
            ]"
            :reset-url="route('program.index')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
            <x-ui.data-table
                :headers="[
                    ['label' => 'Nama Program', 'align' => 'text-left'],
                    ['label' => 'Status', 'align' => 'text-left'],
                    ['label' => 'Tarikh', 'align' => 'text-left'],
                    ['label' => 'Lokasi', 'align' => 'text-left']
                ]"
                empty-message="Tiada program dijumpai."
            >
                @forelse($programs ?? [] as $program)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                {{ $program->nama_program }}
                            </div>
                            <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                Pemohon: {{ $program->pemohon->nama_penuh ?? 'N/A' }}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.status-badge :status="$program->status" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $program->tarikh_mula->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            hingga {{ $program->tarikh_selesai->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $program->lokasi_program }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-2">
                            <!-- View Icon -->
                            <a href="{{ route('show-program', $program) }}" class="text-blue-600 hover:text-blue-900">
                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </a>

                            <!-- Approve/Reject Icons - Only show for 'draf' status and users with permission -->
                            @if($program->status === 'draf' && auth()->user()->adaKebenaran('program', 'terima'))
                                <form action="{{ route('approve-program', $program) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk meluluskan {{ $program->nama_program }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                                    </button>
                                </form>
                            @endif

                            @if($program->status === 'draf' && auth()->user()->adaKebenaran('program', 'tolak'))
                                <form action="{{ route('reject-program', $program) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk menolak {{ $program->nama_program }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">cancel</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Edit Icon - Show for all programs with permission -->
                            @if(auth()->user()->adaKebenaran('program', 'kemaskini'))
                                <a href="{{ route('edit-program', $program) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                </a>
                            @endif

                            <!-- Delete Icon - Show for all programs with permission -->
                            @if(auth()->user()->adaKebenaran('program', 'padam'))
                                <form action="{{ route('delete-program', $program) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $program->nama_program }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        Tiada program dijumpai.
                    </td>
                </tr>
                @endforelse
            </x-ui.data-table>
        </div>

        <!-- Mobile Card Layout (Hidden on Desktop) -->
        <div class="mobile-table-card">
            @forelse($programs ?? [] as $program)
                <div class="mobile-card">
                    <!-- Card Header -->
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">
                            {{ $program->nama_program }}
                        </div>
                        <div class="mobile-card-badge">
                            <x-ui.status-badge :status="$program->status" />
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="mobile-card-body">
                        <!-- Pemohon -->
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">Pemohon:</div>
                            <div class="mobile-card-value">{{ $program->pemohon->nama_penuh ?? 'N/A' }}</div>
                        </div>

                        <!-- Tarikh -->
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">Tarikh:</div>
                            <div class="mobile-card-value">
                                {{ $program->tarikh_mula->format('d/m/Y H:i') }}
                                <div class="mobile-card-value-secondary">
                                    hingga {{ $program->tarikh_selesai->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">Lokasi:</div>
                            <div class="mobile-card-value">{{ $program->lokasi_program }}</div>
                        </div>
                    </div>

                    <!-- Card Footer (Actions) -->
                    <div class="mobile-card-footer">
                        <!-- View -->
                        <a href="{{ route('show-program', $program) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>

                        <!-- Approve (conditional) -->
                        @if($program->status === 'draf' && auth()->user()->adaKebenaran('program', 'terima'))
                            <form action="{{ route('approve-program', $program) }}" method="POST" onsubmit="return confirm('Lulus {{ $program->nama_program }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="mobile-card-action mobile-action-approve">
                                    <span class="material-symbols-outlined mobile-card-action-icon">check_circle</span>
                                    <span class="mobile-card-action-label">Lulus</span>
                                </button>
                            </form>
                        @endif

                        <!-- Reject (conditional) -->
                        @if($program->status === 'draf' && auth()->user()->adaKebenaran('program', 'tolak'))
                            <form action="{{ route('reject-program', $program) }}" method="POST" onsubmit="return confirm('Tolak {{ $program->nama_program }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="mobile-card-action mobile-action-reject">
                                    <span class="material-symbols-outlined mobile-card-action-icon">cancel</span>
                                    <span class="mobile-card-action-label">Tolak</span>
                                </button>
                            </form>
                        @endif

                        <!-- Edit (conditional) -->
                        @if(auth()->user()->adaKebenaran('program', 'kemaskini'))
                            <a href="{{ route('edit-program', $program) }}" class="mobile-card-action mobile-action-edit">
                                <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                                <span class="mobile-card-action-label">Edit</span>
                            </a>
                        @endif

                        <!-- Delete (conditional) -->
                        @if(auth()->user()->adaKebenaran('program', 'padam'))
                            <form action="{{ route('delete-program', $program) }}" method="POST" onsubmit="return confirm('Padam {{ $program->nama_program }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mobile-card-action mobile-action-delete">
                                    <span class="material-symbols-outlined mobile-card-action-icon">delete</span>
                                    <span class="mobile-card-action-label">Padam</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    Tiada program dijumpai.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-ui.pagination :paginator="$programs" record-label="program" />
    </x-ui.page-header>
</x-dashboard-layout>
<x-dashboard-layout title="Nota Keluaran">


    <!-- Nota Keluaran Container -->
    <x-ui.page-header
        title="Nota Keluaran"
        description="Senarai kemaskini dan ciri baharu sistem"
    >
        <!-- Release Notes Content -->
        <div class="release-notes-container">

            @foreach($releases as $index => $release)
                @if($index === 0)
                    <!-- Latest Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">{{ $release->is_latest ? 'new_releases' : 'history' }}</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>

                    <!-- Version Description -->
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>

                    <!-- Ciri Baharu -->
                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @elseif($index === 1)
                    <!-- Previous Version Separator -->
                    <div style="margin: 40px 0; border-top: 2px solid #e5e7eb; padding-top: 40px;">
                        <h2 style="font-family: Poppins, sans-serif !important; font-size: 18px !important; font-weight: 600 !important; color: #6b7280; margin-bottom: 20px;">Previous Versions</h2>
                    </div>

                    <!-- Previous Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">history</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>

                    <!-- Version Description -->
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>

                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Subsequent Previous Versions -->
                    <div style="margin: 40px 0; border-top: 2px solid #e5e7eb; padding-top: 40px;"></div>

                    <!-- Previous Version Header -->
                    <div class="version-header">
                        <div class="version-badge">
                            <span class="material-symbols-outlined version-icon">history</span>
                            <span class="version-number">v{{ $release->versi }}</span>
                            <span class="version-label">{{ $release->jenis_keluaran_label }}</span>
                        </div>
                        <div class="version-date">{{ $release->tarikh_keluaran->format('d/m/Y') }}</div>
                    </div>

                    <!-- Version Description -->
                    <div class="version-description">
                        <p>{{ $release->penerangan }}</p>
                    </div>

                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">new_releases</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Ciri Baharu</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->ciri_baharu as $feature)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">upgrade</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Penambahbaikan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->penambahbaikan as $improvement)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $improvement }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-orange-600" style="font-size: 20px;">bug_report</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pembetulan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->pembetulan_pepijat as $bugfix)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-green-600 mt-0.5" style="font-size: 16px;">check_circle</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bugfix }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">settings</span>
                                <h3 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Teknikal</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                @foreach($release->perubahan_teknikal as $technical)
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-purple-600 mt-0.5" style="font-size: 16px;">code</span>
                                        <span class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $technical }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

        </div>
    </x-ui.page-header>
</x-dashboard-layout>

<x-dashboard-layout title="Nota Keluaran">
    <x-ui.page-header
        title="Nota Keluaran"
        description="Senarai kemaskini dan ciri baharu sistem"
    >
        
        <div class="space-y-8">
            @foreach($releases as $index => $release)
                @if($index === 0)
                    {{-- LATEST VERSION - Hero Card --}}
                    <div class="latest-version-card">
                        {{-- Gradient Header --}}
                        <div class="latest-version-header">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="latest-icon-wrapper">
                                        <span class="material-symbols-outlined text-white text-[24px]">stars</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-white font-bold" style="font-family: Poppins, sans-serif !important; font-size: 18px !important;">
                                                v{{ $release->versi }}
                                            </span>
                                            <span class="latest-badge">TERKINI</span>
                                        </div>
                                        <p class="text-emerald-50" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                            {{ $release->tarikh_keluaran->format('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="release-type-badge release-type-{{ $release->jenis_keluaran }}">
                                    {{ $release->jenis_keluaran_label }}
                                </span>
                            </div>
                            
                            @if($release->penerangan)
                                <p class="text-white/90 mt-4 leading-relaxed" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                                    {{ $release->penerangan }}
                                </p>
                            @endif
                        </div>

                        {{-- Latest Version Content --}}
                        <div class="latest-version-content">
                            {{-- Features Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                                    <div class="feature-card feature-card-new">
                                        <div class="feature-card-header">
                                            <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                                            <h4 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Ciri Baharu</h4>
                                            <span class="feature-count">{{ count($release->ciri_baharu) }}</span>
                                        </div>
                                        <ul class="feature-list">
                                            @foreach($release->ciri_baharu as $feature)
                                                <li class="feature-item">
                                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                                    <span style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                                    <div class="feature-card feature-card-improve">
                                        <div class="feature-card-header">
                                            <span class="material-symbols-outlined text-[18px]">trending_up</span>
                                            <h4 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Penambahbaikan</h4>
                                            <span class="feature-count">{{ count($release->penambahbaikan) }}</span>
                                        </div>
                                        <ul class="feature-list">
                                            @foreach($release->penambahbaikan as $improvement)
                                                <li class="feature-item">
                                                    <span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span>
                                                    <span style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $improvement }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                                    <div class="feature-card feature-card-fix">
                                        <div class="feature-card-header">
                                            <span class="material-symbols-outlined text-[18px]">build_circle</span>
                                            <h4 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Pembetulan Bug</h4>
                                            <span class="feature-count">{{ count($release->pembetulan_pepijat) }}</span>
                                        </div>
                                        <ul class="feature-list">
                                            @foreach($release->pembetulan_pepijat as $bugfix)
                                                <li class="feature-item">
                                                    <span class="material-symbols-outlined text-[14px] text-orange-600">check_circle</span>
                                                    <span style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $bugfix }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                                    <div class="feature-card feature-card-tech">
                                        <div class="feature-card-header">
                                            <span class="material-symbols-outlined text-[18px]">code</span>
                                            <h4 style="font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 600;">Teknikal</h4>
                                            <span class="feature-count">{{ count($release->perubahan_teknikal) }}</span>
                                        </div>
                                        <ul class="feature-list">
                                            @foreach($release->perubahan_teknikal as $technical)
                                                <li class="feature-item">
                                                    <span class="material-symbols-outlined text-[14px] text-purple-600">check_circle</span>
                                                    <span style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $technical }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @elseif($index === 1)
                    {{-- Previous Versions Header --}}
                    <div class="previous-versions-divider">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">history</span>
                            <h2 style="font-family: Poppins, sans-serif !important; font-size: 14px !important; font-weight: 600 !important; color: #6b7280;">
                                Versi Sebelumnya
                            </h2>
                        </div>
                    </div>

                    {{-- Timeline Item with Expandable Details --}}
                    <div class="timeline-item" x-data="{ expanded: false }">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-header cursor-pointer" @click="expanded = !expanded">
                                <div class="flex items-center gap-2">
                                    <span class="timeline-version">v{{ $release->versi }}</span>
                                    <span class="timeline-type">{{ $release->jenis_keluaran_label }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="timeline-date">{{ $release->tarikh_keluaran->format('d M Y') }}</span>
                                    <span class="material-symbols-outlined text-gray-400 text-[18px] transition-transform duration-200" :class="{ 'rotate-180': expanded }">expand_more</span>
                                </div>
                            </div>
                            
                            @if($release->penerangan)
                                <p class="timeline-description">{{ $release->penerangan }}</p>
                            @endif

                            {{-- Summary Badges (Always visible) --}}
                            @if($release->ciri_baharu || $release->penambahbaikan || $release->pembetulan_pepijat || $release->perubahan_teknikal)
                                <div class="timeline-changes mb-3">
                                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                                        <span class="change-badge change-badge-new">{{ count($release->ciri_baharu) }} Ciri Baharu</span>
                                    @endif
                                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                                        <span class="change-badge change-badge-improve">{{ count($release->penambahbaikan) }} Penambahbaikan</span>
                                    @endif
                                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                                        <span class="change-badge change-badge-fix">{{ count($release->pembetulan_pepijat) }} Pembetulan</span>
                                    @endif
                                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                                        <span class="change-badge change-badge-tech">{{ count($release->perubahan_teknikal) }} Teknikal</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Detailed Changes (Expandable) --}}
                            <div x-show="expanded" 
                                 x-collapse
                                 class="mt-4 pt-4 border-t border-gray-100">
                                
                                <div class="space-y-4">
                                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-emerald-600 text-[16px]">rocket_launch</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Ciri Baharu</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->ciri_baharu as $feature)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-emerald-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-blue-600 text-[16px]">trending_up</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Penambahbaikan</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->penambahbaikan as $improvement)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-blue-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $improvement }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-orange-600 text-[16px]">build_circle</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pembetulan Bug</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->pembetulan_pepijat as $bugfix)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-orange-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $bugfix }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-purple-600 text-[16px]">code</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Teknikal</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->perubahan_teknikal as $technical)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-purple-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $technical }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Timeline Item for subsequent versions (Expandable) --}}
                    <div class="timeline-item" x-data="{ expanded: false }">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-header cursor-pointer" @click="expanded = !expanded">
                                <div class="flex items-center gap-2">
                                    <span class="timeline-version">v{{ $release->versi }}</span>
                                    <span class="timeline-type">{{ $release->jenis_keluaran_label }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="timeline-date">{{ $release->tarikh_keluaran->format('d M Y') }}</span>
                                    <span class="material-symbols-outlined text-gray-400 text-[18px] transition-transform duration-200" :class="{ 'rotate-180': expanded }">expand_more</span>
                                </div>
                            </div>
                            
                            @if($release->penerangan)
                                <p class="timeline-description">{{ $release->penerangan }}</p>
                            @endif

                            {{-- Summary Badges (Always visible) --}}
                            @if($release->ciri_baharu || $release->penambahbaikan || $release->pembetulan_pepijat || $release->perubahan_teknikal)
                                <div class="timeline-changes mb-3">
                                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                                        <span class="change-badge change-badge-new">{{ count($release->ciri_baharu) }} Ciri Baharu</span>
                                    @endif
                                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                                        <span class="change-badge change-badge-improve">{{ count($release->penambahbaikan) }} Penambahbaikan</span>
                                    @endif
                                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                                        <span class="change-badge change-badge-fix">{{ count($release->pembetulan_pepijat) }} Pembetulan</span>
                                    @endif
                                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                                        <span class="change-badge change-badge-tech">{{ count($release->perubahan_teknikal) }} Teknikal</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Detailed Changes (Expandable) --}}
                            <div x-show="expanded" 
                                 x-collapse
                                 class="mt-4 pt-4 border-t border-gray-100">
                                
                                <div class="space-y-4">
                                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-emerald-600 text-[16px]">rocket_launch</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Ciri Baharu</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->ciri_baharu as $feature)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-emerald-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-blue-600 text-[16px]">trending_up</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Penambahbaikan</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->penambahbaikan as $improvement)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-blue-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $improvement }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-orange-600 text-[16px]">build_circle</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pembetulan Bug</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->pembetulan_pepijat as $bugfix)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-orange-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $bugfix }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-purple-600 text-[16px]">code</span>
                                                <h5 class="font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Teknikal</h5>
                                            </div>
                                            <ul class="space-y-1.5 ml-6">
                                                @foreach($release->perubahan_teknikal as $technical)
                                                    <li class="flex items-start gap-2">
                                                        <span class="material-symbols-outlined text-purple-600 text-[12px] mt-0.5">check_circle</span>
                                                        <span class="text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $technical }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        
    </x-ui.page-header>
</x-dashboard-layout>

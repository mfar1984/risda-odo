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

                    <!-- Dynamic Content Sections -->
                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon">new_releases</span>
                                    <h3 class="section-title">Ciri Baharu</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->ciri_baharu as $feature)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                                            <span class="feature-text">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-purple">upgrade</span>
                                    <h3 class="section-title">Penambahbaikan</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->penambahbaikan as $improvement)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-blue">trending_up</span>
                                            <span class="feature-text">{{ $improvement }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-orange">bug_report</span>
                                    <h3 class="section-title">Pembetulan Pepijat</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->pembetulan_pepijat as $bugfix)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-orange">build</span>
                                            <span class="feature-text">{{ $bugfix }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-gray">settings</span>
                                    <h3 class="section-title">Perubahan Teknikal</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->perubahan_teknikal as $technical)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-gray">code</span>
                                            <span class="feature-text">{{ $technical }}</span>
                                        </div>
                                    @endforeach
                                </div>
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

                    <!-- Dynamic Content Sections -->
                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon">new_releases</span>
                                    <h3 class="section-title">Ciri Baharu</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->ciri_baharu as $feature)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                                            <span class="feature-text">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-purple">upgrade</span>
                                    <h3 class="section-title">Penambahbaikan</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->penambahbaikan as $improvement)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-blue">trending_up</span>
                                            <span class="feature-text">{{ $improvement }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-orange">bug_report</span>
                                    <h3 class="section-title">Pembetulan Pepijat</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->pembetulan_pepijat as $bugfix)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-orange">build</span>
                                            <span class="feature-text">{{ $bugfix }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-gray">settings</span>
                                    <h3 class="section-title">Perubahan Teknikal</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->perubahan_teknikal as $technical)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-gray">code</span>
                                            <span class="feature-text">{{ $technical }}</span>
                                        </div>
                                    @endforeach
                                </div>
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

                    <!-- Dynamic Content Sections -->
                    @if($release->ciri_baharu && count($release->ciri_baharu) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon">new_releases</span>
                                    <h3 class="section-title">Ciri Baharu</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->ciri_baharu as $feature)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                                            <span class="feature-text">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->penambahbaikan && count($release->penambahbaikan) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-purple">upgrade</span>
                                    <h3 class="section-title">Penambahbaikan</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->penambahbaikan as $improvement)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-blue">trending_up</span>
                                            <span class="feature-text">{{ $improvement }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->pembetulan_pepijat && count($release->pembetulan_pepijat) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-orange">bug_report</span>
                                    <h3 class="section-title">Pembetulan Pepijat</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->pembetulan_pepijat as $bugfix)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-orange">build</span>
                                            <span class="feature-text">{{ $bugfix }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($release->perubahan_teknikal && count($release->perubahan_teknikal) > 0)
                        <div class="features-grid">
                            <div class="feature-section">
                                <div class="feature-section-header">
                                    <span class="material-symbols-outlined section-icon section-icon-gray">settings</span>
                                    <h3 class="section-title">Perubahan Teknikal</h3>
                                </div>
                                <div class="feature-list">
                                    @foreach($release->perubahan_teknikal as $technical)
                                        <div class="feature-item">
                                            <span class="material-symbols-outlined feature-icon feature-icon-gray">code</span>
                                            <span class="feature-text">{{ $technical }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

        </div>
    </x-ui.page-header>
</x-dashboard-layout>

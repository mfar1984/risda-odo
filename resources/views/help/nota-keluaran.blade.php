<x-dashboard-layout title="Nota Keluaran">


    <!-- Nota Keluaran Container -->
    <x-ui.page-header
        title="Nota Keluaran"
        description="Senarai kemaskini dan ciri baharu sistem"
    >
        <!-- Release Notes Content -->
        <div class="release-notes-container">

            <!-- Version v1.1 Header -->
            <div class="version-header">
                <div class="version-badge">
                    <span class="material-symbols-outlined version-icon">new_releases</span>
                    <span class="version-number">v1.1</span>
                    <span class="version-label">Nota Keluaran Blue</span>
                </div>
                <div class="version-date">24/09/2025</div>
            </div>

            <!-- Version Description -->
            <div class="version-description">
                <p>Kemaskini major dengan penambahan sistem pengurusan RISDA Bahagian dan Stesen, serta dokumentasi lengkap untuk data isolation.</p>
            </div>

            <!-- Features Grid -->
            <div class="features-grid">

                <!-- RISDA Management Features -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon">business</span>
                        <h3 class="section-title">RISDA Management</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">RISDA Bahagian CRUD Management</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">RISDA Stesen CRUD Management</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Parent-Child Relationship (Bahagian → Stesen)</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Tab-based Navigation (Bahagian & Stesen)</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Malaysia Postcodes Integration</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Auto-detect Bandar & Negeri</span>
                        </div>
                    </div>
                </div>

                <!-- Form & Validation -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon section-icon-purple">edit_document</span>
                        <h3 class="section-title">Form & Validation</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Two-column Form Layout</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Comprehensive Field Validation</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Error Handling & Display</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Dropdown dengan Parent Selection</span>
                        </div>
                    </div>
                </div>

                <!-- UI/UX Components -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon section-icon-pink">palette</span>
                        <h3 class="section-title">UI/UX Components</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Button Components (Warning, Info, Success)</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Shine Effect Animation</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Consistent Design Patterns</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Poppins Font (11-14px)</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Material Symbols Outlined</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Reusable Form Components</span>
                        </div>
                    </div>
                </div>

                <!-- Documentation & Architecture -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon section-icon-orange">description</span>
                        <h3 class="section-title">Documentation & Architecture</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">System Architecture Documentation</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">Development Guidelines</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">Data Isolation Implementation Guide</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">API Documentation</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">Multi-Tenant Architecture Planning</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-blue">check_circle</span>
                            <span class="feature-text">Security & Performance Guidelines</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Previous Version Separator -->
            <div style="margin: 40px 0; border-top: 2px solid #e5e7eb; padding-top: 40px;">
                <h2 style="font-family: Poppins, sans-serif !important; font-size: 18px !important; font-weight: 600 !important; color: #6b7280; margin-bottom: 20px;">Previous Versions</h2>
            </div>

            <!-- Version v1.0 Header -->
            <div class="version-header">
                <div class="version-badge">
                    <span class="material-symbols-outlined version-icon">history</span>
                    <span class="version-number">v1.0</span>
                    <span class="version-label">Nota Keluaran Green</span>
                </div>
                <div class="version-date">15/10/2025</div>
            </div>

            <!-- Version Description -->
            <div class="version-description">
                <p>Pelancaran awal sistem dengan fungsi asas dashboard, authentication, dan component system.</p>
            </div>

            <!-- Features Grid for v1.0 -->
            <div class="features-grid">
                <!-- Core System -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon">star</span>
                        <h3 class="section-title">Core System</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Laravel 12.x Framework Setup</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Authentication System</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Dashboard Layout</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Sidebar Navigation</span>
                        </div>
                    </div>
                </div>

                <!-- UI Components -->
                <div class="feature-section">
                    <div class="feature-section-header">
                        <span class="material-symbols-outlined section-icon section-icon-pink">widgets</span>
                        <h3 class="section-title">UI Components</h3>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Basic Button Components</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Form Input Components</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Page Header Component</span>
                        </div>
                        <div class="feature-item">
                            <span class="material-symbols-outlined feature-icon feature-icon-green">check_circle</span>
                            <span class="feature-text">Container Component</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </x-ui.page-header>
</x-dashboard-layout>

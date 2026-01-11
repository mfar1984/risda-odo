@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout 
    title="Lihat Pengguna"
    description="Maklumat terperinci pengguna"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Maklumat Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci pengguna') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">
                    <!-- Row 1: Nama & Email -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="name" value="Nama Pengguna" />
                            <x-forms.text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $pengguna->name }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="email" value="Email" />
                            <x-forms.text-input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="mt-1 block w-full" 
                                value="{{ $pengguna->email }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 2: Peranan Kumpulan & Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="kumpulan" value="Peranan Kumpulan" />
                            <x-forms.text-input
                                id="kumpulan"
                                name="kumpulan"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $pengguna->kumpulan->nama_kumpulan ?? 'Semua Akses' }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status Akaun" />
                            <x-forms.text-input 
                                id="status" 
                                name="status" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ ucfirst(str_replace('_', ' ', $pengguna->status)) }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Akses
                        </h3>
                    </div>

                    <!-- Row 3: Jenis Organisasi & Organisasi -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="jenis_organisasi" value="Jenis Akses" />
                            <x-forms.text-input 
                                id="jenis_organisasi" 
                                name="jenis_organisasi" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $pengguna->jenis_organisasi ? ucfirst($pengguna->jenis_organisasi) : 'Semua' }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="organisasi" value="Organisasi Akses" />
                            @php
                                $organisasiNama = 'Semua Organisasi';
                                if ($pengguna->jenis_organisasi === 'bahagian' && $pengguna->organisasi_id) {
                                    $bahagian = \App\Models\RisdaBahagian::find($pengguna->organisasi_id);
                                    $organisasiNama = $bahagian ? $bahagian->nama_bahagian : 'Tiada';
                                } elseif ($pengguna->jenis_organisasi === 'stesen' && $pengguna->stesen_akses_ids) {
                                    $organisasiNama = $pengguna->stesen_akses_names;
                                }
                            @endphp
                            <x-forms.text-input
                                id="organisasi"
                                name="organisasi"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $organisasiNama }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-pengguna') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>
                        
                        <a href="{{ route('pengurusan.edit-pengguna', $pengguna) }}">
                            <x-buttons.primary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                Edit Pengguna
                            </x-buttons.primary-button>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Security Management Section -->
            @if(Auth::user()->adaKebenaran('senarai_pengguna', 'kemaskini'))
            <section class="mt-6">
                <header>
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Pengurusan Keselamatan') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Urus tetapan keselamatan dan akses pengguna') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            <span class="material-symbols-outlined mr-1" style="font-size: 14px;">admin_panel_settings</span>
                            Tindakan Admin
                        </span>
                    </div>
                </header>

                <div class="mt-6 space-y-6">
                    <!-- Security Status Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- 2FA Status -->
                        <div class="border border-gray-200 rounded-sm p-4">
                            <div class="flex items-center mb-2">
                                <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 18px;">security</span>
                                <span class="text-xs text-gray-600">Status 2FA</span>
                            </div>
                            <div class="text-sm font-semibold">
                                @if($pengguna->two_factor_enabled)
                                    <span class="text-green-600">Diaktifkan</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>

                        <!-- Active Sessions -->
                        <div class="border border-gray-200 rounded-sm p-4">
                            <div class="flex items-center mb-2">
                                <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 18px;">devices</span>
                                <span class="text-xs text-gray-600">Sesi Aktif</span>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $activeSessions }} sesi
                            </div>
                        </div>

                        <!-- Last Login -->
                        <div class="border border-gray-200 rounded-sm p-4">
                            <div class="flex items-center mb-2">
                                <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 18px;">login</span>
                                <span class="text-xs text-gray-600">Log Masuk Terakhir</span>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $pengguna->last_login_at ? formatTarikhMasa($pengguna->last_login_at) : 'Tiada' }}
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="border border-gray-200 rounded-sm p-4">
                            <div class="flex items-center mb-2">
                                <span class="material-symbols-outlined text-gray-600 mr-2" style="font-size: 18px;">account_circle</span>
                                <span class="text-xs text-gray-600">Status Akaun</span>
                            </div>
                            <div class="text-sm font-semibold">
                                @if($pengguna->status === 'aktif')
                                    <span class="text-green-600">Aktif</span>
                                @else
                                    <span class="text-red-600">Dikunci</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Security Actions -->
                    <div class="border-t border-gray-200 pt-4">
                        <h5 class="text-sm font-semibold text-gray-900 mb-3">Tindakan Keselamatan</h5>
                        <div class="flex flex-wrap gap-2">
                            <!-- Reset 2FA -->
                            @if($pengguna->two_factor_enabled)
                                <button onclick="confirmReset2FA({{ $pengguna->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">security</span>
                                    Reset 2FA
                                </button>
                            @endif

                            <!-- Force Logout -->
                            @if($activeSessions > 0)
                                <button onclick="confirmForceLogout({{ $pengguna->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-orange-300 rounded-sm text-xs font-medium text-orange-700 bg-white hover:bg-orange-50">
                                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">logout</span>
                                    Log Keluar Semua Sesi
                                </button>
                            @endif

                            <!-- Reset Password -->
                            <button onclick="openResetPasswordModal({{ $pengguna->id }})" 
                                    class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-sm text-xs font-medium text-blue-700 bg-white hover:bg-blue-50">
                                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">key</span>
                                Reset Kata Laluan
                            </button>

                            <!-- Lock/Unlock Account -->
                            @if($pengguna->jenis_organisasi !== 'semua')
                                @if($pengguna->status === 'aktif')
                                    <button onclick="openLockAccountModal({{ $pengguna->id }})" 
                                            class="inline-flex items-center px-3 py-2 border border-red-300 rounded-sm text-xs font-medium text-red-700 bg-white hover:bg-red-50">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 16px;">lock</span>
                                        Kunci Akaun
                                    </button>
                                @else
                                    <button onclick="confirmUnlockAccount({{ $pengguna->id }})" 
                                            class="inline-flex items-center px-3 py-2 border border-green-300 rounded-sm text-xs font-medium text-green-700 bg-white hover:bg-green-50">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 16px;">lock_open</span>
                                        Buka Kunci Akaun
                                    </button>
                                @endif
                            @endif

                            <!-- View Security Logs -->
                            <a href="{{ route('pengurusan.security-logs-pengguna', $pengguna) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">history</span>
                                Lihat Log Keselamatan
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            @endif
        </x-ui.container>

        <!-- Modals -->
        @if(Auth::user()->adaKebenaran('senarai_pengguna', 'kemaskini'))
            @include('pengurusan.partials.security-modals', ['pengguna' => $pengguna])
        @endif

        @push('scripts')
        <script>
        console.log('Security actions script loaded');

        // Reset 2FA Confirmation
        function confirmReset2FA(userId) {
            console.log('confirmReset2FA called for user:', userId);
            if (confirm('Adakah anda pasti ingin mereset 2FA untuk pengguna ini? Pengguna perlu menyediakan 2FA semula.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/pengurusan/senarai-pengguna/${userId}/reset-2fa`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Force Logout Confirmation
        function confirmForceLogout(userId) {
            console.log('confirmForceLogout called for user:', userId);
            if (confirm('Adakah anda pasti ingin log keluar semua sesi untuk pengguna ini? Pengguna perlu log masuk semula.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/pengurusan/senarai-pengguna/${userId}/force-logout`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Unlock Account Confirmation
        function confirmUnlockAccount(userId) {
            console.log('confirmUnlockAccount called for user:', userId);
            if (confirm('Adakah anda pasti ingin membuka kunci akaun ini? Pengguna akan dapat log masuk semula.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/pengurusan/senarai-pengguna/${userId}/unlock-account`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Open Reset Password Modal
        function openResetPasswordModal(userId) {
            console.log('openResetPasswordModal called for user:', userId);
            const modal = document.getElementById('resetPasswordModal');
            const form = document.getElementById('resetPasswordForm');
            
            if (!modal) {
                console.error('resetPasswordModal not found!');
                alert('Error: Modal tidak dijumpai. Sila refresh page.');
                return;
            }
            
            if (!form) {
                console.error('resetPasswordForm not found!');
                alert('Error: Form tidak dijumpai. Sila refresh page.');
                return;
            }
            
            modal.classList.remove('hidden');
            form.action = `/pengurusan/senarai-pengguna/${userId}/reset-password`;
        }

        // Close Reset Password Modal
        function closeResetPasswordModal() {
            console.log('closeResetPasswordModal called');
            const modal = document.getElementById('resetPasswordModal');
            const form = document.getElementById('resetPasswordForm');
            
            if (modal) modal.classList.add('hidden');
            if (form) form.reset();
        }

        // Open Lock Account Modal
        function openLockAccountModal(userId) {
            console.log('openLockAccountModal called for user:', userId);
            const modal = document.getElementById('lockAccountModal');
            const form = document.getElementById('lockAccountForm');
            
            if (!modal) {
                console.error('lockAccountModal not found!');
                alert('Error: Modal tidak dijumpai. Sila refresh page.');
                return;
            }
            
            if (!form) {
                console.error('lockAccountForm not found!');
                alert('Error: Form tidak dijumpai. Sila refresh page.');
                return;
            }
            
            modal.classList.remove('hidden');
            form.action = `/pengurusan/senarai-pengguna/${userId}/lock-account`;
        }

        // Close Lock Account Modal
        function closeLockAccountModal() {
            console.log('closeLockAccountModal called');
            const modal = document.getElementById('lockAccountModal');
            const form = document.getElementById('lockAccountForm');
            
            if (modal) modal.classList.add('hidden');
            if (form) form.reset();
        }

        // Check if modals exist on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Checking for modals...');
            const resetPasswordModal = document.getElementById('resetPasswordModal');
            const lockAccountModal = document.getElementById('lockAccountModal');
            
            console.log('resetPasswordModal exists:', !!resetPasswordModal);
            console.log('lockAccountModal exists:', !!lockAccountModal);
        });
        </script>
        @endpush
</x-dashboard-layout>

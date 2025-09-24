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
        </x-ui.container>
</x-dashboard-layout>

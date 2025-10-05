@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout 
    title="Lihat RISDA Staf"
    description="Maklumat terperinci RISDA Staf"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Maklumat RISDA Staf') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci RISDA Staf') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">
                    <!-- Row 1: No. Pekerja & Nama Penuh -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_pekerja" value="No. Pekerja" />
                            <x-forms.text-input 
                                id="no_pekerja" 
                                name="no_pekerja" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->no_pekerja }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_penuh" value="Nama Penuh" />
                            <x-forms.text-input 
                                id="nama_penuh" 
                                name="nama_penuh" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->nama_penuh }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 2: No. Kad Pengenalan & Jantina -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_kad_pengenalan" value="No. Kad Pengenalan" />
                            <x-forms.text-input 
                                id="no_kad_pengenalan" 
                                name="no_kad_pengenalan" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->no_kad_pengenalan }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="jantina" value="Jantina" />
                            <x-forms.text-input 
                                id="jantina" 
                                name="jantina" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ ucfirst($risdaStaf->jantina) }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 3: Bahagian & Stesen -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="bahagian" value="RISDA Bahagian" />
                            <x-forms.text-input 
                                id="bahagian" 
                                name="bahagian" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->bahagian->nama_bahagian ?? 'N/A' }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="stesen" value="RISDA Stesen" />
                            <x-forms.text-input 
                                id="stesen" 
                                name="stesen" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->stesen->nama_stesen ?? 'Semua Stesen dalam Bahagian' }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 4: Jawatan & No. Telefon -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="jawatan" value="Jawatan" />
                            <x-forms.text-input 
                                id="jawatan" 
                                name="jawatan" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->jawatan }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_telefon" value="No. Telefon" />
                            <x-forms.text-input 
                                id="no_telefon" 
                                name="no_telefon" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->no_telefon }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 5: Email & No. Fax -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="email" value="Email" />
                            <x-forms.text-input 
                                id="email" 
                                name="email" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->email }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_fax" value="No. Fax" />
                            <x-forms.text-input 
                                id="no_fax" 
                                name="no_fax" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->no_fax ?? 'Tiada' }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 6: Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status" />
                            <div class="mt-1">
                                @if($risdaStaf->status === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @elseif($risdaStaf->status === 'tidak_aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Gantung
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
                            Maklumat Alamat
                        </h3>
                    </div>

                    <!-- Row 7: Alamat 1 & Alamat 2 -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="alamat_1" value="Alamat 1" />
                            <x-forms.text-input 
                                id="alamat_1" 
                                name="alamat_1" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->alamat_1 }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="alamat_2" value="Alamat 2" />
                            <x-forms.text-input 
                                id="alamat_2" 
                                name="alamat_2" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->alamat_2 ?? 'Tiada' }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 8: Poskod & Bandar -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="poskod" value="Poskod" />
                            <x-forms.text-input 
                                id="poskod" 
                                name="poskod" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->poskod }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="bandar" value="Bandar" />
                            <x-forms.text-input 
                                id="bandar" 
                                name="bandar" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->bandar }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 9: Negeri & Negara -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="negeri" value="Negeri" />
                            <x-forms.text-input 
                                id="negeri" 
                                name="negeri" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->negeri }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="negara" value="Negara" />
                            <x-forms.text-input 
                                id="negara" 
                                name="negara" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->negara }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Sistem
                        </h3>
                    </div>

                    <!-- Row 10: Created & Updated -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="created_at" value="Dicipta Pada" />
                            <x-forms.text-input 
                                id="created_at" 
                                name="created_at" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStaf->created_at->format('d/m/Y H:i:s') }}"
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
                                value="{{ $risdaStaf->updated_at->format('d/m/Y H:i:s') }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-risda') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('pengurusan.edit-staf', $risdaStaf) }}">
                                <x-buttons.warning-button type="button">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                    Edit
                                </x-buttons.warning-button>
                            </a>
                            
                            <x-buttons.danger-button type="button" onclick="deleteStafItem({{ $risdaStaf->id }})">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">delete</span>
                                Padam
                            </x-buttons.danger-button>
                        </div>
                    </div>
                </div>
            </section>
        </x-ui.container>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>

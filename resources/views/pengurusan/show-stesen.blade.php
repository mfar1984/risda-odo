@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout 
    title="Lihat RISDA Stesen"
    description="Maklumat terperinci RISDA Stesen"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('RISDA Stesen') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci RISDA Stesen') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">
                    <!-- Row 1: RISDA Bahagian & Nama Stesen -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="risda_bahagian" value="RISDA Bahagian" />
                            <x-forms.text-input 
                                id="risda_bahagian" 
                                name="risda_bahagian" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->risdaBahagian->nama_bahagian ?? 'N/A' }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_stesen" value="Nama Stesen" />
                            <x-forms.text-input 
                                id="nama_stesen" 
                                name="nama_stesen" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->nama_stesen }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 2: No. Telefon & No. Fax -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_telefon" value="No. Telefon" />
                            <x-forms.text-input 
                                id="no_telefon" 
                                name="no_telefon" 
                                type="tel" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->no_telefon }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_fax" value="No. Fax" />
                            <x-forms.text-input 
                                id="no_fax" 
                                name="no_fax" 
                                type="tel" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->no_fax }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 3: Email & Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="email" value="Email" />
                            <x-forms.text-input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->email }}"
                                readonly
                            />
                        </div>
                        
                        <div style="flex: 1;">
                            <x-forms.input-label for="status_dropdown" value="Status" />
                            <div class="mt-1">
                                @if($risdaStesen->status_dropdown === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                        <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Maklumat Alamat
                        </h3>
                    </div>

                    <!-- Row 4: Alamat 1 & Alamat 2 -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="alamat_1" value="Alamat 1" />
                            <x-forms.text-input 
                                id="alamat_1" 
                                name="alamat_1" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->alamat_1 }}"
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
                                value="{{ $risdaStesen->alamat_2 }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 5: Poskod & Bandar -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="poskod" value="Poskod" />
                            <x-forms.text-input 
                                id="poskod" 
                                name="poskod" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->poskod }}"
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
                                value="{{ $risdaStesen->bandar }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 6: Negeri & Malaysia -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="negeri" value="Negeri" />
                            <x-forms.text-input 
                                id="negeri" 
                                name="negeri" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ $risdaStesen->negeri }}"
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
                                value="{{ $risdaStesen->negara }}"
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

                    <!-- Row 7: Created & Updated -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="created_at" value="Dicipta Pada" />
                            <x-forms.text-input
                                id="created_at"
                                name="created_at"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $risdaStesen->created_at->format('d/m/Y H:i:s') }}"
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
                                value="{{ $risdaStesen->updated_at->format('d/m/Y H:i:s') }}"
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
                            <a href="{{ route('pengurusan.edit-stesen', $risdaStesen) }}">
                                <x-buttons.warning-button type="button">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                    Edit
                                </x-buttons.warning-button>
                            </a>
                            
                            <x-buttons.danger-button type="button" onclick="deleteStesenItem({{ $risdaStesen->id }})">
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

<x-dashboard-layout
    title="Lihat Kenderaan"
    description="Maklumat terperinci kenderaan"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Maklumat Kenderaan') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci kenderaan') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">

                    <!-- Row 1: No. Plat & Jenama -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_plat" value="No. Plat" />
                            <x-forms.text-input
                                id="no_plat"
                                name="no_plat"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->no_plat }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="jenama" value="Jenama" />
                            <x-forms.text-input
                                id="jenama"
                                name="jenama"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->jenama }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 2: Model & Tahun -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="model" value="Model" />
                            <x-forms.text-input
                                id="model"
                                name="model"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->model }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="tahun" value="Tahun" />
                            <x-forms.text-input
                                id="tahun"
                                name="tahun"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->tahun }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 3: Jenis Bahan Api & Warna -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="jenis_bahan_api" value="Jenis Bahan Api" />
                            <x-forms.text-input
                                id="jenis_bahan_api"
                                name="jenis_bahan_api"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->jenis_bahan_api_label }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="warna" value="Warna" />
                            <x-forms.text-input
                                id="warna"
                                name="warna"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->warna }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 4: No. Enjin & No. Casis -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="no_enjin" value="No. Enjin" />
                            <x-forms.text-input
                                id="no_enjin"
                                name="no_enjin"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->no_enjin }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="no_casis" value="No. Casis" />
                            <x-forms.text-input
                                id="no_casis"
                                name="no_casis"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->no_casis }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 5: Kapasiti Muatan & Cukai Tamat Tempoh -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="kapasiti_muatan" value="Kapasiti Muatan" />
                            <x-forms.text-input
                                id="kapasiti_muatan"
                                name="kapasiti_muatan"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->kapasiti_muatan ?: '-' }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="cukai_tamat_tempoh" value="Cukai Tamat Tempoh" />
                            <x-forms.text-input
                                id="cukai_tamat_tempoh"
                                name="cukai_tamat_tempoh"
                                type="text"
                                class="mt-1 block w-full {{ $kenderaan->is_cukai_expired ? 'text-red-600 font-medium' : '' }}"
                                value="{{ $kenderaan->cukai_tamat_tempoh->format('d/m/Y') }}"
                                readonly
                            />
                            @if($kenderaan->is_cukai_expired)
                                <p class="mt-1 text-sm text-red-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tamat Tempoh
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Row 6: Tarikh Pendaftaran & Status -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="tarikh_pendaftaran" value="Tarikh Pendaftaran" />
                            <x-forms.text-input
                                id="tarikh_pendaftaran"
                                name="tarikh_pendaftaran"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $kenderaan->tarikh_pendaftaran->format('d/m/Y') }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status" />
                            <div class="mt-1">
                                @if($kenderaan->status === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        {{ $kenderaan->status_label }}
                                    </span>
                                @elseif($kenderaan->status === 'tidak_aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        {{ $kenderaan->status_label }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        {{ $kenderaan->status_label }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    @if($kenderaan->dokumen_kenderaan && count($kenderaan->dokumen_kenderaan) > 0)
                    <div style="margin-top: 24px;">
                        <x-forms.input-label value="Dokumen Kenderaan" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($kenderaan->dokumen_kenderaan as $index => $dokumen)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $dokumen['original_name'] ?? 'Dokumen ' . ($index + 1) }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-gray-500">
                                                <span>{{ isset($dokumen['size']) ? number_format($dokumen['size'] / 1024, 1) . ' KB' : '' }}</span>
                                                @if(isset($dokumen['uploaded_at']))
                                                    <span class="mx-2">•</span>
                                                    <span>{{ \Carbon\Carbon::parse($dokumen['uploaded_at'])->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <a href="{{ Storage::url($dokumen['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                            </a>
                                            <a href="{{ Storage::url($dokumen['path']) }}" download class="text-green-600 hover:text-green-900">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Cukai Status Alert -->
                    @if($kenderaan->is_cukai_expired)
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <span class="material-symbols-outlined text-red-400 mr-2" style="font-size: 20px;">warning</span>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Cukai Tamat Tempoh</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Cukai kenderaan ini telah tamat tempoh pada {{ $kenderaan->cukai_tamat_tempoh->format('d/m/Y') }}.
                                </p>
                            </div>
                        </div>
                    </div>
                    @elseif($kenderaan->cukai_tamat_tempoh->diffInDays(now()) <= 30)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <span class="material-symbols-outlined text-yellow-400 mr-2" style="font-size: 20px;">schedule</span>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Cukai Hampir Tamat</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Cukai akan tamat tempoh dalam {{ $kenderaan->cukai_tamat_tempoh->diffInDays(now()) }} hari.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @php
                        $currentUser = auth()->user();
                    @endphp

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-kenderaan') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>

                        <div class="flex space-x-3">
                            @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini'))
                            <a href="{{ route('pengurusan.edit-kenderaan', $kenderaan) }}">
                                <x-buttons.warning-button type="button">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                    Edit
                                </x-buttons.warning-button>
                            </a>
                            @endif

                            @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'padam'))
                            <form action="{{ route('pengurusan.delete-kenderaan', $kenderaan) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam kenderaan {{ $kenderaan->no_plat }}?')">
                                @csrf
                                @method('DELETE')
                                <x-buttons.danger-button type="submit">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">delete</span>
                                    Padam
                                </x-buttons.danger-button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </x-ui.container>
</x-dashboard-layout>

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout
    title="Lihat Penyelenggaraan"
    description="Maklumat terperinci rekod penyelenggaraan"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Rekod Penyelenggaraan') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci rekod penyelenggaraan kenderaan') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">

                    <!-- Maklumat Kenderaan Section -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Kenderaan</h3>
                        
                        <!-- Row 1: No. Plat & Model -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="no_plat" value="No. Plat" />
                                <x-forms.text-input
                                    id="no_plat"
                                    name="no_plat"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->kenderaan->no_plat }}"
                                    readonly
                                />
                            </div>

                            <div style="flex: 1;">
                                <x-forms.input-label for="kenderaan" value="Jenama & Model" />
                                <x-forms.text-input
                                    id="kenderaan"
                                    name="kenderaan"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->kenderaan->nama_penuh }}"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Maklumat Penyelenggaraan Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Penyelenggaraan</h3>
                        
                        <!-- Row 1: Kategori Kos & Status -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="kategori_kos" value="Kategori Kos" />
                                <x-forms.text-input
                                    id="kategori_kos"
                                    name="kategori_kos"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->kategoriKos->nama_kategori }}"
                                    readonly
                                />
                            </div>

                            <div style="flex: 1;">
                                <x-forms.input-label for="status" value="Status" />
                                <x-forms.text-input
                                    id="status"
                                    name="status"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->status_label }}"
                                    readonly
                                />
                            </div>
                        </div>

                        <!-- Row 2: Tarikh Mula & Tarikh Selesai -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_mula" value="Tarikh Mula" />
                                <x-forms.text-input
                                    id="tarikh_mula"
                                    name="tarikh_mula"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ formatTarikh($selenggara->tarikh_mula) }}"
                                    readonly
                                />
                            </div>

                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_selesai" value="Tarikh Selesai" />
                                <x-forms.text-input
                                    id="tarikh_selesai"
                                    name="tarikh_selesai"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ formatTarikh($selenggara->tarikh_selesai) }}"
                                    readonly
                                />
                            </div>
                        </div>

                        <!-- Row 3: Jumlah Kos & Jumlah Hari -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="jumlah_kos" value="Jumlah Kos (RM)" />
                                <x-forms.text-input
                                    id="jumlah_kos"
                                    name="jumlah_kos"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ formatNombor($selenggara->jumlah_kos, 2) }}"
                                    readonly
                                />
                            </div>

                            <div style="flex: 1;">
                                <x-forms.input-label for="jumlah_hari" value="Tempoh (Hari)" />
                                <x-forms.text-input
                                    id="jumlah_hari"
                                    name="jumlah_hari"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->jumlah_hari }} hari"
                                    readonly
                                />
                            </div>
                        </div>

                        <!-- Row 4: Keterangan -->
                        <div>
                            <x-forms.input-label for="keterangan" value="Keterangan" />
                            <textarea
                                id="keterangan"
                                name="keterangan"
                                rows="3"
                                class="form-textarea mt-1"
                                style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                readonly
                            >{{ $selenggara->keterangan ?? '-' }}</textarea>
                        </div>
                    </div>

                    <!-- Maklumat Minyak Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Minyak Enjin</h3>
                        
                        <!-- Row 1: Tukar Minyak -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="tukar_minyak" value="Tukar Minyak" />
                                <x-forms.text-input
                                    id="tukar_minyak"
                                    name="tukar_minyak"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->tukar_minyak ? 'Ya' : 'Tidak' }}"
                                    readonly
                                />
                            </div>

                            @if($selenggara->tukar_minyak)
                            <div style="flex: 1;">
                                <x-forms.input-label for="jangka_hayat_km" value="Jangka Hayat Minyak (KM)" />
                                <x-forms.text-input
                                    id="jangka_hayat_km"
                                    name="jangka_hayat_km"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ formatNombor($selenggara->jangka_hayat_km) }} km"
                                    readonly
                                />
                            </div>
                            @else
                            <div style="flex: 1;"></div>
                            @endif
                        </div>
                    </div>

                    <!-- Maklumat Dokumen Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Dokumen</h3>
                        
                        <div>
                            <x-forms.input-label for="fail_invois" value="Invois / Resit" />
                            @if($selenggara->fail_invois)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($selenggara->fail_invois) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">download</span>
                                        Muat Turun Invois
                                    </a>
                                </div>
                            @else
                                <p class="mt-2 text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada fail invois</p>
                            @endif
                        </div>
                    </div>

                    <!-- Maklumat Tambahan Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Tambahan</h3>
                        
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="dilaksana_oleh" value="Dilaksana Oleh" />
                                <x-forms.text-input
                                    id="dilaksana_oleh"
                                    name="dilaksana_oleh"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ $selenggara->pelaksana->name ?? '-' }}"
                                    readonly
                                />
                            </div>

                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_dicipta" value="Tarikh Dicipta" />
                                <x-forms.text-input
                                    id="tarikh_dicipta"
                                    name="tarikh_dicipta"
                                    type="text"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ formatTarikhMasa($selenggara->created_at) }}"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('pengurusan.senarai-selenggara') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                            Kembali
                        </a>
                    </div>

                </div>
            </section>
        </x-ui.container>
</x-dashboard-layout>

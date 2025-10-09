@php
    $tetapanUmum = $tetapanUmum ?? \App\Models\TetapanUmum::getForCurrentUser();
@endphp

<x-dashboard-layout 
    title="Edit Program"
    description="Kemaskini maklumat program"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Edit Program') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Kemaskini maklumat program') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('update-program', $program) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Maklumat Program Section -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Program</h3>
                        
                        <!-- Row 1: Nama Program & Status -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="nama_program" value="Nama Program" />
                                <x-forms.text-input 
                                    id="nama_program" 
                                    name="nama_program" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    value="{{ old('nama_program', $program->nama_program) }}"
                                    required 
                                    autofocus
                                    placeholder="Contoh: Program Lawatan Ladang"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('nama_program')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="status" value="Status" />
                                <select
                                    id="status"
                                    name="status"
                                    class="form-select mt-1"
                                    required
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                >
                                    <option value="">Pilih Status</option>
                                    <option value="draf" {{ old('status', $program->status) == 'draf' ? 'selected' : '' }}>Draf</option>
                                    <option value="lulus" {{ old('status', $program->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                    <option value="tolak" {{ old('status', $program->status) == 'tolak' ? 'selected' : '' }}>Tolak</option>
                                    <option value="aktif" {{ old('status', $program->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tertunda" {{ old('status', $program->status) == 'tertunda' ? 'selected' : '' }}>Tertunda</option>
                                    <option value="selesai" {{ old('status', $program->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>
                        </div>

                        <!-- Row 2: Tarikh Mula & Tarikh Selesai -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_mula" value="Tarikh & Masa Mula" />
                                <x-forms.datetime-input
                                    id="tarikh_mula"
                                    name="tarikh_mula"
                                    class="mt-1 block w-full"
                                    value="{{ old('tarikh_mula', $program->tarikh_mula->format('Y-m-d H:i:s')) }}"
                                    required
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('tarikh_mula')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_selesai" value="Tarikh & Masa Selesai" />
                                <x-forms.datetime-input
                                    id="tarikh_selesai"
                                    name="tarikh_selesai"
                                    class="mt-1 block w-full"
                                    value="{{ old('tarikh_selesai', $program->tarikh_selesai->format('Y-m-d H:i:s')) }}"
                                    required
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('tarikh_selesai')" />
                            </div>
                        </div>

                        <!-- Row 3: Lokasi Program -->
                        <div>
                            <x-forms.input-label for="lokasi_program" value="Lokasi Program" />
                            <x-forms.text-input 
                                id="lokasi_program" 
                                name="lokasi_program" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('lokasi_program', $program->lokasi_program) }}"
                                required 
                                placeholder="Contoh: Ladang Getah Sungai Buloh"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_program')" />
                        </div>

                        <x-map.location-picker
                            :latitude="old('lokasi_lat', $program->lokasi_lat)"
                            :longitude="old('lokasi_long', $program->lokasi_long)"
                            input-lat="lokasi_lat"
                            input-long="lokasi_long"
                            :provider="$tetapanUmum->map_provider ?? 'openstreetmap'"
                            :api-key="$tetapanUmum->map_api_key ?? null"
                            :style-url="$tetapanUmum->map_style_url ?? null"
                        />

                        <div>
                            <x-forms.input-label for="jarak_anggaran" value="Anggaran KM" />
                            <x-forms.text-input
                                id="jarak_anggaran"
                                name="jarak_anggaran"
                                type="number"
                                step="0.1"
                                min="0"
                                class="mt-1 block w-full"
                                value="{{ old('jarak_anggaran', $program->jarak_anggaran) }}"
                                placeholder="Contoh: 120.5"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('jarak_anggaran')" />
                        </div>

                        <!-- Row 4: Penerangan -->
                        <div>
                            <x-forms.input-label for="penerangan" value="Penerangan" />
                            <textarea
                                id="penerangan"
                                name="penerangan"
                                rows="3"
                                class="form-textarea mt-1"
                                placeholder="Penerangan ringkas tentang program ini..."
                            >{{ old('penerangan', $program->penerangan) }}</textarea>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('penerangan')" />
                        </div>
                    </div>

                    <!-- Permohonan & Tugasan Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Permohonan & Tugasan</h3>
                        
                        <!-- Row 1: Permohonan dari & Pilih Pemandu -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="permohonan_dari" value="Permohonan dari" />
                                <select
                                    id="permohonan_dari"
                                    name="permohonan_dari"
                                    class="form-select mt-1"
                                    required
                                >
                                    <option value="">Pilih Staf Pemohon</option>
                                    @foreach($stafs as $staf)
                                        <option value="{{ $staf->id }}" {{ old('permohonan_dari', $program->permohonan_dari) == $staf->id ? 'selected' : '' }}>
                                            {{ $staf->nama_penuh }}{{ $staf->jawatan ? ' - ' . $staf->jawatan : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('permohonan_dari')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="pemandu_id" value="Pilih Pemandu" />
                                <select
                                    id="pemandu_id"
                                    name="pemandu_id"
                                    class="form-select mt-1"
                                    required
                                >
                                    <option value="">Pilih Staf Pemandu</option>
                                    @foreach($stafs as $staf)
                                        <option value="{{ $staf->id }}" {{ old('pemandu_id', $program->pemandu_id) == $staf->id ? 'selected' : '' }}>
                                            {{ $staf->nama_penuh }}{{ $staf->jawatan ? ' - ' . $staf->jawatan : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('pemandu_id')" />
                            </div>
                        </div>

                        <!-- Row 2: Pilih Kenderaan -->
                        <div>
                            <x-forms.input-label for="kenderaan_id" value="Pilih Kenderaan" />
                            <select
                                id="kenderaan_id"
                                name="kenderaan_id"
                                class="form-select mt-1"
                                required
                            >
                                <option value="">Pilih Kenderaan</option>
                                @foreach($kenderaans as $kenderaan)
                                    <option value="{{ $kenderaan->id }}" {{ old('kenderaan_id', $program->kenderaan_id) == $kenderaan->id ? 'selected' : '' }}>
                                        {{ $kenderaan->no_plat }} - {{ $kenderaan->jenama }} {{ $kenderaan->model }} ({{ $kenderaan->tahun }})
                                    </option>
                                @endforeach
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('kenderaan_id')" />
                        </div>

                        <!-- Row 3: Arahan Khas Pengguna Kenderaan (Optional) -->
                        <div>
                            <x-forms.input-label for="arahan_khas_pengguna_kenderaan" value="Arahan Khas Pengguna Kenderaan (Opsyenal)" />
                            <textarea
                                id="arahan_khas_pengguna_kenderaan"
                                name="arahan_khas_pengguna_kenderaan"
                                rows="2"
                                class="form-textarea mt-1"
                                placeholder="Contoh: Isi minyak di stesen Shell Sg. Merah sebelum bertolak."
                            >{{ old('arahan_khas_pengguna_kenderaan', $program->arahan_khas_pengguna_kenderaan) }}</textarea>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('arahan_khas_pengguna_kenderaan')" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-8">
                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Kemaskini Program
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>
</x-dashboard-layout>

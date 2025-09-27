<x-dashboard-layout
    title="Tetapan Umum"
    description="Konfigurasi dan tetapan umum sistem"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Tetapan Umum') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Konfigurasi dan tetapan umum sistem') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.update-tetapan-umum') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Tetapan Umum Section -->
                    <div style="margin-bottom: 32px;">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Tetapan Umum</h3>

                        <!-- Row 1: Nama Sistem & Versi Sistem -->
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="nama_sistem" value="Nama Sistem" />
                                <x-forms.text-input
                                    id="nama_sistem"
                                    name="nama_sistem"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('nama_sistem', $tetapan->nama_sistem) }}"
                                    required
                                    placeholder="Contoh: RISDA Odometer System"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('nama_sistem')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="versi_sistem" value="Versi Sistem" />
                                <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700 font-medium">
                                    {{ $tetapan->versi_sistem }}
                                    @php
                                        $latestRelease = \App\Models\NotaKeluaran::getLatestVersion();
                                    @endphp
                                    @if($latestRelease)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $latestRelease->jenis_keluaran === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $latestRelease->jenis_keluaran_label }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Versi sistem dikemaskini secara automatik dari Nota Keluaran terkini
                                    @if($latestRelease)
                                        ({{ $latestRelease->tarikh_keluaran->format('d/m/Y') }})
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Row 2: Alamat 1 & Alamat 2 -->
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="alamat_1" value="Alamat 1" />
                                <x-forms.text-input
                                    id="alamat_1"
                                    name="alamat_1"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('alamat_1', $tetapan->alamat_1) }}"
                                    placeholder="Contoh: No. 123, Jalan Utama"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_1')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="alamat_2" value="Alamat 2 (Opsional)" />
                                <x-forms.text-input
                                    id="alamat_2"
                                    name="alamat_2"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('alamat_2', $tetapan->alamat_2) }}"
                                    placeholder="Contoh: Taman Sejahtera"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('alamat_2')" />
                            </div>
                        </div>

                        <!-- Row 3: Poskod & Bandar -->
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="poskod" value="Poskod" />
                                <x-forms.text-input
                                    id="poskod"
                                    name="poskod"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('poskod', $tetapan->poskod) }}"
                                    placeholder="Contoh: 50000"
                                    maxlength="10"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('poskod')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="bandar" value="Bandar" />
                                <x-forms.text-input
                                    id="bandar"
                                    name="bandar"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('bandar', $tetapan->bandar) }}"
                                    placeholder="Contoh: Kuala Lumpur"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('bandar')" />
                            </div>
                        </div>

                        <!-- Row 4: Negeri & Negara -->
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="negeri" value="Negeri" />
                                <x-forms.text-input
                                    id="negeri"
                                    name="negeri"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('negeri', $tetapan->negeri) }}"
                                    placeholder="Contoh: Selangor"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('negeri')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="negara" value="Negara" />
                                <x-forms.text-input
                                    id="negara"
                                    name="negara"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('negara', $tetapan->negara) }}"
                                    required
                                    readonly
                                />
                                <p class="mt-1 text-xs text-gray-500">Negara ditetapkan secara automatik kepada Malaysia</p>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('negara')" />
                            </div>
                        </div>
                    </div>

                    <!-- Tetapan Sistem Section -->
                    <div style="margin-bottom: 32px;">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Tetapan Sistem</h3>

                        <!-- Row 1: Maksimum Percubaan Login & Masa Tamat Sesi -->
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="maksimum_percubaan_login" value="Maksimum Percubaan Login" />
                                <x-forms.text-input
                                    id="maksimum_percubaan_login"
                                    name="maksimum_percubaan_login"
                                    type="number"
                                    class="mt-1 block w-full"
                                    value="{{ old('maksimum_percubaan_login', $tetapan->maksimum_percubaan_login) }}"
                                    required
                                    min="1"
                                    max="10"
                                    placeholder="Contoh: 3"
                                />
                                <p class="mt-1 text-xs text-gray-500">Bilangan percubaan login yang dibenarkan sebelum akaun dikunci</p>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('maksimum_percubaan_login')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="masa_tamat_sesi_minit" value="Masa Tamat Sesi (Minit)" />
                                <x-forms.text-input
                                    id="masa_tamat_sesi_minit"
                                    name="masa_tamat_sesi_minit"
                                    type="number"
                                    class="mt-1 block w-full"
                                    value="{{ old('masa_tamat_sesi_minit', $tetapan->masa_tamat_sesi_minit) }}"
                                    required
                                    min="5"
                                    max="1440"
                                    placeholder="Contoh: 60"
                                />
                                <p class="mt-1 text-xs text-gray-500">Masa dalam minit sebelum sesi pengguna tamat secara automatik</p>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('masa_tamat_sesi_minit')" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end mt-6">
                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Simpan Tetapan
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>
</x-dashboard-layout>

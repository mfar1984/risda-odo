<x-dashboard-layout title="Kemaskini Log Pemandu">
    <x-ui.page-header
        title="Kemaskini Log Pemandu"
        description="Kemaskini maklumat perjalanan pemandu"
    >
        <x-ui.card>
            <form method="POST" action="{{ route('log-pemandu.update', $log) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Maklumat Status & Destinasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-forms.input-label for="status" value="Status Log" />
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="dalam_perjalanan" {{ old('status', $log->status) === 'dalam_perjalanan' ? 'selected' : '' }}>Sedang Berjalan</option>
                                <option value="selesai" {{ old('status', $log->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="tertunda" {{ old('status', $log->status) === 'tertunda' ? 'selected' : '' }}>Tertunda</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-forms.input-label for="destinasi" value="Destinasi" />
                            <x-forms.text-input id="destinasi" name="destinasi" type="text" class="mt-1 block w-full"
                                value="{{ old('destinasi', $log->destinasi) }}" required />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('destinasi')" />
                        </div>
                    </div>

                    {{-- Masa Check-in/Out --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-forms.input-label for="masa_keluar" value="Masa Check-in" />
                            <x-forms.text-input id="masa_keluar" name="masa_keluar" type="time" class="mt-1 block w-full"
                                value="{{ old('masa_keluar', $log->masa_keluar ? \Carbon\Carbon::parse($log->masa_keluar)->format('H:i') : '') }}" required />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('masa_keluar')" />
                        </div>

                        <div>
                            <x-forms.input-label for="masa_masuk" value="Masa Check-out" />
                            <x-forms.text-input id="masa_masuk" name="masa_masuk" type="time" class="mt-1 block w-full"
                                value="{{ old('masa_masuk', $log->masa_masuk ? \Carbon\Carbon::parse($log->masa_masuk)->format('H:i') : '') }}" />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('masa_masuk')" />
                        </div>
                    </div>

                        {{-- Lokasi Check-in / Check-out (Lat/Long + alamat) --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-900">Maklumat Lokasi</h3>
                            <div>
                                <x-forms.input-label value="GPS Mula Perjalanan" />
                                <x-map.location-picker
                                    :latitude="old('lokasi_checkin_lat', $log->lokasi_checkin_lat)"
                                    :longitude="old('lokasi_checkin_long', $log->lokasi_checkin_long)"
                                    input-lat="lokasi_checkin_lat"
                                    input-long="lokasi_checkin_long"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_checkin_lat')" />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_checkin_long')" />
                            </div>

                            <div class="mt-6">
                                <x-forms.input-label value="GPS Tamat Perjalanan" />
                                <x-map.location-picker
                                    :latitude="old('lokasi_checkout_lat', $log->lokasi_checkout_lat)"
                                    :longitude="old('lokasi_checkout_long', $log->lokasi_checkout_long)"
                                    input-lat="lokasi_checkout_lat"
                                    input-long="lokasi_checkout_long"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_checkout_lat')" />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_checkout_long')" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <x-forms.input-label for="lokasi_mula_perjalanan" value="Lokasi Mula Perjalanan" />
                                    <x-forms.text-input id="lokasi_mula_perjalanan" name="lokasi_mula_perjalanan" type="text" class="mt-1 block w-full"
                                        value="{{ old('lokasi_mula_perjalanan', $log->lokasi_mula_perjalanan) }}" />
                                    <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_mula_perjalanan')" />
                                </div>
                                <div>
                                    <x-forms.input-label for="lokasi_tamat_perjalanan" value="Lokasi Tamat Perjalanan" />
                                    <x-forms.text-input id="lokasi_tamat_perjalanan" name="lokasi_tamat_perjalanan" type="text" class="mt-1 block w-full"
                                        value="{{ old('lokasi_tamat_perjalanan', $log->lokasi_tamat_perjalanan) }}" />
                                    <x-forms.input-error class="mt-2" :messages="$errors->get('lokasi_tamat_perjalanan')" />
                                </div>
                            </div>
                    </div>

                    {{-- Odometer & Jarak --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-forms.input-label for="odometer_keluar" value="Odometer Check-in (km)" />
                            <x-forms.text-input id="odometer_keluar" name="odometer_keluar" type="number" class="mt-1 block w-full"
                                value="{{ old('odometer_keluar', $log->odometer_keluar) }}" min="0" required />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('odometer_keluar')" />
                        </div>
                        <div>
                            <x-forms.input-label for="odometer_masuk" value="Odometer Check-out (km)" />
                            <x-forms.text-input id="odometer_masuk" name="odometer_masuk" type="number" class="mt-1 block w-full"
                                value="{{ old('odometer_masuk', $log->odometer_masuk) }}" min="{{ $log->odometer_keluar }}" />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('odometer_masuk')" />
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <x-forms.input-label for="catatan" value="Catatan" />
                        <textarea id="catatan" name="catatan" rows="4" class="form-textarea mt-1 block w-full">{{ old('catatan', $log->catatan) }}</textarea>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('catatan')" />
                    </div>
                </div>

                <div class="flex justify-between">
                    <x-buttons.secondary-button type="button" onclick="window.history.back()">Batal</x-buttons.secondary-button>
                    <x-buttons.primary-button type="submit">Simpan Perubahan</x-buttons.primary-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page-header>
</x-dashboard-layout>


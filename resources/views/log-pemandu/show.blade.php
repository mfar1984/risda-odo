@php
    $gambarCheckin = $log->gambar_checkin ?? $log->resit_minyak;
    $gambarCheckout = $log->gambar_checkout ?? null;
    $tabKembali = request('tab', 'semua');
@endphp

<x-dashboard-layout title="Butiran Log Pemandu">
    <x-ui.page-header
        title="Butiran Log Pemandu"
        description="Maklumat lengkap perjalanan yang direkod oleh pemandu"
    >
        <div class="mt-6 space-y-6">
            {{-- Maklumat Program --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Maklumat Program</h3>
                <div class="mt-4 space-y-6">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Nama Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program->nama_program ?? 'Tiada Program' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Status Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program ? ucfirst($log->program->status) : '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Tarikh Mula Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program && $log->program->tarikh_mula ? $log->program->tarikh_mula->format('d/m/Y H:i') : '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Tarikh Selesai Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program && $log->program->tarikh_selesai ? $log->program->tarikh_selesai->format('d/m/Y H:i') : '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program->lokasi_program ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Anggaran KM" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program_jarak_anggaran_label ?? '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maklumat Pemandu & Kenderaan --}}
            <div class="my-6">
                <div class="border-t border-gray-200"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Maklumat Pemandu &amp; Kenderaan</h3>
                <div class="mt-4 space-y-6">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Nama Pemandu" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->pemandu->name ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="No. Plat Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->kenderaan->no_plat ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Jenis Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->kenderaan->jenama ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Model Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->kenderaan->model ?? '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maklumat Log --}}
            <div class="my-6">
                <div class="border-t border-gray-200"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Maklumat Log</h3>
                <div class="mt-4 space-y-6">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Masa Check-in" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->masa_keluar_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Masa Check-out" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->masa_masuk_label ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi Check-in" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_checkin_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi Check-out" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_checkout_label ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Odometer Check-in" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->odometer_keluar_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Odometer Check-out" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->odometer_masuk_label ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Jarak (KM)" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->jarak_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Jarak Perjalanan (Sistem)" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->program_jarak_anggaran_label ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div>
                        <x-forms.input-label value="Catatan" />
                        <textarea class="mt-1 block w-full form-input" rows="3" readonly>{{ $log->catatan ?? '-' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Gambar Odometer --}}
            <div class="my-6">
                <div class="border-t border-gray-200"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Gambar Odometer</h3>
                <div class="mt-4 space-y-6">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Gambar Check-in" />
                            @if($gambarCheckin)
                                <img src="{{ asset('storage/' . $gambarCheckin) }}" alt="Gambar Odometer Check-in" class="rounded-lg border mt-2">
                            @else
                                <x-forms.text-input class="mt-1 block w-full" value="Tiada gambar" readonly />
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Gambar Check-out" />
                            @if($gambarCheckout)
                                <img src="{{ asset('storage/' . $gambarCheckout) }}" alt="Gambar Odometer Check-out" class="rounded-lg border mt-2">
                            @else
                                <x-forms.text-input class="mt-1 block w-full" value="Tiada gambar" readonly />
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maklumat Audit --}}
            <div class="my-6">
                <div class="border-t border-gray-200"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Maklumat Audit</h3>
                <div class="mt-4 space-y-6">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Dicipta Oleh" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->creator->name ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Tarikh Cipta" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Dikemaskini Oleh" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->updater->name ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Tarikh Dikemaskini" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->updated_at ? $log->updated_at->format('d/m/Y H:i:s') : '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('log-pemandu.index', ['tab' => $tabKembali]) }}">
                    <x-buttons.secondary-button type="button">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                        Kembali
                    </x-buttons.secondary-button>
                </a>

                <div class="flex space-x-3">
                    @if(auth()->user()->adaKebenaran('log_pemandu_semua', 'kemaskini'))
                        <a href="{{ route('log-pemandu.edit', $log) }}">
                            <x-buttons.warning-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                Edit
                            </x-buttons.warning-button>
                        </a>
                    @endif

                    @if(auth()->user()->adaKebenaran('log_pemandu_semua', 'padam'))
                        <form action="{{ route('log-pemandu.destroy', $log) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam log ini?')">
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
    </x-ui.page-header>
</x-dashboard-layout>


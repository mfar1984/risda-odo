@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    // Helper to normalize stored file paths into valid URLs
    $resolveUrl = function (?string $path) {
        if (!$path) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) return $path;
        if (Str::startsWith($path, '/')) return $path; // already absolute
        if (Str::startsWith($path, 'storage/')) return asset($path);
        if (Str::startsWith($path, 'public/')) return Storage::url(Str::after($path, 'public/'));
        return Storage::url($path);
    };

    // Check-out = Start Journey (foto_odometer_keluar)
    // Check-in = End Journey (foto_odometer_masuk)
    $gambarCheckin = $resolveUrl($log->foto_odometer_masuk);
    $gambarCheckout = $resolveUrl($log->foto_odometer_keluar);
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
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->pemandu->risdaStaf->nama_penuh ?? '-' }}" readonly />
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
                            <x-forms.input-label value="Lokasi GPS Mula Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_checkin_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi GPS Tamat Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_checkout_label ?? '-' }}" readonly />
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi Mula Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_mula_perjalanan_label ?? '-' }}" readonly />
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Lokasi Tamat Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $log->lokasi_tamat_perjalanan_label ?? '-' }}" readonly />
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
                            <x-forms.input-label value="Gambar Check-out (Start Journey)" />
                            @if($gambarCheckout)
                                <div class="mt-2 border rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition-opacity" 
                                     style="max-width: 600px;"
                                     onclick="openImageModal('{{ $gambarCheckout }}', 'Gambar Odometer Start Journey')">
                                    <img src="{{ $gambarCheckout }}" 
                                         alt="Gambar Odometer Start Journey" 
                                         class="w-full h-auto object-cover"
                                         style="max-height: 250px;">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Klik gambar untuk zoom</p>
                            @else
                                <x-forms.text-input class="mt-1 block w-full" value="Tiada gambar" readonly />
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <x-forms.input-label value="Gambar Check-in (End Journey)" />
                            @if($gambarCheckin)
                                <div class="mt-2 border rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition-opacity" 
                                     style="max-width: 600px;"
                                     onclick="openImageModal('{{ $gambarCheckin }}', 'Gambar Odometer End Journey')">
                                    <img src="{{ $gambarCheckin }}" 
                                         alt="Gambar Odometer End Journey" 
                                         class="w-full h-auto object-cover"
                                         style="max-height: 250px;">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Klik gambar untuk zoom</p>
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

            {{-- Senarai Tuntutan --}}
            @php
                $tuntutan_list = $log->tuntutan ?? collect();
                $total_tuntutan = $tuntutan_list->sum('jumlah');
            @endphp
            
            @if($tuntutan_list->isNotEmpty())
            <div class="my-6">
                <div class="border-t border-gray-200"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                    <span class="material-symbols-outlined mr-2" style="font-size: 20px;">receipt_long</span>
                    Senarai Tuntutan untuk Perjalanan Ini
                </h3>
                <div class="mt-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tuntutan_list as $tuntutan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $tuntutan->kategori_label }}</div>
                                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important;">{{ $tuntutan->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">RM {{ number_format($tuntutan->jumlah, 2) }}</div>
                                        @if($tuntutan->keterangan)
                                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important;">{{ Str::limit($tuntutan->keterangan, 40) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-ui.status-badge 
                                            :status="$tuntutan->status" 
                                            :label="$tuntutan->status_label" 
                                            :color="$tuntutan->status_badge_color" 
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('laporan.laporan-tuntutan.show', $tuntutan) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors inline-flex items-center"
                                           title="Lihat Tuntutan">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="1" class="px-6 py-3 text-right text-sm font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">JUMLAH TUNTUTAN:</td>
                                    <td class="px-6 py-3 text-sm font-bold text-green-700" style="font-family: Poppins, sans-serif !important;">RM {{ number_format($total_tuntutan, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

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
                        <x-buttons.danger-button type="button" onclick="deleteLogPemanduItem({{ $log->id }})">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">delete</span>
                            Padam
                        </x-buttons.danger-button>
                    @endif
                </div>
            </div>
        </div>
    </x-ui.page-header>

    {{-- Image Modal for Zoom --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="relative max-w-7xl max-h-full" onclick="event.stopPropagation()">
            {{-- Close Button --}}
            <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">
                <span class="material-symbols-outlined" style="font-size: 32px;">close</span>
            </button>
            
            {{-- Image Container with Zoom --}}
            <div class="overflow-auto max-h-[90vh]" style="cursor: zoom-in;" onclick="toggleZoom(event)">
                <img id="modalImage" src="" alt="" class="transition-transform duration-200" style="max-width: 100%; height: auto;">
            </div>
            
            {{-- Image Title --}}
            <p id="modalTitle" class="text-white text-center mt-4 text-lg"></p>
            <p class="text-gray-400 text-center text-sm mt-2">Klik gambar untuk zoom | Klik luar untuk tutup</p>
        </div>
    </div>

    <script>
        let isZoomed = false;
        
        function openImageModal(imageUrl, title) {
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('modalTitle').textContent = title;
            document.body.style.overflow = 'hidden'; // Prevent background scroll
            isZoomed = false;
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scroll
            isZoomed = false;
            document.getElementById('modalImage').style.transform = 'scale(1)';
            document.getElementById('modalImage').style.cursor = 'zoom-in';
        }
        
        function toggleZoom(event) {
            event.stopPropagation();
            const img = document.getElementById('modalImage');
            
            if (!isZoomed) {
                img.style.transform = 'scale(2)';
                img.style.cursor = 'zoom-out';
                isZoomed = true;
            } else {
                img.style.transform = 'scale(1)';
                img.style.cursor = 'zoom-in';
                isZoomed = false;
            }
        }
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>


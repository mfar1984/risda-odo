@php
    use Illuminate\Support\Facades\Storage;
    $resitUrl = $tuntutan->resit ? Storage::url($tuntutan->resit) : null;
    $program = $tuntutan->logPemandu->program;
@endphp

<x-dashboard-layout title="Butiran Tuntutan">
    <x-ui.page-header
        title="Butiran Tuntutan: {{ $program->nama_program }}"
        description="Perincian lengkap tuntutan {{ $tuntutan->kategori_label }} untuk program ini"
    >
        <div class="space-y-8">
            {{-- Maklumat Program Card --}}
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Program</h3>
                        <p class="text-xs text-gray-500 mt-1">Rincian asas program dan tugasan</p>
                    </div>
                    <x-ui.status-badge :status="$program->status" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Nama Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->nama_program }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Lokasi" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->lokasi_program ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Anggaran KM" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->jarak_anggaran ? number_format($program->jarak_anggaran, 1) . ' km' : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemohon" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemohon->nama_penuh ?? '-' }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Tarikh Mula" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_mula?->format('d/m/Y H:i') ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Selesai" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_selesai?->format('d/m/Y H:i') ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemandu Tugasan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemandu->nama_penuh ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->kenderaan ? $program->kenderaan->no_plat . ' - ' . trim(($program->kenderaan->jenama ?? '') . ' ' . ($program->kenderaan->model ?? '')) : '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-ui.stat-card icon="receipt_long" icon-color="text-blue-600" :value="number_format($stats['jumlah_tuntutan'])" label="Jumlah Tuntutan" />
                <x-ui.stat-card icon="payments" icon-color="text-indigo-600" :value="number_format($stats['jumlah_keseluruhan'], 2)" prefix="RM " label="Jumlah Keseluruhan" />
                <x-ui.stat-card icon="pending_actions" icon-color="text-yellow-600" :value="number_format($stats['pending'])" label="Pending" />
                <x-ui.stat-card icon="check_circle" icon-color="text-green-600" :value="number_format($stats['diluluskan'])" label="Diluluskan" />
                <x-ui.stat-card icon="cancel" icon-color="text-red-600" :value="number_format($stats['ditolak'])" label="Ditolak" />
                <x-ui.stat-card icon="account_balance_wallet" icon-color="text-emerald-600" :value="number_format($stats['jumlah_diluluskan'], 2)" prefix="RM " label="Jumlah Diluluskan" />
            </div>

            {{-- Maklumat Tuntutan Card --}}
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Tuntutan</h3>
                        <p class="text-xs text-gray-500 mt-1">Rincian asas tuntutan ini</p>
                    </div>
                    <x-ui.status-badge 
                        :status="$tuntutan->status" 
                        :label="$tuntutan->status_label"
                        :color="$tuntutan->status_badge_color" 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Pemandu" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="No. Pekerja" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->tarikh_perjalanan?->format('d/m/Y') ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->kenderaan ? $tuntutan->logPemandu->kenderaan->no_plat . ' - ' . trim(($tuntutan->logPemandu->kenderaan->jenama ?? '') . ' ' . ($tuntutan->logPemandu->kenderaan->model ?? '')) : '-' }}" 
                                readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Kategori" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->kategori_label }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Jumlah Tuntutan" />
                            <x-forms.text-input class="mt-1 block w-full font-semibold text-blue-700" 
                                value="RM {{ number_format($tuntutan->jumlah, 2) }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Dituntut" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->created_at->format('d/m/Y H:i') }}" readonly />
                        </div>
                        @if($tuntutan->keterangan)
                        <div>
                            <x-forms.input-label value="Keterangan" />
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                      rows="1" readonly>{{ $tuntutan->keterangan }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            {{-- Receipt Card --}}
            @if($resitUrl)
            <x-ui.card>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-blue-600">receipt</span>
                    <h3 class="text-base font-semibold text-gray-900">Resit</h3>
                </div>

                <div class="flex justify-center">
                    <button 
                        onclick="openImageModal('{{ $resitUrl }}', 'Resit Tuntutan - {{ $tuntutan->kategori_label }}')"
                        class="relative group cursor-pointer">
                        <img src="{{ $resitUrl }}" 
                             alt="Resit Tuntutan" 
                             class="max-w-full rounded-lg shadow-md hover:opacity-90 transition-opacity"
                             style="max-width: 600px; max-height: 250px; object-fit: cover;">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black bg-opacity-30 rounded-lg">
                            <span class="material-symbols-outlined text-white text-5xl">zoom_in</span>
                        </div>
                    </button>
                </div>
            </x-ui.card>
            @endif

            {{-- Senarai Tuntutan untuk Program Ini --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">list_alt</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Tuntutan untuk Program Ini</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ number_format($relatedClaims->count() + 1) }}</div>
                </div>

                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh', 'align' => 'text-left'],
                        ['label' => 'Pemandu', 'align' => 'text-left'],
                        ['label' => 'Kategori', 'align' => 'text-left'],
                        ['label' => 'Jumlah (RM)', 'align' => 'text-right'],
                        ['label' => 'Status', 'align' => 'text-left'],
                        ['label' => 'Tindakan', 'align' => 'text-center'],
                    ]"
                    :actions="false"
                    empty-message="Tiada tuntutan lain untuk program ini."
                >
                    {{-- Current Claim (highlighted) --}}
                    <tr class="bg-blue-50 border-l-4 border-blue-500">
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div>{{ $tuntutan->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $tuntutan->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>{{ $tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $tuntutan->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $tuntutan->kategori_label }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900 font-semibold">
                            RM {{ number_format($tuntutan->jumlah, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <x-ui.status-badge 
                                :status="$tuntutan->status" 
                                :label="$tuntutan->status_label"
                                :color="$tuntutan->status_badge_color" 
                            />
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-medium text-blue-700">⬅ Tuntutan Semasa</span>
                        </td>
                    </tr>

                    {{-- Related Claims --}}
                    @forelse($relatedClaims as $claim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ $claim->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $claim->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $claim->kategori_label }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                RM {{ number_format($claim->jumlah, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge 
                                    :status="$claim->status" 
                                    :label="$claim->status_label"
                                    :color="$claim->status_badge_color" 
                                />
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.laporan-tuntutan.show', $claim->id) }}" 
                                   class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Lihat Butiran">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        @if($relatedClaims->count() === 0)
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tiada tuntutan lain untuk program ini.</td>
                        </tr>
                        @endif
                    @endforelse
                </x-ui.data-table>
            </x-ui.card>

            {{-- Processing Info Card (if processed) --}}
            @if($tuntutan->tarikh_diproses)
            <x-ui.card>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-blue-600">verified</span>
                    <h3 class="text-base font-semibold text-gray-900">Maklumat Pemprosesan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm mb-6">
                    <div>
                        <x-forms.input-label value="Diproses Oleh" />
                        <x-forms.text-input class="mt-1 block w-full" 
                            value="{{ $tuntutan->diprosesOleh->risdaStaf->nama_penuh ?? $tuntutan->diprosesOleh->name ?? '-' }}" readonly />
                    </div>
                    <div>
                        <x-forms.input-label value="Tarikh Diproses" />
                        <x-forms.text-input class="mt-1 block w-full" 
                            value="{{ $tuntutan->tarikh_diproses->format('d/m/Y H:i') }}" readonly />
                    </div>
                </div>

                @if($tuntutan->status === 'ditolak' && $tuntutan->alasan_tolak)
                <div class="pt-6 border-t border-gray-200">
                    <x-forms.input-label value="Alasan Penolakan" />
                    <textarea class="mt-1 block w-full border-red-300 bg-red-50 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-red-900 text-sm" 
                              rows="4" readonly>{{ $tuntutan->alasan_tolak }}</textarea>
                    <p class="mt-2 text-xs text-orange-600 font-medium">
                        <span class="material-symbols-outlined text-xs align-middle">info</span>
                        Pemandu boleh edit & hantar semula
                    </p>
                </div>
                @endif

                @if($tuntutan->status === 'digantung' && $tuntutan->alasan_gantung)
                <div class="pt-6 border-t border-gray-200">
                    <x-forms.input-label value="Alasan Pembatalan" />
                    <textarea class="mt-1 block w-full border-gray-300 bg-gray-50 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-900 text-sm" 
                              rows="4" readonly>{{ $tuntutan->alasan_gantung }}</textarea>
                    <p class="mt-2 text-xs text-red-600 font-medium">
                        <span class="material-symbols-outlined text-xs align-middle">block</span>
                        Tuntutan tidak boleh diedit
                    </p>
                </div>
                @endif
            </x-ui.card>
            @endif

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-2">
                {{-- Approve Button --}}
                @if($tuntutan->canBeApproved() && Auth::user()->adaKebenaran('laporan_tuntutan', 'terima'))
                <button onclick="approveItem({{ $tuntutan->id }})" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                        title="Lulus Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">check_circle</span>
                    Lulus
                </button>
                @endif

                {{-- Reject Button --}}
                @if($tuntutan->canBeRejected() && Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak'))
                <button onclick="openRejectModal({{ $tuntutan->id }})" 
                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors"
                        title="Tolak Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">cancel</span>
                    Tolak
                </button>
                @endif

                {{-- Cancel Button --}}
                @if($tuntutan->canBeCancelled() && Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung'))
                <button onclick="openCancelModal({{ $tuntutan->id }})" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
                        title="Batal Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">block</span>
                    Batal
                </button>
                @endif

                {{-- Delete Button --}}
                @if(Auth::user()->adaKebenaran('laporan_tuntutan', 'padam'))
                <button onclick="deleteItem({{ $tuntutan->id }})" 
                        class="inline-flex items-center px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
                        title="Padam Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">delete</span>
                    Padam
                </button>
                @endif
            </div>
        </div>
    </x-ui.page-header>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[1100] flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tolak Tuntutan</h3>
                <form id="rejectForm" method="POST" action="{{ route('laporan.laporan-tuntutan.reject', $tuntutan->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_tolak" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan: <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alasan_tolak" name="alasan_tolak" rows="4" required minlength="10" maxlength="1000"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Sila berikan alasan penolakan (minimum 10 aksara)"></textarea>
                        <p class="mt-2 text-xs text-orange-600">⚠️ Pemandu boleh edit & hantar semula</p>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors">
                            Tolak Tuntutan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Cancel Modal --}}
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[1100] flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Batal Tuntutan</h3>
                <form id="cancelForm" method="POST" action="{{ route('laporan.laporan-tuntutan.cancel', $tuntutan->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_gantung" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Pembatalan: <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alasan_gantung" name="alasan_gantung" rows="4" required minlength="10" maxlength="1000"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Sila berikan alasan pembatalan (minimum 10 aksara)"></textarea>
                        <p class="mt-2 text-sm text-red-600 font-medium">⚠️ AMARAN: Tindakan ini kekal! Pemandu tidak boleh edit.</p>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeCancelModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                            Batal Tuntutan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Image Modal for Receipt (matching laporan-kos design) --}}
    <div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);">
        <span onclick="closeImageModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
        <img id="modalImage" style="margin: auto; display: block; max-width: 90%; max-height: 90%; margin-top: 50px; cursor: zoom-in;" onclick="toggleZoom(this)">
        <div id="modalCaption" style="margin: auto; display: block; width: 80%; max-width: 700px; text-align: center; color: #ccc; padding: 10px 0; font-size: 16px;"></div>
    </div>

    {{-- JavaScript for modals and actions --}}
    <script>
        // Image Modal Functions (matching laporan-kos)
        let isZoomed = false;
        
        function openImageModal(imageSrc, caption) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            
            modal.style.display = 'block';
            modalImg.src = imageSrc;
            modalCaption.innerHTML = caption;
            isZoomed = false;
            modalImg.style.cursor = 'zoom-in';
            modalImg.style.maxWidth = '90%';
            modalImg.style.maxHeight = '90%';
            modalImg.style.width = 'auto';
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            isZoomed = false;
        }
        
        function toggleZoom(img) {
            if (!isZoomed) {
                img.style.maxWidth = '100%';
                img.style.maxHeight = 'none';
                img.style.width = '100%';
                img.style.cursor = 'zoom-out';
                isZoomed = true;
            } else {
                img.style.maxWidth = '90%';
                img.style.maxHeight = '90%';
                img.style.width = 'auto';
                img.style.cursor = 'zoom-in';
                isZoomed = false;
            }
        }
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
                closeRejectModal();
                closeCancelModal();
            }
        });
        
        // Close modal on click outside image
        document.getElementById('imageModal').addEventListener('click', function(event) {
            if (event.target.id === 'imageModal') {
                closeImageModal();
            }
        });

        // Approve
        function approveItem(id) {
            if (!confirm('Adakah anda pasti untuk meluluskan tuntutan ini?')) {
                return;
            }

            fetch(`/laporan/laporan-tuntutan/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Ralat berlaku');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ralat berlaku. Sila cuba lagi.');
            });
        }

        // Reject Modal
        function openRejectModal(id) {
            document.getElementById('alasan_tolak').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Ralat berlaku');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ralat berlaku. Sila cuba lagi.');
            });
        });

        // Cancel Modal
        function openCancelModal(id) {
            document.getElementById('alasan_gantung').value = '';
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        document.getElementById('cancelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Ralat berlaku');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ralat berlaku. Sila cuba lagi.');
            });
        });

        // Delete
        function deleteItem(id) {
            if (!confirm('Adakah anda pasti untuk memadam tuntutan ini? Tindakan ini tidak boleh dibatalkan.')) {
                return;
            }

            fetch(`/laporan/laporan-tuntutan/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = '/laporan/laporan-tuntutan';
                } else {
                    alert(data.message || 'Ralat berlaku');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ralat berlaku. Sila cuba lagi.');
            });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const cancelModal = document.getElementById('cancelModal');
            
            if (event.target == rejectModal) {
                closeRejectModal();
            }
            if (event.target == cancelModal) {
                closeCancelModal();
            }
        }
    </script>
</x-dashboard-layout>

@php
    $tuntutan_collection = $tuntutan ?? collect();
    if ($tuntutan instanceof \Illuminate\Pagination\Paginator || $tuntutan instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $tuntutan_collection = $tuntutan->getCollection();
    } else {
        $tuntutan_collection = collect($tuntutan);
    }

    // Calculate statistics
    $total_pending = $tuntutan_collection->where('status', 'pending')->sum('jumlah');
    $total_diluluskan = $tuntutan_collection->where('status', 'diluluskan')->sum('jumlah');
    $total_ditolak = $tuntutan_collection->where('status', 'ditolak')->sum('jumlah');
    $total_digantung = $tuntutan_collection->where('status', 'digantung')->sum('jumlah');
    $count_pending = $tuntutan_collection->where('status', 'pending')->count();
    $count_diluluskan = $tuntutan_collection->where('status', 'diluluskan')->count();
    $jumlah_keseluruhan = $tuntutan_collection->sum('jumlah');
@endphp

<x-dashboard-layout title="Laporan Tuntutan">
    <x-ui.page-header
        title="Laporan Tuntutan"
        description="Analisis tuntutan pemandu termasuk tol, parking, makanan, penginapan dan lain-lain"
    >
        {{-- Overall Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card 
                icon="pending_actions" 
                icon-color="text-yellow-600" 
                :value="$count_pending" 
                label="Pending" 
            />
            <x-ui.stat-card 
                icon="check_circle" 
                icon-color="text-green-600" 
                :value="$count_diluluskan" 
                label="Diluluskan" 
            />
            <x-ui.stat-card 
                icon="payments" 
                icon-color="text-indigo-600" 
                :value="number_format($jumlah_keseluruhan, 2)" 
                prefix="RM " 
                label="Jumlah Keseluruhan" 
            />
            <x-ui.stat-card 
                icon="hourglass_empty" 
                icon-color="text-orange-600" 
                :value="number_format($total_pending, 2)" 
                prefix="RM " 
                label="Pending (RM)" 
            />
            <x-ui.stat-card 
                icon="paid" 
                icon-color="text-emerald-600" 
                :value="number_format($total_diluluskan, 2)" 
                prefix="RM " 
                label="Diluluskan (RM)" 
            />
            <x-ui.stat-card 
                icon="cancel" 
                icon-color="text-red-600" 
                :value="number_format($total_ditolak, 2)" 
                prefix="RM " 
                label="Ditolak (RM)" 
            />
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-tuntutan')"
            search-placeholder="Cari nama program atau pemandu"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => $status_list
                ],
                [
                    'name' => 'kategori',
                    'type' => 'select',
                    'placeholder' => 'Semua Kategori',
                    'options' => $kategori_list
                ],
                [
                    'name' => 'tarikh_dari',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Dari'
                ],
                [
                    'name' => 'tarikh_hingga',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Hingga'
                ]
            ]"
            :reset-url="route('laporan.laporan-tuntutan')"
        />

        <x-ui.data-table
            :headers="[
                ['label' => 'Tuntutan', 'align' => 'text-left'],
                ['label' => 'Pemandu & Program', 'align' => 'text-left'],
                ['label' => 'Jumlah & Status', 'align' => 'text-left'],
            ]"
            empty-message="Tiada tuntutan ditemui untuk penapis semasa."
        >
            @forelse($tuntutan_collection as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $item->kategori_label }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Tarikh: {{ $item->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            @if($item->keterangan)
                                {{ Str::limit($item->keterangan, 40) }}
                            @else
                                Tiada keterangan
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $item->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            {{ $item->logPemandu->pemandu->risdaStaf->no_pekerja ?? '' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1 truncate" title="{{ $item->logPemandu->program->nama_program ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 10px !important; max-width: 12rem;">
                            Program: {{ $item->logPemandu->program->nama_program ?? '-' }}
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            RM {{ number_format($item->jumlah, 2) }}
                        </div>
                        <div class="mt-2">
                            <x-ui.status-badge 
                                :status="$item->status" 
                                :label="$item->status_label" 
                                :color="$item->status_badge_color" 
                            />
                        </div>
                        @if($item->tarikh_diproses)
                            <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                {{ $item->tarikh_diproses->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <x-ui.action-buttons
                            :show-url="route('laporan.laporan-tuntutan.show', $item)"
                            :show-view="true"
                            :show-edit="false"
                            :show-delete="Auth::user()->adaKebenaran('laporan_tuntutan', 'padam')"
                            :delete-id="$item->id"
                            delete-route="laporan.laporan-tuntutan.destroy"
                            :delete-confirm="'Adakah anda pasti untuk memadam tuntutan ini?'"
                            :custom-actions="array_filter([
                                $item->canBeApproved() && Auth::user()->adaKebenaran('laporan_tuntutan', 'terima') ? [
                                    'onclick' => 'approveItem(' . $item->id . ')',
                                    'icon' => 'check_circle',
                                    'class' => 'text-green-600 hover:text-green-800',
                                    'title' => 'Lulus'
                                ] : null,
                                $item->canBeRejected() && Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak') ? [
                                    'onclick' => 'openRejectModal(' . $item->id . ')',
                                    'icon' => 'cancel',
                                    'class' => 'text-orange-600 hover:text-orange-800',
                                    'title' => 'Tolak'
                                ] : null,
                                $item->canBeCancelled() && Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung') ? [
                                    'onclick' => 'openCancelModal(' . $item->id . ')',
                                    'icon' => 'block',
                                    'class' => 'text-red-600 hover:text-red-800',
                                    'title' => 'Batal'
                                ] : null,
                                [
                                    'url' => route('laporan.laporan-tuntutan.export-pdf', array_filter([
                                        'status' => request('status'),
                                        'kategori' => request('kategori'),
                                        'tarikh_dari' => request('tarikh_dari'),
                                        'tarikh_hingga' => request('tarikh_hingga'),
                                        'search' => request('search'),
                                        'tuntutan_id' => $item->id
                                    ])),
                                    'icon' => 'picture_as_pdf',
                                    'class' => 'text-purple-600 hover:text-purple-800',
                                    'title' => 'Eksport ke PDF'
                                ]
                            ])"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        Tiada tuntutan ditemui buat masa ini.
                    </td>
                </tr>
            @endforelse
        </x-ui.data-table>

        <x-ui.pagination :paginator="$tuntutan" record-label="tuntutan" />
    </x-ui.page-header>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tolak Tuntutan</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_tolak" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan: <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alasan_tolak" name="alasan_tolak" rows="4" required minlength="10" maxlength="1000"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Sila berikan alasan penolakan (minimum 10 aksara)"></textarea>
                        <p class="mt-2 text-sm text-orange-600">⚠️ Pemandu boleh edit & hantar semula</p>
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
    <div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Batal Tuntutan</h3>
                <form id="cancelForm" method="POST">
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

    {{-- JavaScript for modals and actions --}}
    <script>
        let currentItemId = null;

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
            currentItemId = id;
            document.getElementById('rejectForm').action = `/laporan/laporan-tuntutan/${id}/reject`;
            document.getElementById('alasan_tolak').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            currentItemId = null;
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
            currentItemId = id;
            document.getElementById('cancelForm').action = `/laporan/laporan-tuntutan/${id}/cancel`;
            document.getElementById('alasan_gantung').value = '';
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            currentItemId = null;
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

<x-dashboard-layout
    title="Edit Rekod Penyelenggaraan"
    description="Kemaskini rekod penyelenggaraan kenderaan"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Kemaskini Penyelenggaraan Kenderaan') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Kemaskini rekod penyelenggaraan kenderaan dalam sistem') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.update-selenggara', $selenggara) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Maklumat Penyelenggaraan Section -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Penyelenggaraan</h3>
                        
                        <!-- Row 1: Kenderaan & Kategori Kos -->
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="kenderaan_id" value="Kenderaan" />
                                <select
                                    id="kenderaan_id"
                                    name="kenderaan_id"
                                    class="form-select mt-1"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    required
                                >
                                    <option value="">Pilih Kenderaan</option>
                                    @php
                                        $currentUser = auth()->user();
                                        $kenderaanQuery = \App\Models\Kenderaan::query();
                                        
                                        if ($currentUser->jenis_organisasi === 'bahagian') {
                                            $kenderaanQuery->where('bahagian_id', $currentUser->organisasi_id);
                                        } elseif ($currentUser->jenis_organisasi === 'stesen') {
                                            $kenderaanQuery->where('stesen_id', $currentUser->organisasi_id);
                                        }
                                        
                                        $kenderaanList = $kenderaanQuery->where('status', '!=', 'tidak_aktif')->get();
                                    @endphp
                                    @foreach($kenderaanList as $k)
                                        <option value="{{ $k->id }}" {{ (old('kenderaan_id', $selenggara->kenderaan_id) == $k->id) ? 'selected' : '' }}>
                                            {{ $k->no_plat }} - {{ $k->nama_penuh }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('kenderaan_id')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="kategori_kos_id" value="Kategori Kos" />
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 0.25rem;">
                                    <select
                                        id="kategori_kos_id"
                                        name="kategori_kos_id"
                                        class="form-select"
                                        style="font-family: Poppins, sans-serif !important; font-size: 12px !important; flex: 1;"
                                        required
                                    >
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategoriList as $kategori)
                                            <option value="{{ $kategori->id }}" {{ (old('kategori_kos_id', $selenggara->kategori_kos_id) == $kategori->id) ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        onclick="document.getElementById('kategoriModal').classList.remove('hidden')"
                                        class="bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center border-0"
                                        style="min-width: 32px; height: 32px; padding: 0;"
                                        title="Tambah Kategori Baru"
                                    >
                                        <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="padamKategori()"
                                        class="bg-red-600 text-white rounded hover:bg-red-700 flex items-center justify-center border-0"
                                        style="min-width: 32px; height: 32px; padding: 0;"
                                        title="Padam Kategori"
                                    >
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </div>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('kategori_kos_id')" />
                            </div>
                        </div>

                        <!-- Row 2: Tarikh Mula & Tarikh Selesai -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_mula" value="Tarikh Mula" />
                                <x-forms.text-input
                                    id="tarikh_mula"
                                    name="tarikh_mula"
                                    type="date"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ old('tarikh_mula', $selenggara->tarikh_mula->format('Y-m-d')) }}"
                                    required
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('tarikh_mula')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="tarikh_selesai" value="Tarikh Selesai" />
                                <x-forms.text-input
                                    id="tarikh_selesai"
                                    name="tarikh_selesai"
                                    type="date"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ old('tarikh_selesai', $selenggara->tarikh_selesai->format('Y-m-d')) }}"
                                    required
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('tarikh_selesai')" />
                            </div>
                        </div>

                        <!-- Row 3: Jumlah Kos & Status -->
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <x-forms.input-label for="jumlah_kos" value="Jumlah Kos (RM)" />
                                <x-forms.text-input
                                    id="jumlah_kos"
                                    name="jumlah_kos"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    value="{{ old('jumlah_kos', number_format($selenggara->jumlah_kos, 2, '.', '')) }}"
                                    required
                                    placeholder="Contoh: 250.00"
                                />
                                <x-forms.input-error class="mt-2" :messages="$errors->get('jumlah_kos')" />
                            </div>
                            <div style="flex: 1;">
                                <x-forms.input-label for="status" value="Status" />
                                <select
                                    id="status"
                                    name="status"
                                    class="form-select mt-1"
                                    style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                    required
                                >
                                    <option value="selesai" {{ old('status', $selenggara->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dalam_proses" {{ old('status', $selenggara->status) == 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                                    <option value="dijadualkan" {{ old('status', $selenggara->status) == 'dijadualkan' ? 'selected' : '' }}>Dijadualkan</option>
                                </select>
                                <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
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
                                placeholder="Masukkan keterangan tambahan (pilihan)"
                            >{{ old('keterangan', $selenggara->keterangan) }}</textarea>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('keterangan')" />
                        </div>
                    </div>

                    <!-- Maklumat Tambahan Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <h3 class="text-md font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Maklumat Tambahan</h3>
                        
                        <!-- Row 1: Tukar Minyak Checkbox -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="tukar_minyak"
                                    id="tukar_minyak"
                                    value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    onchange="document.getElementById('jangka_hayat_section').classList.toggle('hidden', !this.checked)"
                                    {{ old('tukar_minyak', $selenggara->tukar_minyak) ? 'checked' : '' }}
                                />
                                <span class="ml-2 text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tukar Minyak Enjin</span>
                            </label>
                        </div>

                        <!-- Row 2: Jangka Hayat KM (conditional) -->
                        <div id="jangka_hayat_section" class="{{ old('tukar_minyak', $selenggara->tukar_minyak) ? '' : 'hidden' }}">
                            <x-forms.input-label for="jangka_hayat_km" value="Jangka Hayat Minyak (KM)" />
                            <x-forms.text-input
                                id="jangka_hayat_km"
                                name="jangka_hayat_km"
                                type="number"
                                min="1000"
                                max="50000"
                                class="mt-1 block w-full"
                                style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                                value="{{ old('jangka_hayat_km', $selenggara->jangka_hayat_km ?? 5000) }}"
                                placeholder="Contoh: 5000"
                            />
                            <p class="mt-1 text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                Masukkan jangka hayat minyak dalam kilometer (contoh: 5000 km)
                            </p>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('jangka_hayat_km')" />
                        </div>

                        <!-- Row 3: Fail Invois -->
                        <div>
                            <x-forms.input-label for="fail_invois" value="Fail Invois / Resit (PDF, JPG, PNG)" />
                            
                            @if($selenggara->fail_invois)
                                <div class="mb-3 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-700 mb-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        <strong>Fail Sedia Ada:</strong> {{ basename($selenggara->fail_invois) }}
                                    </p>
                                    <a href="{{ Storage::url($selenggara->fail_invois) }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                        <span class="material-symbols-outlined mr-1" style="font-size: 14px;">download</span>
                                        Muat Turun Fail
                                    </a>
                                </div>
                            @endif
                            
                            <input
                                type="file"
                                id="fail_invois"
                                name="fail_invois"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                            />
                            <p class="mt-1 text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                {{ $selenggara->fail_invois ? 'Pilih fail baru untuk menggantikan fail sedia ada. Biarkan kosong untuk kekalkan fail sedia ada.' : 'Maksimum saiz fail: 10MB' }}
                            </p>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('fail_invois')" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3 mt-8">
                        <a href="{{ route('pengurusan.senarai-selenggara') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">cancel</span>
                            Batal
                        </a>
                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Kemaskini Penyelenggaraan
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>

        <!-- Modal for Adding New Category -->
        <div id="kategoriModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('kategoriModal').classList.add('hidden')"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tambah Kategori Kos Baharu</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="kategori_baru" class="block text-sm font-medium text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Nama Kategori</label>
                            <input
                                type="text"
                                id="kategori_baru"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Alat Ganti"
                                style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                            />
                        </div>
                        
                        <div>
                            <label for="keterangan_kategori" class="block text-sm font-medium text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Keterangan</label>
                            <textarea
                                id="keterangan_kategori"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan keterangan (pilihan)"
                                style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="document.getElementById('kategoriModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            onclick="simpanKategori()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            style="font-family: Poppins, sans-serif !important; font-size: 12px !important;"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function simpanKategori() {
            const namaBaru = document.getElementById('kategori_baru').value.trim();
            const keterangan = document.getElementById('keterangan_kategori').value.trim();
            
            if (!namaBaru) {
                alert('Sila masukkan nama kategori');
                return;
            }
            
            fetch('{{ route('pengurusan.store-kategori-kos') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nama_kategori: namaBaru,
                    keterangan: keterangan
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('kategori_kos_id');
                    const option = new Option(data.kategori.nama_kategori, data.kategori.id, true, true);
                    select.add(option);
                    document.getElementById('kategoriModal').classList.add('hidden');
                    document.getElementById('kategori_baru').value = '';
                    document.getElementById('keterangan_kategori').value = '';
                } else {
                    alert('Gagal menambah kategori: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function padamKategori() {
            const select = document.getElementById('kategori_kos_id');
            const selectedOption = select.options[select.selectedIndex];
            const kategoriId = selectedOption.value;
            
            if (!kategoriId) {
                alert('Sila pilih kategori yang ingin dipadam');
                return;
            }
            
            if (!confirm('Adakah anda pasti untuk memadam kategori "' + selectedOption.text + '"?')) {
                return;
            }
            
            fetch('{{ url('pengurusan/kategori-kos-selenggara') }}/' + kategoriId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove option from dropdown
                    selectedOption.remove();
                    alert(data.message);
                } else {
                    alert('Gagal memadam kategori: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
        </script>
</x-dashboard-layout>

<x-dashboard-layout 
    title="Tambah Kumpulan"
    description="Tambah kumpulan pengguna baru dengan kebenaran akses"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Kumpulan Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Tambah kumpulan pengguna baru dengan kebenaran akses') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.store-kumpulan') }}" class="mt-6 space-y-6">
                    @csrf
                
                <!-- Row 1: Nama Kumpulan -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="nama_kumpulan" value="Nama Kumpulan" />
                        <x-forms.text-input
                            id="nama_kumpulan"
                            name="nama_kumpulan"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('nama_kumpulan') }}"
                            required
                            autofocus
                            placeholder="Contoh: Admin Bahagian Sibu"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('nama_kumpulan')" />
                    </div>
                </div>

                <!-- Row 2: Status & Keterangan -->
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <x-forms.input-label for="status" value="Status" />
                        <select
                            id="status"
                            name="status"
                            class="form-select mt-1"
                            required
                        >
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="gantung" {{ old('status') == 'gantung' ? 'selected' : '' }}>Gantung</option>
                        </select>
                        <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>
                    
                    <div style="flex: 1;">
                        <x-forms.input-label for="keterangan" value="Keterangan" />
                        <x-forms.text-input 
                            id="keterangan" 
                            name="keterangan" 
                            type="text" 
                            class="mt-1 block w-full" 
                            value="{{ old('keterangan') }}"
                            placeholder="Keterangan tambahan (pilihan)"
                        />
                        <x-forms.input-error class="mt-2" :messages="$errors->get('keterangan')" />
                    </div>
                </div>

                <!-- Separator -->
                <div class="my-8">
                    <div class="border-t border-gray-200"></div>
                    <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                        Kebenaran Asas Peranan (Permission Matrix)
                    </h3>
                    <p class="text-sm text-gray-600 mb-6" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        Tetapkan kebenaran akses untuk setiap modul dalam sistem
                    </p>
                </div>

                <!-- Permission Matrix Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; min-width: 200px;">
                                    Kategori
                                </th>
                                @foreach($permissionLabels as $action => $label)
                                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 10px !important; min-width: 80px;">
                                    {{ $label }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($permissionMatrix as $module => $permissions)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                    {{ $moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}
                                </td>
                                @foreach($permissionLabels as $action => $label)
                                <td class="px-3 py-3 text-center border-r border-gray-300">
                                    @if(isset($permissions[$action]))
                                    <input
                                        type="checkbox"
                                        name="kebenaran_matrix[{{ $module }}][{{ $action }}]"
                                        value="1"
                                        class="form-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        {{ old("kebenaran_matrix.{$module}.{$action}") ? 'checked' : '' }}
                                    >
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
                <div class="flex justify-between items-center mt-6 p-4 bg-gray-50 rounded">
                    <div class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        Tindakan Pantas:
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="selectAllPermissions()" class="text-blue-600 hover:text-blue-800 text-sm" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Pilih Semua
                        </button>
                        <button type="button" onclick="clearAllPermissions()" class="text-red-600 hover:text-red-800 text-sm" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Kosongkan Semua
                        </button>
                        <button type="button" onclick="selectViewOnly()" class="text-green-600 hover:text-green-800 text-sm" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Lihat Sahaja
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-8">
                    <x-buttons.primary-button type="submit">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                        Simpan Kumpulan
                    </x-buttons.primary-button>
                </div>
                </form>
            </section>
        </x-ui.container>

    <!-- JavaScript for Dynamic Loading and Matrix Actions -->
    <script>


        // Matrix action functions
        function selectAllPermissions() {
            document.querySelectorAll('input[type="checkbox"][name^="kebenaran_matrix"]').forEach(cb => {
                cb.checked = true;
            });
        }

        function clearAllPermissions() {
            document.querySelectorAll('input[type="checkbox"][name^="kebenaran_matrix"]').forEach(cb => {
                cb.checked = false;
            });
        }

        function selectViewOnly() {
            clearAllPermissions();
            document.querySelectorAll('input[type="checkbox"][name*="[lihat]"]').forEach(cb => {
                cb.checked = true;
            });
        }
    </script>
</x-dashboard-layout>

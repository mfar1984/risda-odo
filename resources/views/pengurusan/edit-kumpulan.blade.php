@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout
    title="Edit Kumpulan Pengguna"
    description="Kemaskini maklumat kumpulan pengguna"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Kumpulan Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Kemaskini maklumat kumpulan pengguna') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('pengurusan.update-kumpulan', $userGroup) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Row 1: Nama Kumpulan -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_kumpulan" value="Nama Kumpulan" />
                            <x-forms.text-input 
                                id="nama_kumpulan" 
                                name="nama_kumpulan" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('nama_kumpulan', $userGroup->nama_kumpulan) }}"
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
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="aktif" {{ old('status', $userGroup->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status', $userGroup->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="gantung" {{ old('status', $userGroup->status) == 'gantung' ? 'selected' : '' }}>Gantung</option>
                            </select>
                            <x-forms.input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="keterangan" value="Keterangan (Pilihan)" />
                            <x-forms.text-input 
                                id="keterangan" 
                                name="keterangan" 
                                type="text" 
                                class="mt-1 block w-full" 
                                value="{{ old('keterangan', $userGroup->keterangan) }}"
                                placeholder="Keterangan tambahan"
                            />
                            <x-forms.input-error class="mt-2" :messages="$errors->get('keterangan')" />
                        </div>
                    </div>

                    <!-- Permission Matrix Section -->
                    <div class="border-t pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">Matriks Kebenaran</h3>
                        </div>
                        
                        <div class="overflow-x-auto permission-matrix-wrapper">
                            <table class="min-w-full divide-y divide-gray-200 permission-matrix-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                            Modul
                                        </th>
                                        @foreach($permissionLabels as $key => $label)
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                            {{ $label }}
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $defaultMatrix = \App\Models\UserGroup::getDefaultPermissionMatrix();
                                    @endphp
                                    @foreach($defaultMatrix as $module => $defaultPermissions)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            {{ $moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}
                                        </td>
                                        @foreach($permissionLabels as $permission => $permLabel)
                                        <td class="px-3 py-4 whitespace-nowrap text-center" @if(isset($defaultPermissions[$permission])) data-label="{{ $permLabel }}" @endif>
                                            @if(isset($defaultPermissions[$permission]))
                                            <input
                                                type="checkbox"
                                                name="kebenaran_matrix[{{ $module }}][{{ $permission }}]"
                                                value="1"
                                                {{ (old("kebenaran_matrix.{$module}.{$permission}") || (isset($permissionMatrix[$module][$permission]) && $permissionMatrix[$module][$permission])) ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
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

                        <!-- Quick Actions (align sama seperti tambah-kumpulan) -->
                        <div class="flex justify-between items-center mt-6 p-4 bg-gray-50 rounded">
                            <div class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                Tindakan Pantas:
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" onclick="selectAllPermissions()" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Pilih Semua
                                </button>
                                <button type="button" onclick="clearAllPermissions()" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Kosongkan Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-kumpulan') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Batal
                            </x-buttons.secondary-button>
                        </a>

                        <x-buttons.primary-button type="submit">
                            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">save</span>
                            Kemaskini Kumpulan
                        </x-buttons.primary-button>
                    </div>
                </form>
            </section>
        </x-ui.container>

    <!-- JavaScript for Matrix Actions -->
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
    </script>
</x-dashboard-layout>

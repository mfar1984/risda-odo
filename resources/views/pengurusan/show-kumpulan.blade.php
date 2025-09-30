<x-dashboard-layout
    title="Lihat Kumpulan Pengguna"
    description="Maklumat terperinci kumpulan pengguna"
    >
        <x-ui.container class="w-full">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Kumpulan Pengguna') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Maklumat terperinci kumpulan pengguna') }}
                    </p>
                </header>

                <div class="mt-6 space-y-6">
                    <!-- Basic Information -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="nama_kumpulan" value="Nama Kumpulan" />
                            <x-forms.text-input
                                id="nama_kumpulan"
                                name="nama_kumpulan"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $userGroup->nama_kumpulan }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="status" value="Status" />
                            <x-forms.text-input
                                id="status"
                                name="status"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ ucfirst(str_replace('_', ' ', $userGroup->status)) }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Row 2: Keterangan -->
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <x-forms.input-label for="keterangan" value="Keterangan" />
                            <x-forms.text-input
                                id="keterangan"
                                name="keterangan"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $userGroup->keterangan ?: '-' }}"
                                readonly
                            />
                        </div>

                        <div style="flex: 1;">
                            <x-forms.input-label for="dicipta_oleh" value="Dicipta Oleh" />
                            <x-forms.text-input
                                id="dicipta_oleh"
                                name="dicipta_oleh"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ $userGroup->pencipta->name ?? 'Sistem' }}"
                                readonly
                            />
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="my-6">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Permission Matrix -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 16px !important;">
                            Matriks Kebenaran
                        </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
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
                                @foreach($defaultMatrix as $module => $permissions)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                        {{ $moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}
                                    </td>
                                    @foreach($permissionLabels as $permission => $permLabel)
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        @if(isset($permissions[$permission]))
                                            @if(isset($userGroup->kebenaran_matrix[$module][$permission]) && $userGroup->kebenaran_matrix[$module][$permission])
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    ✓
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                                    ✗
                                                </span>
                                            @endif
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

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('pengurusan.senarai-kumpulan') }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>

                        <div class="flex space-x-3">
                            <a href="{{ route('pengurusan.edit-kumpulan', $userGroup) }}">
                                <x-buttons.warning-button type="button">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">edit</span>
                                    Edit
                                </x-buttons.warning-button>
                            </a>

                            <form action="{{ route('pengurusan.delete-kumpulan', $userGroup) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $userGroup->nama_kumpulan }}?')">
                                @csrf
                                @method('DELETE')
                                <x-buttons.danger-button type="submit">
                                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">delete</span>
                                    Padam
                                </x-buttons.danger-button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </x-ui.container>
</x-dashboard-layout>

{{-- Audit Trail Tab Content (Admin Only) --}}
<div class="audit-trail-tab">
    <!-- Generate Report Form -->
    <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h4 class="text-base font-semibold text-gray-900 mb-1" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                    Jana Laporan Audit Trail
                </h4>
                <p class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                    Pilih pengguna dan julat tarikh untuk menjana laporan audit trail
                </p>
            </div>
            <span class="material-symbols-outlined text-indigo-600" style="font-size: 24px;">policy</span>
        </div>

        <form method="POST" action="{{ route('pengurusan.audit-trail.generate') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- User Selection -->
                <div>
                    <label for="audit_user_id" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important;">
                        Pilih Pengguna
                    </label>
                    <select id="audit_user_id" name="user_id" required
                            class="form-select w-full text-sm" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ ($selectedUser && $selectedUser->id == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important;">
                        Tarikh Mula
                    </label>
                    <input type="date" id="date_from" name="date_from" required
                           value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : now()->subDays(7)->format('Y-m-d') }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="form-input w-full text-sm" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-700 mb-1" style="font-family: Poppins, sans-serif !important;">
                        Tarikh Akhir
                    </label>
                    <input type="date" id="date_to" name="date_to" required
                           value="{{ $dateTo ? $dateTo->format('Y-m-d') : now()->format('Y-m-d') }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="form-input w-full text-sm" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-colors">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">search</span>
                    Jana Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @if($auditTrails && $selectedUser)
        <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">
            <!-- Results Header -->
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                <div>
                    <h4 class="text-base font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 13px !important;">
                        Audit Trail: {{ $selectedUser->name }}
                    </h4>
                    <p class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                        {{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }} 
                        ({{ $auditTrails->total() }} rekod)
                    </p>
                </div>
                <a href="{{ route('pengurusan.audit-trail.export-pdf', ['user_id' => $selectedUser->id, 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}" 
                   class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors">
                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">picture_as_pdf</span>
                    Eksport PDF
                </a>
            </div>

            <!-- Results Table -->
            @if($auditTrails->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">
                                    Masa
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">
                                    Jenis
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">
                                    Aktiviti
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">
                                    URL
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important;">
                                    IP
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($auditTrails as $trail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                            {{ $trail->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                            {{ $trail->created_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $trail->action_type_color }}" style="font-family: Poppins, sans-serif !important;">
                                            <span class="material-symbols-outlined mr-1" style="font-size: 14px;">{{ $trail->action_type_icon }}</span>
                                            {{ $trail->action_type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                            {{ $trail->action_name }}
                                        </div>
                                        @if($trail->route_name)
                                            <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important;">
                                                {{ $trail->route_name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-gray-600 truncate max-w-xs" style="font-family: Poppins, sans-serif !important;" title="{{ $trail->url }}">
                                            {{ Str::limit($trail->url, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-900 font-mono" style="font-family: 'Courier New', monospace !important;">
                                            {{ $trail->ip_address ?? 'N/A' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $auditTrails->appends(['audit_user_id' => $selectedUser->id, 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d'), 'tab' => 'audit-trail'])->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-gray-400" style="font-size: 48px;">search_off</span>
                    <p class="text-sm text-gray-600 mt-2" style="font-family: Poppins, sans-serif !important;">
                        Tiada rekod audit trail untuk pengguna dan tarikh yang dipilih
                    </p>
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gray-50 rounded-sm border border-gray-200 p-8 text-center">
            <span class="material-symbols-outlined text-gray-400" style="font-size: 48px;">policy</span>
            <h4 class="text-sm font-medium text-gray-900 mt-4" style="font-family: Poppins, sans-serif !important;">
                Pilih Pengguna untuk Menjana Laporan
            </h4>
            <p class="text-xs text-gray-500 mt-2" style="font-family: Poppins, sans-serif !important;">
                Audit trail merekodkan setiap aktiviti pengguna termasuk lawatan halaman, klik butang, dan penghantaran borang.
            </p>
        </div>
    @endif
</div>

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout 
    title="Log Keselamatan"
    description="Log aktiviti keselamatan pengguna"
    >
        <x-ui.container class="w-full">
            <section>
                <header class="mb-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Log Keselamatan') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Log aktiviti keselamatan untuk <strong>{{ $pengguna->name }}</strong> ({{ $pengguna->email }})
                            </p>
                        </div>
                        <a href="{{ route('pengurusan.show-pengguna', $pengguna) }}">
                            <x-buttons.secondary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                                Kembali
                            </x-buttons.secondary-button>
                        </a>
                    </div>
                </header>

                <!-- Security Logs Table -->
                <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                    @if($logs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tarikh & Masa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tindakan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Keterangan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IP Address
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($logs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                                {{ formatTarikhMasa($log->created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $actionBadges = [
                                                        'admin_reset_2fa' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Reset 2FA'],
                                                        'admin_force_logout' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Force Logout'],
                                                        'admin_reset_password' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Reset Password'],
                                                        'admin_lock_account' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Lock Account'],
                                                        'admin_unlock_account' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Unlock Account'],
                                                        'login_success' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Login Success'],
                                                        'login_failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Login Failed'],
                                                        'login_blocked' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Login Blocked'],
                                                        'password_changed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Password Changed'],
                                                        '2fa_enabled' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => '2FA Enabled'],
                                                        '2fa_disabled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => '2FA Disabled'],
                                                    ];
                                                    // Check both log_name and event fields (Spatie uses event for login events)
                                                    $eventKey = $log->event ?? $log->log_name;
                                                    $badge = $actionBadges[$eventKey] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $eventKey ?? 'Unknown'];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                    {{ $badge['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-gray-900">
                                                {{ $log->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                {{ $log->properties['ip'] ?? $log->properties['ip_address'] ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 border-t border-gray-200">
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-gray-400 mb-2" style="font-size: 48px;">history</span>
                            <p class="text-sm text-gray-500">Tiada log keselamatan dijumpai</p>
                        </div>
                    @endif
                </div>
            </section>
        </x-ui.container>
</x-dashboard-layout>

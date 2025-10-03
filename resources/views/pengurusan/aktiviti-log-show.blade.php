<x-dashboard-layout title="Butiran Aktiviti">
    <x-ui.page-header
        title="Butiran Aktiviti"
        description="Maklumat lengkap log aktiviti pengguna"
    >
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('pengurusan.aktiviti-log') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                ← Kembali
            </a>
        </div>

        <!-- Activity Details -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Maklumat Aktiviti -->
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                    Maklumat Aktiviti
                </h3>
            </div>

            <div class="border-t border-gray-200">
                <dl>
                    <!-- Pengguna -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Pengguna
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            @if($activity->causer)
                                <div class="font-medium">{{ $activity->causer->name }}</div>
                                <div class="text-gray-500">{{ $activity->causer->email }}</div>
                            @else
                                <span class="text-gray-400">Sistem</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Aktiviti -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Aktiviti
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            <div class="font-medium">{{ ucfirst($activity->description) }}</div>
                            @if($activity->event)
                                <div class="text-gray-500">({{ ucfirst($activity->event) }})</div>
                            @endif
                        </dd>
                    </div>

                    <!-- Model -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Model
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            @if($activity->subject_type)
                                <div class="font-medium">{{ class_basename($activity->subject_type) }}</div>
                                <div class="text-gray-500">ID: {{ $activity->subject_id }}</div>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Masa -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Masa
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            <div class="font-medium">{{ $activity->created_at->format('d M Y, H:i:s') }}</div>
                            <div class="text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                        </dd>
                    </div>

                    <!-- IP Address -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            IP Address
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono" style="font-family: 'Courier New', monospace !important; font-size: 12px !important;">
                            {{ $activity->properties['ip'] ?? 'N/A' }}
                        </dd>
                    </div>

                    <!-- User Agent -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            User Agent
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono break-all" style="font-family: 'Courier New', monospace !important; font-size: 11px !important;">
                            {{ $activity->properties['user_agent'] ?? 'N/A' }}
                        </dd>
                    </div>

                    <!-- Log Name -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Log Name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $activity->log_name ?? 'N/A' }}
                        </dd>
                    </div>

                    <!-- Batch UUID (if any) -->
                    @if($activity->batch_uuid)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                Batch UUID
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono" style="font-family: 'Courier New', monospace !important; font-size: 11px !important;">
                                {{ $activity->batch_uuid }}
                            </dd>
                        </div>
                    @endif

                    <!-- Properties (JSON) -->
                    @if($activity->properties && count($activity->properties) > 0)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                Properties (Raw)
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <pre class="bg-gray-100 p-3 rounded border border-gray-300 text-xs overflow-x-auto font-mono" style="font-family: 'Courier New', monospace !important; font-size: 11px !important;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>


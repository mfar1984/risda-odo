@props([
    'headers' => [],
    'data' => [],
    'emptyMessage' => 'Tiada data dijumpai.',
    'actions' => true
])

<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3 {{ $header['align'] ?? 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        {{ $header['label'] }}
                    </th>
                @endforeach
                @if($actions)
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        Tindakan
                    </th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>

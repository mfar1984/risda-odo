@props([
    'inputLat' => 'lokasi_lat',
    'inputLong' => 'lokasi_long',
    'inputAddress' => null,
    'latitude' => null,
    'longitude' => null,
    'provider' => 'maptiler',
    'apiKey' => null,
    'styleUrl' => null,
    'height' => '320px',
    'defaultLatitude' => 3.1390,
    'defaultLongitude' => 101.6869,
])

@php
    $mapId = 'map-picker-' . uniqid();
    $tetapan = \App\Models\TetapanUmum::getForCurrentUser();
    $lat = old($inputLat, $latitude ?? $tetapan->getDefaultLatitude());
    $lng = old($inputLong, $longitude ?? $tetapan->getDefaultLongitude());
    $providerValue = $provider ?: 'maptiler';
    $maskedStyleUrl = $styleUrl ? preg_replace('/\?.*/', '', $styleUrl) : '';
@endphp

<div class="space-y-4" x-data="locationPicker('{{ $mapId }}', '{{ addslashes($providerValue) }}', '{{ addslashes($apiKey ?? '') }}', '{{ addslashes($maskedStyleUrl) }}', {{ json_encode([
        'lat' => $lat,
        'lng' => $lng,
        'defaultLat' => $tetapan->getDefaultLatitude(),
        'defaultLng' => $tetapan->getDefaultLongitude(),
    ]) }})" x-init="init()">
    <div class="flex flex-col md:flex-row md:items-end md:gap-3 gap-3">
        <div class="w-full md:w-48">
            <x-forms.input-label value="Jenis Peta" />
            <select class="form-select mt-1 block w-full" x-on:change="setLayerFromSelect($event.target.value)">
                <template x-for="option in layerOptions" :key="option.value">
                    <option :value="option.value" x-text="option.label" :selected="option.value === activeLayer"></option>
                </template>
            </select>
        </div>

        <div class="flex-1 relative">
            <x-forms.input-label value="Alamat" />
            <div class="flex gap-3">
                <x-forms.text-input
                    type="text"
                    class="form-input mt-1 block w-full"
                    placeholder="Masukkan alamat (cth: Jalan Abang Barieng, Sibu)"
                    x-model="searchQuery"
                    x-on:keyup.enter.prevent="searchAddress()"
                />
                <x-buttons.secondary-button type="button" class="mt-1" x-on:click="searchAddress()" x-bind:disabled="isSearching">
                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">search</span>
                    Cari
                </x-buttons.secondary-button>
            </div>
            <p class="mt-1 text-xs text-gray-500" x-show="isSearching">Mencari alamat...</p>
            <p class="mt-1 text-xs text-red-600" x-text="searchError" x-show="searchError"></p>
            <div
                class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded shadow-md max-h-48 overflow-auto"
                style="z-index: 1050;"
                x-show="searchResults.length"
                x-transition
            >
                <template x-for="result in searchResults" :key="result.id">
                    <button
                        type="button"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50"
                        x-text="result.label"
                        x-on:click="selectSearchResult(result)"
                    ></button>
                </template>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-buttons.primary-button type="button" x-on:click="goToCurrentLocation()">
                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">my_location</span>
                Lokasi Semasa
            </x-buttons.primary-button>

            <x-buttons.secondary-button type="button" x-on:click="resetToDefault()">
                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">home</span>
                Lokasi Default
            </x-buttons.secondary-button>
        </div>
    </div>

    <div id="{{ $mapId }}" class="w-full rounded-lg border border-gray-200" style="height: {{ $height }}"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-forms.input-label for="{{ $inputLat }}" value="Latitude" />
            <x-forms.text-input
                id="{{ $inputLat }}"
                name="{{ $inputLat }}"
                type="text"
                class="mt-1 block w-full"
                x-model="coords.lat"
                readonly
            />
        </div>
        <div>
            <x-forms.input-label for="{{ $inputLong }}" value="Longitude" />
            <x-forms.text-input
                id="{{ $inputLong }}"
                name="{{ $inputLong }}"
                type="text"
                class="mt-1 block w-full"
                x-model="coords.lng"
                readonly
            />
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            /* Ensure map container stays below header and modals */
            .leaflet-container {
                z-index: 1 !important;
            }

            /* Leaflet controls should be above the map but below header */
            .leaflet-control-container {
                z-index: 10 !important;
            }

            /* Leaflet popup/tooltip should be visible but below header */
            .leaflet-popup,
            .leaflet-tooltip {
                z-index: 100 !important;
            }

            .leaflet-marker-dot {
                background: #2563eb;
                border: 2px solid #ffffff;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
                position: relative;
            }

            .leaflet-marker-dot::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 0;
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                border-top: 8px solid #2563eb;
            }
        </style>
    @endpush
@endonce


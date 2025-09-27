@props([
    'action' => '',
    'searchPlaceholder' => 'Masukkan kata kunci',
    'searchValue' => '',
    'searchName' => 'search',
    'filters' => [],
    'resetUrl' => '',
    'searchLabel' => 'Cari',
    'resetLabel' => 'Reset'
])

<div class="mb-6">
    <form method="GET" action="{{ $action }}">
        <div class="flex items-end gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <x-forms.text-input
                    id="{{ $searchName }}"
                    name="{{ $searchName }}"
                    type="text"
                    class="block w-full h-9"
                    value="{{ $searchValue }}"
                    placeholder="{{ $searchPlaceholder }}"
                    style="height: 36px; min-height: 36px;"
                />
            </div>

            <!-- Dynamic Filters -->
            @foreach($filters as $filter)
                <div class="w-48">
                    @if($filter['type'] === 'select')
                        <select
                            id="{{ $filter['name'] }}"
                            name="{{ $filter['name'] }}"
                            class="form-select h-9"
                            style="height: 36px; min-height: 36px;"
                        >
                            <option value="">{{ $filter['placeholder'] ?? 'Semua' }}</option>
                            @foreach($filter['options'] as $value => $label)
                                <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filter['type'] === 'text')
                        <x-forms.text-input
                            id="{{ $filter['name'] }}"
                            name="{{ $filter['name'] }}"
                            type="text"
                            class="block w-full h-9"
                            value="{{ request($filter['name']) }}"
                            placeholder="{{ $filter['placeholder'] ?? '' }}"
                            style="height: 36px; min-height: 36px;"
                        />
                    @elseif($filter['type'] === 'date')
                        <input
                            id="{{ $filter['name'] }}"
                            name="{{ $filter['name'] }}"
                            type="date"
                            class="form-input h-9"
                            value="{{ request($filter['name']) }}"
                            placeholder="{{ $filter['placeholder'] ?? 'Pilih Tarikh' }}"
                            style="height: 36px; min-height: 36px;"
                        />
                    @endif
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="flex space-x-3">
                <x-buttons.primary-button type="submit">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">search</span>
                    {{ $searchLabel }}
                </x-buttons.primary-button>
                <a href="{{ $resetUrl }}">
                    <x-buttons.danger-button type="button">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">refresh</span>
                        {{ $resetLabel }}
                    </x-buttons.danger-button>
                </a>
            </div>
        </div>
    </form>
</div>

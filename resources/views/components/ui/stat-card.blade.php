@props([
    'icon' => 'insights',
    'iconColor' => 'text-blue-600',
    'value' => '0',
    'label' => '',
    'prefix' => '',
    'suffix' => '',
])

<div class="bg-white rounded-xl shadow-lg p-5 h-32 w-full flex flex-col items-center justify-center text-center transition-transform transition-shadow duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl">
    <div class="mb-2 h-10 flex items-center justify-center">
        <span class="material-symbols-outlined {{ $iconColor }} text-3xl leading-none">{{ $icon }}</span>
    </div>
    <div class="text-lg font-semibold text-gray-900 leading-tight">
        {{ $prefix }}{{ $value }}{{ $suffix }}
    </div>
    <div class="text-xs text-gray-500 font-semibold mt-1 tracking-wide uppercase leading-tight">
        {{ $label }}
    </div>
</div>

@props([
    'padding' => 'default', // default, sm, lg, none
    'background' => 'white', // white, gray-50, gray-100, transparent
    'border' => true, // true, false
    'shadow' => true, // true, false, md, lg
    'rounded' => true, // true, false, sm, md, lg
])

@php
// Padding classes
$paddingClasses = [
    'none' => '',
    'sm' => 'p-3 sm:p-4',
    'default' => 'p-4 sm:p-8',
    'lg' => 'p-6 sm:p-12',
][$padding];

// Background classes
$backgroundClasses = [
    'white' => 'bg-white',
    'gray-50' => 'bg-gray-50',
    'gray-100' => 'bg-gray-100',
    'transparent' => 'bg-transparent',
][$background];

// Border classes
$borderClasses = $border === true ? 'border border-gray-200' : ($border === false ? '' : "border {$border}");

// Shadow classes
$shadowClasses = match($shadow) {
    true => 'shadow',
    false => '',
    'md' => 'shadow-md',
    'lg' => 'shadow-lg',
    default => "shadow-{$shadow}"
};

// Rounded classes
$roundedClasses = match($rounded) {
    true => 'sm:rounded-lg',
    false => '',
    'sm' => 'sm:rounded',
    'md' => 'sm:rounded-md',
    'lg' => 'sm:rounded-lg',
    default => "sm:rounded-{$rounded}"
};

// Combine all classes
$containerClasses = trim("{$paddingClasses} {$backgroundClasses} {$borderClasses} {$shadowClasses} {$roundedClasses}");
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    {{ $slot }}
</div>

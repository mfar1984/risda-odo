@props(['active'])

@php
    $classes = $active
        ? 'inline-flex items-center border-b-2 border-blue-500 text-sm font-medium leading-5 text-blue-600 focus:outline-none focus:border-blue-700 transition'
        : 'inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 hover:text-blue-600 hover:border-blue-300 focus:outline-none focus:text-blue-600 focus:border-blue-300 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

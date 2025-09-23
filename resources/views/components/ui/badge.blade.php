@props(['variant' => 'default', 'size' => 'md'])

@php
$variantClasses = match($variant) {
    'primary' => 'badge-primary',
    'secondary' => 'badge-secondary',
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'danger' => 'badge-danger',
    'info' => 'badge-info',
    default => 'badge-default',
};

$sizeClasses = match($size) {
    'sm' => 'badge-sm',
    'lg' => 'badge-lg',
    default => 'badge-md',
};
@endphp

<span {{ $attributes->merge(['class' => "badge {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>

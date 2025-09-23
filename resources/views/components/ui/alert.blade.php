@props(['type' => 'info', 'dismissible' => false, 'title' => ''])

@php
$alertClasses = match($type) {
    'success' => 'alert-success',
    'warning' => 'alert-warning',
    'danger' => 'alert-danger',
    'info' => 'alert-info',
    default => 'alert-info',
};

$iconPath = match($type) {
    'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
    'danger' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
};
@endphp

<div {{ $attributes->merge(['class' => "alert {$alertClasses}"]) }} 
     @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    <div class="alert-content">
        <div class="alert-icon">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
            </svg>
        </div>
        
        <div class="alert-body">
            @if($title)
                <h4 class="alert-title">{{ $title }}</h4>
            @endif
            <div class="alert-message">
                {{ $slot }}
            </div>
        </div>
        
        @if($dismissible)
            <button @click="show = false" class="alert-dismiss">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>
</div>

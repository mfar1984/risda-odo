@props(['type' => 'success', 'message' => null, 'duration' => 5000])

@php
$toastClasses = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
];

$iconMap = [
    'success' => 'check_circle',
    'error' => 'error',
    'warning' => 'warning',
    'info' => 'info',
];

$iconColorClasses = [
    'success' => 'text-green-600',
    'error' => 'text-red-600',
    'warning' => 'text-yellow-600',
    'info' => 'text-blue-600',
];
@endphp

@php
$sessionMessage = null;
$sessionType = $type;

if (session('success')) {
    $sessionMessage = session('success');
    $sessionType = 'success';
} elseif (session('error')) {
    $sessionMessage = session('error');
    $sessionType = 'error';
} elseif (session('warning')) {
    $sessionMessage = session('warning');
    $sessionType = 'warning';
} elseif (session('info')) {
    $sessionMessage = session('info');
    $sessionType = 'info';
}

$finalMessage = $message ?? $sessionMessage;
@endphp

@if($finalMessage)
<div
    x-data="{
        show: false,
        message: '{{ $finalMessage }}',
        type: '{{ $sessionType }}',
        init() {
            this.show = true;
            setTimeout(() => {
                this.show = false;
            }, {{ $duration }});
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-full"
    :class="'toast-notification ' + (type === 'success' ? '{{ $toastClasses['success'] }}' : type === 'error' ? '{{ $toastClasses['error'] }}' : type === 'warning' ? '{{ $toastClasses['warning'] }}' : '{{ $toastClasses['info'] }}')"
    style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;"
>
    <div class="flex items-center p-3 rounded-md border shadow-lg">
        <div class="flex-shrink-0 mr-3">
            <span class="material-symbols-outlined"
                  :class="type === 'success' ? '{{ $iconColorClasses['success'] }}' : type === 'error' ? '{{ $iconColorClasses['error'] }}' : type === 'warning' ? '{{ $iconColorClasses['warning'] }}' : '{{ $iconColorClasses['info'] }}'"
                  style="font-size: 18px !important; font-weight: 500 !important;"
                  x-text="type === 'success' ? '{{ $iconMap['success'] }}' : type === 'error' ? '{{ $iconMap['error'] }}' : type === 'warning' ? '{{ $iconMap['warning'] }}' : '{{ $iconMap['info'] }}'">
            </span>
        </div>
        
        <div class="flex-1">
            <p class="toast-message" 
               style="font-family: Poppins, sans-serif !important; font-size: 11px !important; font-weight: 500 !important; margin: 0; line-height: 1.4;">
                <span x-text="message"></span>
            </p>
        </div>
        
        <button
            @click="show = false"
            class="flex-shrink-0 ml-3 hover:opacity-70 transition-opacity duration-200"
            :class="type === 'success' ? '{{ $iconColorClasses['success'] }}' : type === 'error' ? '{{ $iconColorClasses['error'] }}' : type === 'warning' ? '{{ $iconColorClasses['warning'] }}' : '{{ $iconColorClasses['info'] }}'"
            style="background: none; border: none; cursor: pointer; padding: 2px;"
        >
            <span class="material-symbols-outlined" style="font-size: 16px !important; font-weight: 500 !important;">close</span>
        </button>
    </div>
</div>
@endif

@props(['message' => null, 'dismissible' => true])

@if($message || session('warning'))
<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="warning-alert"
>
    <div class="warning-alert-content">
        <div class="warning-alert-icon">
            <span class="material-symbols-outlined">warning</span>
        </div>
        
        <div class="warning-alert-message">
            {{ $message ?? session('warning') }}
        </div>
        
        @if($dismissible)
        <button 
            @click="show = false" 
            class="warning-alert-close"
            type="button"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
        @endif
    </div>
</div>

@if($dismissible)
<script>
    // Auto-dismiss after 6 seconds
    setTimeout(() => {
        const alert = document.querySelector('.warning-alert [x-data*="show: true"]');
        if (alert && alert.__x) {
            alert.__x.$data.show = false;
        }
    }, 6000);
</script>
@endif
@endif

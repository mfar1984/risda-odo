@props(['message' => null, 'dismissible' => true])

@if($message || session('success'))
<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="success-alert"
>
    <div class="success-alert-content">
        <div class="success-alert-icon">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        
        <div class="success-alert-message">
            {{ $message ?? session('success') }}
        </div>
        
        @if($dismissible)
        <button 
            @click="show = false" 
            class="success-alert-close"
            type="button"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
        @endif
    </div>
</div>

@if($dismissible)
<script>
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('[x-data*="show: true"]');
        if (alert && alert.__x) {
            alert.__x.$data.show = false;
        }
    }, 5000);
</script>
@endif
@endif

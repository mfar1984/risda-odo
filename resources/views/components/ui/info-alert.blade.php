@props(['message' => null, 'dismissible' => true])

@if($message || session('info'))
<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="info-alert"
>
    <div class="info-alert-content">
        <div class="info-alert-icon">
            <span class="material-symbols-outlined">info</span>
        </div>
        
        <div class="info-alert-message">
            {{ $message ?? session('info') }}
        </div>
        
        @if($dismissible)
        <button 
            @click="show = false" 
            class="info-alert-close"
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
        const alert = document.querySelector('.info-alert [x-data*="show: true"]');
        if (alert && alert.__x) {
            alert.__x.$data.show = false;
        }
    }, 5000);
</script>
@endif
@endif

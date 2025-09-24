@props(['message' => null, 'dismissible' => true])

@if($message || session('error') || $errors->any())
<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="error-alert"
>
    <div class="error-alert-content">
        <div class="error-alert-icon">
            <span class="material-symbols-outlined">error</span>
        </div>
        
        <div class="error-alert-message">
            @if($message)
                {{ $message }}
            @elseif(session('error'))
                {{ session('error') }}
            @elseif($errors->any())
                @if($errors->count() == 1)
                    {{ $errors->first() }}
                @else
                    <ul style="margin: 0; padding-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
        
        @if($dismissible)
        <button 
            @click="show = false" 
            class="error-alert-close"
            type="button"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
        @endif
    </div>
</div>

@if($dismissible)
<script>
    // Auto-dismiss after 7 seconds (longer for errors)
    setTimeout(() => {
        const alert = document.querySelector('.error-alert [x-data*="show: true"]');
        if (alert && alert.__x) {
            alert.__x.$data.show = false;
        }
    }, 7000);
</script>
@endif
@endif

@props(['title' => '', 'subtitle' => '', 'actions' => false])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $subtitle || $actions)
        <div class="card-header">
            <div class="card-header-content">
                @if($title)
                    <h3 class="card-title">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="card-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="card-actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
</div>

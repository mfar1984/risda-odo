@props([
    'title' => '',
    'description' => '',
    'class' => ''
])

<div class="dashboard-stat-card {{ $class }}" style="padding-top: 1px;">
    <div style="position: relative; margin-bottom: 16px; padding-top: 12px; padding-bottom: 12px;">
        <h1 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ $title }}</h1>
        @if($description)
            <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ $description }}</p>
        @endif
        <!-- Full width separator line -->
        <div style="position: absolute; bottom: 0; left: -24px; right: -24px; height: 1px; background-color: #e5e7eb;"></div>
    </div>
    
    @if($slot->isNotEmpty())
        {{ $slot }}
    @endif
</div>

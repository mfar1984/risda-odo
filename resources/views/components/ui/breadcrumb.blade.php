@props(['route' => null])

@php
    use App\Services\BreadcrumbService;
    
    $currentRoute = $route ?? request()->route()->getName();
    $breadcrumbs = BreadcrumbService::generate($currentRoute);
@endphp

<div class="breadcrumb-container">
    @foreach($breadcrumbs as $index => $breadcrumb)
        @if($breadcrumb['type'] === 'home')
            <!-- Home icon -->
            <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-home">
                <span class="material-symbols-outlined">home_app_logo</span>
            </a>
        @elseif($breadcrumb['type'] === 'link')
            <!-- Separator -->
            <span class="breadcrumb-separator">></span>
            <!-- Link -->
            <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['name'] }}</a>
        @elseif($breadcrumb['type'] === 'current')
            <!-- Separator -->
            <span class="breadcrumb-separator">></span>
            <!-- Current page -->
            <span class="breadcrumb-current">{{ $breadcrumb['name'] }}</span>
        @endif
    @endforeach
</div>

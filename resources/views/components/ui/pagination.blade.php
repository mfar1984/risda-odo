@props([
    'paginator',
    'recordLabel' => 'rekod',
    'showSummary' => true,
])

@php
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator as SimplePaginator;

    $isLengthAware = $paginator instanceof LengthAwarePaginator;
    $isSimple = $paginator instanceof SimplePaginator;
@endphp

@if($isLengthAware || $isSimple)
    @php
        $hasPages = $paginator->hasPages();
        $shouldShowSummary = $showSummary && ($isLengthAware ? ($paginator->total() ?? 0) > 0 : true);
        $shouldRenderWrapper = $hasPages || $shouldShowSummary;
    @endphp

    @if($shouldRenderWrapper)
        <div class="mt-6 flex flex-col items-center space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between" style="font-family: Poppins, sans-serif !important;">
        @if($shouldShowSummary)
            <div class="text-sm text-gray-500" style="font-size: 12px !important;">
                @if($isLengthAware)
                    Menunjukkan {{ $paginator->firstItem() ?? 0 }} hingga {{ $paginator->lastItem() ?? 0 }} daripada {{ $paginator->total() ?? 0 }} {{ $recordLabel }}
                @else
                    Halaman {{ $paginator->currentPage() }}
                @endif
            </div>
        @endif

        @if($hasPages)
            <div class="flex justify-center sm:justify-end">
                @if($isLengthAware)
                    {{ $paginator->links('pagination::risda') }}
                @else
                    {{ $paginator->links('pagination::risda-simple') }}
                @endif
            </div>
        @endif
        </div>
    @endif
@endif

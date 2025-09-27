@props([
    'paginator',
    'recordLabel' => 'rekod'
])

@if($paginator && ($paginator->hasPages() || $paginator->total() > 0))
<div class="flex items-center justify-between mt-6">
    <!-- Record Count (Left) -->
    <div class="text-sm text-gray-700" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
        Menunjukkan
        <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
        hingga
        <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
        daripada
        <span class="font-medium">{{ $paginator->total() ?? 0 }}</span>
        {{ $recordLabel }}
    </div>

    <!-- Pagination (Right) -->
    <div>
        @if($paginator->hasPages())
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                        <span class="material-symbols-outlined" style="font-size: 16px;">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="material-symbols-outlined" style="font-size: 16px;">chevron_left</span>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="material-symbols-outlined" style="font-size: 16px;">chevron_right</span>
                    </a>
                @else
                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                        <span class="material-symbols-outlined" style="font-size: 16px;">chevron_right</span>
                    </span>
                @endif
            </nav>
        @endif
    </div>
</div>
@endif

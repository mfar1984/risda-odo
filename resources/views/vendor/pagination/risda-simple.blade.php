@if ($paginator->hasPages())
    <nav class="flex items-center justify-end space-x-2" role="navigation" aria-label="Pagination" style="font-family: Poppins, sans-serif !important;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">
                <span class="material-symbols-outlined text-base">chevron_left</span>
            </a>
        @endif

        <span class="px-3 py-1 text-[11px] text-gray-500">Halaman {{ $paginator->currentPage() }}</span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">
                <span class="material-symbols-outlined text-base">chevron_right</span>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">chevron_right</span>
            </span>
        @endif
    </nav>
@endif

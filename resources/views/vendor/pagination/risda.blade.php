@if ($paginator->hasPages())
    <nav class="flex items-center justify-end space-x-2" role="navigation" aria-label="Pagination" style="font-family: Poppins, sans-serif !important;">
        {{-- First Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">keyboard_double_arrow_left</span>
            </span>
        @else
            <a href="{{ $paginator->url(1) }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600" aria-label="Halaman Pertama">
                <span class="material-symbols-outlined text-base">keyboard_double_arrow_left</span>
            </a>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600" aria-label="Halaman Sebelum">
                <span class="material-symbols-outlined text-base">chevron_left</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 py-1 text-[11px] text-gray-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-7 h-7 flex items-center justify-center text-[11px] text-white bg-blue-600 rounded-full">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600" aria-label="Halaman Seterus">
                <span class="material-symbols-outlined text-base">chevron_right</span>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">chevron_right</span>
            </span>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600" aria-label="Halaman Terakhir">
                <span class="material-symbols-outlined text-base">keyboard_double_arrow_right</span>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-base">keyboard_double_arrow_right</span>
            </span>
        @endif
    </nav>
@endif

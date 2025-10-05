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

        {{-- Smart Pagination Elements --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $start = max(1, $current - 1);
            $end = min($last, $current + 1);
        @endphp

        {{-- First Page (if not in range) --}}
        @if($start > 1)
            <a href="{{ $paginator->url(1) }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">1</a>
            @if($start > 2)
                <span class="px-2 py-1 text-[11px] text-gray-400">...</span>
            @endif
        @endif

        {{-- Page Numbers (current ± 1) --}}
        @for($page = $start; $page <= $end; $page++)
            @if ($page == $current)
                <span class="w-7 h-7 flex items-center justify-center text-[11px] text-white bg-blue-600 rounded-full">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">{{ $page }}</a>
            @endif
        @endfor

        {{-- Last Page (if not in range) --}}
        @if($end < $last)
            @if($end < $last - 1)
                <span class="px-2 py-1 text-[11px] text-gray-400">...</span>
            @endif
            <a href="{{ $paginator->url($last) }}" class="w-8 h-8 flex items-center justify-center text-[11px] text-gray-600 hover:text-blue-600">{{ $last }}</a>
        @endif

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

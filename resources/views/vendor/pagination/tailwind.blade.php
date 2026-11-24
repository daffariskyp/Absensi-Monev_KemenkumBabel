@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <ul class="flex items-center border border-gray-300 rounded-lg overflow-hidden shadow-sm">
            {{-- Previous Page Link --}}
            @if (!$paginator->onFirstPage())
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-gray-600 hover:bg-gray-200">
                        &laquo;
                    </a>
                </li>
            @endif
            
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="px-4 py-2 bg-[#6FAEC9] text-white font-bold">
                                {{ $page }}
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-4 py-2 text-gray-600 hover:bg-gray-200">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-gray-600 hover:bg-gray-200">
                        &raquo;
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif

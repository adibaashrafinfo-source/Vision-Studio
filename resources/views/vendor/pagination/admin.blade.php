@if ($paginator->hasPages())
    <div class="pg">
        @if ($paginator->onFirstPage())
            <span class="dots" style="opacity:.4">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="dots">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="on">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
        @else
            <span class="dots" style="opacity:.4">›</span>
        @endif
    </div>
@endif

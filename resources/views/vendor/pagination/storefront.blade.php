@if ($paginator->hasPages())
    <nav class="pager">
        @if ($paginator->onFirstPage())
            <button disabled aria-label="Previous" style="opacity:.4"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:currentColor"><path d="M15 6l-6 6 6 6"></path></svg></button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"><button aria-label="Previous"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:currentColor"><path d="M15 6l-6 6 6 6"></path></svg></button></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <button disabled>{{ $element }}</button>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="on">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}"><button>{{ $page }}</button></a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"><button aria-label="Next"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:currentColor"><path d="M9 6l6 6-6 6"></path></svg></button></a>
        @else
            <button disabled aria-label="Next" style="opacity:.4"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:currentColor"><path d="M9 6l6 6-6 6"></path></svg></button>
        @endif
    </nav>
@endif

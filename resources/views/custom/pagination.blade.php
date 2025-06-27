{{-- resources/views/custom/pagination.blade.php --}}
@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" role="navigation">
        <div class="d-flex justify-content-between align-items-center">
            {{-- Results info --}}
            <div>
                <p class="text-sm text-muted mb-0">
                    {!! __('Showing') !!}
                    <span class="font-weight-bold">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-weight-bold">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-weight-bold">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            {{-- Pagination Elements --}}
            <div>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-left" style="font-size: 0.75rem;"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-angle-left" style="font-size: 0.75rem;"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                                <i class="fas fa-angle-right" style="font-size: 0.75rem;"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-right" style="font-size: 0.75rem;"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
